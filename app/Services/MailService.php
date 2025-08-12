<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Kiota\Authentication\Oauth\ClientCredentialContext;
use Microsoft\Graph\Generated\Models\Message;
use Microsoft\Graph\Generated\Models\ItemBody;
use Microsoft\Graph\Generated\Models\BodyType;
use Microsoft\Graph\Generated\Models\Recipient;
use Microsoft\Graph\Generated\Models\EmailAddress;
use Microsoft\Graph\Generated\Users\Item\SendMail\SendMailPostRequestBody;
use GuzzleHttp\Client;
use Microsoft\Graph\Generated\Models\FileAttachment;
use GuzzleHttp\Psr7\Utils;

class MailService
{
    /**
     * Gửi mail cho club, tự động chọn phương thức phù hợp
     * @param int|string $clubId
     * @param string|array $to
     * @param string $subject
     * @param string $body (html)
     * @param array $attachments (['path' => ..., 'name' => ...])
     * @param array $headers (tuỳ chọn)
     * @return bool|string (true nếu thành công, string lỗi nếu thất bại)
     */
    public static function sendClubMail($clubId, $to, $subject, $body, $attachments = [], $headers = [])
    {
        $settings = Setting::getMany($clubId, [
            'mail_mailer',
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_encryption',
            'mail_from_address',
            'mail_from_name',
            'mail_outlook_client_id',
            'mail_outlook_secret_id',
            'mail_outlook_tenant_id',
        ]);

        $mailer = $settings['mail_mailer'] ?? 'smtp';

        // Log cấu hình mail
        Log::info('[MailService] Cấu hình mail', [
            'club_id' => $clubId,
            'mailer' => $mailer,
            'from_address' => $settings['mail_from_address'] ?? 'N/A',
            'host' => $settings['mail_host'] ?? 'N/A',
            'port' => $settings['mail_port'] ?? 'N/A',
        ]);

        // Render nội dung qua view Blade
        try {
            $renderedBody = view('emails.base', ['content' => $body])->render();
        } catch (\Exception $e) {
            Log::error('[MailService] Lỗi render template email', [
                'error' => $e->getMessage()
            ]);
            return 'Lỗi render template email: ' . $e->getMessage();
        }

        if ($mailer === 'outlook') {
            return self::sendViaMicrosoftGraph($settings, $to, $subject, $renderedBody, $attachments, $headers);
        } else {
            return self::sendViaSmtp($settings, $to, $subject, $renderedBody, $attachments, $headers);
        }
    }

