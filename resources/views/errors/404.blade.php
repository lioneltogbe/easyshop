<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Non Trouvée | easyShop</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container-404 {
            text-align: center;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 60px 40px;
            max-width: 600px;
            width: 100%;
            animation: slideIn 0.6s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-code {
            font-size: 120px;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            line-height: 1;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        .error-icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: rotate 3s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .error-title {
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 15px;
        }

        .error-description {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .error-details {
            background: #f9fafb;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 8px;
            text-align: left;
        }

        .error-details h4 {
            color: #1f2937;
            font-size: 14px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .error-details p {
            color: #6b7280;
            font-size: 13px;
            font-family: 'Courier New', monospace;
            word-break: break-all;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #1f2937;
            border: 2px solid #e5e7eb;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
            border-color: #d1d5db;
        }

        .suggestions {
            background: #f0f9ff;
            border-left: 4px solid #06b6d4;
            padding: 20px;
            border-radius: 8px;
            text-align: left;
            margin-bottom: 30px;
        }

        .suggestions h4 {
            color: #0c4a6e;
            font-size: 14px;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .suggestions ul {
            list-style: none;
            padding-left: 0;
        }

        .suggestions li {
            color: #0c4a6e;
            font-size: 13px;
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .suggestions li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #06b6d4;
            font-weight: bold;
        }

        .footer-404 {
            color: #9ca3af;
            font-size: 12px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }

        .footer-404 a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .footer-404 a:hover {
            text-decoration: underline;
        }

        .logo-404 {
            font-size: 36px;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .container-404 {
                padding: 40px 20px;
            }

            .error-code {
                font-size: 80px;
            }

            .error-title {
                font-size: 24px;
            }

            .error-description {
                font-size: 14px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container-404">
        <!-- Logo -->
        <div class="logo-404"></div>

        <!-- Error Code -->
        <div class="error-code">404</div>

        <!-- Error Icon -->
        <div class="error-icon">🔍</div>

        <!-- Error Title -->
        <h1 class="error-title">Page Non Trouvée</h1>

        <!-- Error Description -->
        <p class="error-description">
            Désolé, la page que vous recherchez n'existe pas ou a été déplacée. 
            Vérifiez l'URL et réessayez.
        </p>

        <!-- Error Details -->
        <!-- <div class="error-details">
            <h4>Détails de l'erreur :</h4>
            <p>
                <strong>URL :</strong> {{ Request::url() }}<br>
                <strong>Méthode :</strong> {{ Request::method() }}<br>
                <strong>Heure :</strong> {{ now()->format('d/m/Y H:i:s') }}
            </p>
        </div> -->

        <!-- Suggestions -->
        <!-- <div class="suggestions">
            <h4>Que faire maintenant ?</h4>
            <ul>
                <li>Vérifiez l'orthographe de l'URL</li>
                <li>Retournez à la page d'accueil</li>
                <li>Consultez le menu de navigation</li>
                <li>Contactez le support si le problème persiste</li>
            </ul>
        </div> -->

        <!-- Action Buttons -->
        <div class="action-buttons">
            <!-- <a href="{{ route('dashboard') }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-2m-9-2l4 2"/>
                </svg>
                Aller au Dashboard
            </a> -->
            <button onclick="history.back()" class="btn btn-secondary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour Précédent
            </button>
        </div>

        <!-- Footer -->
        <div class="footer-404">
            © 2026 easyShop - Gestion Commerciale et finacier | 
            <a href=#>Besoin d'aide ?</a>
        </div>
    </div>
</body>
</html>
