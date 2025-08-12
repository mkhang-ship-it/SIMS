<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .email-container {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }

        .email-content {
            background-color: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-content">
            {!! $content !!}
        </div>
        <div class="footer">
            <p>Email được gửi từ hệ thống {{ config('app.name', 'Laravel Application') }}</p>
            <p>{{ date('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>

</html>
