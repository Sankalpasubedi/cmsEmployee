<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registered</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
            padding: 10px;
        }
        .header {
            background-color: #127fc7;
            color: #fff;
            padding: 5px 10px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            text-align: center;
        }
        .content {
            padding: 20px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 20px;
        }
        button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #127fc7;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Reset Password</h1>
        </div>
        <div class="content">
            <h4>Click the link below to reset your password:</h4>
        <a href="{{ url('/reset/' . $token) }}"><button>Reset Password</button></a>
        <P>If above button is not working copy and paste the link below to your browser.</P>
        <p>{{ url('/reset/' . $token) }}</p>
            <p>Thank you for your attention!</p>
            <p>Best regards,<br>TravelFreeTravels Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} TravelFreeTravels. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
