<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ładowanie</title>
    <style>
        body{
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }

        .squere{
            width: 100px;
            height: 100px;
            border-radius: 5% 5%;
            background: linear-gradient(to right, rgba(255, 0, 0, 0.5), rgba(0, 0, 255, 0.5));
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>

    <div class="squere"></div>

</body>
</html>
