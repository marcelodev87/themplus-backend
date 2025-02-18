<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Redefinição de Senha</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 600px;
            margin: auto;
        }

        h1 {
            color: #333333;
        }

        h2 {
            color: #007BFF;
            font-size: 24px;
            margin: 20px 0;
        }

        p {
            color: #555555;
            line-height: 1.6;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #888888;
        }

        .button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Olá {{ $name }}!</h1>
        <p>Você solicitou a redefinição de sua senha no <strong>Themplus</strong>. Para continuar com o processo de
            redefinição, por favor, utilize o código abaixo:</p>
        <h2>{{ $code }}</h2>
        <p>Se você não solicitou a redefinição da senha, pode ignorar este email.</p>
        <p>Atenciosamente,<br>A equipe do Themplus.</p>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Themplus. Todos os direitos reservados.</p>
        </div>
    </div>
</body>

</html>