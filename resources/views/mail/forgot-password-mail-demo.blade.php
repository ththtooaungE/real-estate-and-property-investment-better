<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password</title>

</head>

<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #F3F4F6;
    margin: 0;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;">
    <div style="background-color: #FFFFFF;
        max-width: 450px;
        margin: 20px;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        text-align: center;">
        <h1 style="font-size: 24px;
            color: #2C3E50;
            margin-bottom: 20px;">OTP</h1>
        <h2 style="font-size: 18px;
            color: #34495E;
            margin-bottom: 15px;">You have requested to reset your password.</h2> 
        <h3 style="letter-spacing: 30px;">{{$token}}</h3>
        <p style="font-size: 16px;
            color: #7F8C8D;
            line-height: 1.5;">This is your otp code. You can use it to reset your password. This code will expire during 10 minutes.</p>
        <br>
        <p style="font-size: 16px;
                color: #7F8C8D;
                line-height: 1.5;">If you did not request a password reset, please ignore this email.</p>
        <br>
    </div>

    <style>
        .button:hover {
            background-color: #2980B9;
        }
    </style>
</body>

</html>