    protected static function sendViaSmtp($settings, $to, $subject, $body, $attachments = [], $headers = [])
    {
        // Kiểm tra cấu hình bắt buộc
        $requiredSettings = ['mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_from_address'];
        foreach ($requiredSettings as $key) {
            if (empty($settings[$key])) {
                $error = "Thiếu cấu hình SMTP: {$key}";
                Log::error('[MailService] ' . $error, $settings);
                return $error;
            }
        }

        // Cấu hình mail động cho club
        config([
            'mail.mailers.smtp.host' => $settings['mail_host'],
            'mail.mailers.smtp.port' => $settings['mail_port'],
            'mail.mailers.smtp.username' => $settings['mail_username'],
            'mail.mailers.smtp.password' => $settings['mail_password'],
            'mail.mailers.smtp.encryption' => $settings['mail_encryption'] ?? 'tls',
            'mail.from.address' => $settings['mail_from_address'],
            'mail.from.name' => $settings['mail_from_name'] ?? $settings['mail_from_address'],
            'mail.default' => 'smtp',
        ]);

        try {
            // Kiểm tra attachments trước khi gửi
            $validAttachments = [];
            foreach ($attachments as $attachment) {
                if (!empty($attachment['path']) && file_exists($attachment['path'])) {
                    $validAttachments[] = $attachment;
                    Log::info('[MailService] Attachment hợp lệ', [
                        'path' => $attachment['path'],
                        'size' => filesize($attachment['path']) . ' bytes'
                    ]);
                } else {
                    Log::warning('[MailService] Attachment không hợp lệ', [
                        'path' => $attachment['path'] ?? 'N/A'
                    ]);
                }
            }

            Log::info('[MailService] Bắt đầu gửi SMTP mail', [
                'to' => $to,
                'subject' => $subject,
                'attachments_count' => count($validAttachments)
            ]);

            $mail = Mail::mailer('smtp')->send(new \App\Mail\GenericMail($to, $subject, $body, $validAttachments, $headers));

            Log::info('[MailService] Gửi SMTP mail thành công', [
                'to' => $to
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('[MailService] Lỗi gửi SMTP mail', [
                'to' => $to,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $e->getMessage();
        }
    }

    protected static function sendViaMicrosoftGraph($settings, $to, $subject, $body, $attachments = [], $headers = [])
    {
        $clientId = $settings['mail_outlook_client_id'] ?? '';
        $clientSecret = $settings['mail_outlook_secret_id'] ?? '';
        $tenantId = $settings['mail_outlook_tenant_id'] ?? '';
        $fromAddress = $settings['mail_from_address'] ?? '';
        $fromName = $settings['mail_from_name'] ?? '';

        if (!$clientId || !$clientSecret || !$tenantId || !$fromAddress) {
            $error = 'Thiếu cấu hình Microsoft Graph API';
            Log::error('[MailService] ' . $error, $settings);
            return $error;
        }

        try {
            // Lấy access token
            $guzzle = new Client();
            $url = 'https://login.microsoftonline.com/' . $tenantId . '/oauth2/v2.0/token';
            $response = $guzzle->post($url, [
                'form_params' => [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'scope' => 'https://graph.microsoft.com/.default',
                    'grant_type' => 'client_credentials',
                ],
            ]);

            $tokenData = json_decode($response->getBody()->getContents(), true);
            $accessToken = $tokenData['access_token'];

            // Khởi tạo Graph client
            $tokenRequestContext = new ClientCredentialContext($tenantId, $clientId, $clientSecret);
            $graphServiceClient = new GraphServiceClient($tokenRequestContext);

            // Tạo message
            $message = new Message();
            $message->setSubject($subject);

            $messageBody = new ItemBody();
            $messageBody->setContentType(new BodyType(BodyType::HTML));
            $messageBody->setContent($body);
            $message->setBody($messageBody);

            // Tạo danh sách người nhận
            $toRecipients = [];
            foreach ((array) $to as $email) {
                $recipient = new Recipient();
                $emailAddress = new EmailAddress();
                $emailAddress->setAddress($email);
                $recipient->setEmailAddress($emailAddress);
                $toRecipients[] = $recipient;
            }
            $message->setToRecipients($toRecipients);

            // Tạo danh sách attachments nếu có
            if (!empty($attachments)) {
                $attachmentList = [];
                foreach ($attachments as $attachment) {
                    if (file_exists($attachment['path'])) {
                        $fileAttachment = new FileAttachment();
                        $fileAttachment->setOdataType('#microsoft.graph.fileAttachment');
                        $fileAttachment->setName($attachment['name'] ?? basename($attachment['path']));

                        // Fix: Sử dụng Utils::streamFor để tạo StreamInterface từ file content
                        $fileContent = file_get_contents($attachment['path']);
                        $contentStream = Utils::streamFor(base64_encode($fileContent));
                        $fileAttachment->setContentBytes($contentStream);

                        $attachmentList[] = $fileAttachment;

                        Log::info('[MailService] Đã thêm attachment cho Microsoft Graph', [
                            'name' => $attachment['name'] ?? basename($attachment['path']),
                            'size' => strlen($fileContent) . ' bytes'
                        ]);
                    }
                }
                $message->setAttachments($attachmentList);
            }

            // Tạo sendMail request body
            $sendMailPostRequestBody = new SendMailPostRequestBody();
            $sendMailPostRequestBody->setMessage($message);
            $sendMailPostRequestBody->setSaveToSentItems(true);

            // Gửi mail
            $graphServiceClient->users()->byUserId($fromAddress)->sendMail()->post($sendMailPostRequestBody);

            Log::info('[MailService] Gửi Microsoft Graph mail thành công', [
                'to' => $to
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('[MailService] Lỗi gửi Microsoft Graph mail', [
                'to' => $to,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $e->getMessage();
        }
    }
}
