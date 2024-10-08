<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password</title>
</head>
<body>
    <h1 style="text-align: center; font-family: Arial, sans-serif; color: #2C3E50;">Reset Password</h1>
    <form action="http://127.0.0.1:8000/api/v1/auth/reset-password" method="POST" class="card">
        @method('put')
        <input name="token" type="text" class="input" placeholder="Token">

        <input name="email" type="email" class="input" placeholder="Email">

        <input name="password" type="password" class="input" placeholder="Password">

        <input name="password_confirmation" type="password" class="input" placeholder="Password Confirmation">

        <input type="submit" value="Submit" class="button">
    </form>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ECF0F1;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .card {
            background-color: #FFFFFF;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            width: 350px;
            padding: 20px;
            border-radius: 10px;
        }
        .input {
            margin: 10px 0;
            padding: 12px;
            background-color: #F8F9FA;
            border-radius: 5px;
            border: 1px solid #DADFE1;
            font-size: 14px;
            width: calc(100% - 24px);
        }
        .button {
            margin-top: 20px;
            background-color: #1ABC9C;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 16px;
        }
        .button:hover {
            background-color: #16A085;
        }
    </style>
</body>

</html>