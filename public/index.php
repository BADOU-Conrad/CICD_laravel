<?php

//Standalone Laravel Hello World
echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel - Hello World</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .container {
            text-align: center;
            color: white;
        }
        h1 {
            font-size: 4rem;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        p {
            font-size: 1.5rem;
            margin-top: 1rem;
            opacity: 0.9;
        }
        .laravel-version {
            margin-top: 2rem;
            font-size: 1rem;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Hello World</h1>
        <p>Bienvenue dans Laravel</p>
        <div class="laravel-version">PHP ' . phpversion() . '</div>
    </div>
</body>
</html>';

