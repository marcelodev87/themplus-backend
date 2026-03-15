<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Great+Vibes&family=Lora:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        @page { size: landscape; margin: 0; }

        body {
            margin: 0;
            padding: 0;
            background-color: #fdfcf7;
            color: #333;
            font-family: 'Lora', serif;
            border: 20px solid #1f3b73;
            outline: 5px solid #c5a059;
            outline-offset: -15px;
            max-width: 96.5%;
            text-align: center;
        }

        .certificate-container {
            margin-top: 100px;
        }

        .title {
            font-family: 'Cinzel', serif;
            font-size: 52px;
            color: #1f3b73;
            margin: 20px 0px 10px 0px;
            letter-spacing: 4px;
        }

        .subtitle {
            font-size: 20px;
            font-style: italic;
            margin-top: 20px;
            color: #555;
        }

        .name {
            font-family: 'Great Vibes', cursive;
            font-size: 50px;
            color: #1f3b73;
            margin: 20px 0;
            display: block;
            border-bottom: 1px solid #ddd;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
        }

        .main-text {
            font-size: 20px;
            line-height: 1.8;
            margin: 30px auto;
            width: 80%;
        }

        .scripture {
            font-weight: bold;
            color: #1f3b73;
        }

        .date-location {
            font-size: 18px;
            margin-bottom: 50px;
        }

        .signatures {
            margin-top: 50px;
        }

        .signature-block {
            width: 350px;
            margin: 0 auto;
        }

        .line {
            border-top: 1px solid #333;
            margin-bottom: 5px;
        }

        .label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #777;
        }

        .church-name {
            font-weight: bold;
            font-size: 14px;
            color: #1f3b73;
        }

        .verse-footer {
            margin-top: 40px;
            font-size: 15px;
            font-style: italic;
            color: #c5a059;
            border-top: 1px double #ddd;
            padding-top: 15px;
            width: 60%;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="title">Certificado de Batismo</div>
        <div class="subtitle">Certificamos que</div>
        <div class="name">
            {{ $member->name }}
        </div>
        <div class="main-text">
            foi batizado(a) nas águas em nome do Pai, do Filho e do Espírito Santo,
            conforme os mandamentos do Senhor Jesus Cristo descritos em <span class="scripture">Mateus 28:19</span>.
        </div>
        <div class="date-location">
            Realizado no dia <strong>{{ $member->date_baptismo }}</strong>
        </div>
        <div class="signatures">
            <div class="signature-block">
                <div class="line"></div>
                <div class="church-name">{{ $member->enterprise->name }}</div>
                <div class="label">Igreja</div>
            </div>
        </div>
        <div class="verse-footer">
            "Quem crer e for batizado será salvo..." <br> <strong>Marcos 16:16</strong>
        </div>
    </div>
</body>
</html>
