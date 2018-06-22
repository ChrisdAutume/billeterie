<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Billet</title>

    <style>
        * {
            margin: 0;
            padding: 0 auto;
        }
        @font-face {
            font-family: 'Agency FB';
            font-style: normal;
            font-weight: 400;
            src: url({{ public_path() }}/polices/agency-fb.ttf) format('truetype');
        }

        page {
            background: white;
            display: block;
            margin: 0 auto;

            width: 800px;
            height: 566px;
        }

        page img {
            margin: 0 auto;
        }

        #nfc
        {
            position: absolute;
            display: block;
            height: 80px;
            top: 1.2cm;
            left: 17cm;

        }
        #name
        {
            position: absolute;
            width: 21cm;
            height: 24px;
            top: 6cm;
            left: 3cm;
            font-size: 35px;

            font-family: "Agency FB",Arial, "Helvetica Neue", Helvetica, sans-serif;
            text-align: center;
            color: #FFFFFF;
            font-weight: bold;
        }

        #surname
        {
            position: absolute;
            width: 21cm;
            height: 24px;
            top: 7cm;
            left: 3cm;
            font-size: 35px;

            font-family: "Agency FB",Arial, "Helvetica Neue", Helvetica, sans-serif;
            text-align: center;
            color: #FFFFFF;
            font-weight: bold;
        }

        #type
        {
            position: absolute;
            width: 21cm;
            height: 24px;
            top: 8cm;
            left: 3cm;
            font-size: 35px;

            font-family: "Agency FB",Arial, "Helvetica Neue", Helvetica, sans-serif;
            text-align: center;
            color: #FFFFFF;
            font-weight: bold;
        }

        #code
        {
            position: absolute;

            width: 100px;
            height: 120px;
            top: 1.65cm;
            left: 17.1cm;
        }

        #code img
        {
            display: block;
            height: 90px;
            width: 90px;
        }

        #code #id
        {
            display: block;
            height: 10px;
            width: 100px;
            color: #000000;
            text-align: center;
            font-size: 15px;
            margin-top: 86px;
            font-family: Consolas, Menlo, Monaco, Lucida Console, Liberation Mono, DejaVu Sans Mono, Bitstream Vera Sans Mono, Courier New, monospace, serif;
        }


    </style>
</head>
<body>
<div class="billet">
    <img width="800" src="data:{{ $billet->price->file->mime }};base64,{{ $billet->price->file->data }}" alt="">
    <div id="code">
        <img src="data:image/png;base64,{{ $billet->base64QrCode() }}" alt="">
        <p id="id">{{ $billet->getQrCodeSecurity() }}</p>
    </div>
    <div id="name">{{ strtoupper($billet->name) }}</div>
    <div id="surname">{{ ucfirst(strtolower($billet->surname)) }}</div>
    <div id="type">{{ $billet->price->name }}</div>
</div>
</body>
</html>