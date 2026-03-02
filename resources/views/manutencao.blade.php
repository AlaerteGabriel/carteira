<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Estamos em manutenção</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color:#2e367c;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #fff;
        }

        p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            color: #a0aec0;
        }

        .logo img {
            height: 100px;
        }

        footer {
            margin-top: 10px;
            font-size: 0.9rem;
            color: #a0aec0;
        }
    </style>
</head>
<body>

    <div class="logo">
        <img src="{{ asset('assets/media/logo.png') }}">
    </div>

    <div class="animation">
        <lottie-player
            src="{{ asset('assets/media/manutencao.json') }}"
            background="transparent"
            speed="1"
            style="width: 500px; height: 500px;"
            loop
            autoplay>
        </lottie-player>
    </div>

    <h1>Estamos em manutenção</h1>
    <p>{!! $manu->message !!}<br>Volte em breve!</p>

    <footer>
        &copy; {{ date('Y') }}. Todos os direitos reservados.
    </footer>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

</body>
</html>
