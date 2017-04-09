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
        .billet
        {
            display: block;
            width: 2480px;
            height: 1336px;
            background-color: black;
        }

        .billet img
        {
            display: block;
            width: 2480px;
            height: 1336px;
        }

        #name
        {
            position: absolute;
            width: 2480px;
            height: 20px;
            top: 550px;

            font-family: "Agency FB",Arial, "Helvetica Neue", Helvetica, sans-serif;
            text-align: center;
            color: #000000;
            font-size: 78px;
            font-weight: bold;
        }

        #surname
        {
            position: absolute;
            width: 2480px;
            height: 20px;
            top: 670px;

            font-family: "Agency FB",Arial, "Helvetica Neue", Helvetica, sans-serif;
            text-align: center;
            color: #000000;
            font-size: 78px;
            font-weight: bold;
        }

        #type
        {
            position: absolute;
            width: 2480px;
            height: 20px;
            top: 780px;

            font-family: "Agency FB",Arial, "Helvetica Neue", Helvetica, sans-serif;
            text-align: center;
            color: #000000;
            font-size: 78px;
            font-weight: bold;
        }

        #code
        {
            position: absolute;
            width: 430px;
            height: 430px;
            top: 507px;
            left: 1962px;
        }

        #code img
        {
            display: block;
            height: 430px;
            width: 430px;
        }


    </style>
</head>
<body>
<div class="billet">
    <img src="{{ @asset('img/billets/'.$billet->price->background) }}" alt="">
    <div id="code"><img src="data:image/png;base64,{{ $billet->base64QrCode() }}" alt=""></div>
    <div id="name">{{ strtoupper($billet->name) }}</div>
    <div id="surname">{{ ucfirst(strtolower($billet->surname)) }}</div>
    <div id="type">{{ ucfirst(strtolower($billet->price->name)) }}</div>
</div>
</body>
</html>