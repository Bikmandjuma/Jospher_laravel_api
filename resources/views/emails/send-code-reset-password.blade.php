<!DOCTYPE html>
<html>
<head>
    <style type="text/css">
        .container_code {
            margin: 0 auto;
            width: fit-content;
        }
        .copy-text {
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }
        .copy-text input.text {
            padding: 5px;
            font-size: 14px;
            color: #555;
            border: 1px solid #ddd;
            outline: none;
            border-radius: 5px;
            width: 200px;
            text-align: center;
        }
    </style>
</head>
<body>
    <p>We have received your request to reset your account password. Please use the following code to recover your account:</p>

    <div class="container_code">
        <div class="copy-text">
            <input type="text" class="text" value="{{ $code }}" readonly />
        </div>
    </div>

    <p>The code is valid for one hour from the time this message was sent.</p>
</body>
</html>
