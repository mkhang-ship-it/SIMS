<?php
// GenericMail: Mailable động cho MailService, cho phép gửi subject, body html, đính kèm file, headers
namespace App\Mail;

use Illuminate\Mail\Mailable;

class GenericMail extends Mailable
{
    public $toAddresses;
    public $subjectText;
    public $bodyHtml;
    public $attachmentsList;
    public $headersList;

    public function __construct($to, $subject, $body, $attachments = [], $headers = [])
    {
        $this->toAddresses = $to;
        $this->subjectText = $subject;
        $this->bodyHtml = $body;
        $this->attachmentsList = $attachments;
        $this->headersList = $headers;
    }

    public function build()
    {
        $mail = $this->subject($this->subjectText)
            ->html($this->bodyHtml)
            ->to($this->toAddresses); // Đảm bảo set trường to
        // Đính kèm file nếu có
        foreach ($this->attachmentsList as $att) {
            if (!empty($att['path']) && file_exists($att['path'])) {
                $mail->attach($att['path'], [
                    'as' => $att['name'] ?? basename($att['path'])
                ]);
            }
        }
        // Thêm headers nếu có
        if (!empty($this->headersList)) {
            $mail->withSwiftMessage(function ($message) {
                foreach ($this->headersList as $key => $value) {
                    $message->getHeaders()->addTextHeader($key, $value);
                }
            });
        }
        return $mail;
    }
}
