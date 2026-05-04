
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - easyShop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            /* background: linear-gradient(135deg, #7C3AED 0%, #06B6D4 100%); */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            display: flex;
        }

        .login-left {
            background: linear-gradient(135deg, #7C3AED 0%, #06B6D4 100%);
            color: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            min-height: 500px;
        }

        .login-left h1 {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .login-left p {
            font-size: 16px;
            opacity: 0.9;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .login-left .features {
            text-align: left;
            margin-top: 30px;
        }

        .login-left .features li {
            list-style: none;
            margin: 15px 0;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .login-left .features i {
            font-size: 18px;
        }

        .login-right {
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex: 1;
            min-width: 350px;
        }

        .login-right h2 {
            font-size: 32px;
            color: #333;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .login-right p {
            color: #999;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #7C3AED;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .form-group input::placeholder {
            color: #999;
        }

        .error-message {
            color: #dc2626;
            font-size: 13px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .remember-forgot a {
            color: #7C3AED;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .remember-forgot a:hover {
            color: #06B6D4;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #7C3AED 0%, #06B6D4 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(124, 58, 237, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .signup-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .signup-link a {
            color: #7C3AED;
            text-decoration: none;
            font-weight: 600;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-danger {
            background: #fee;
            color: #c33;
            border-left: 4px solid #c33;
        }

        .alert-success {
            background: #efe;
            color: #3c3;
            border-left: 4px solid #3c3;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }

            .login-left {
                min-height: 250px;
                padding: 40px 20px;
            }

            .login-left h1 {
                font-size: 36px;
            }

            .login-right {
                min-width: auto;
                padding: 40px 20px;
            }

            .login-right h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- CÔTÉ GAUCHE : PRÉSENTATION -->
        <div class="login-left">
            <div>
                <h1>easy<span style="color: #06B6D4;">Shop</span></h1>
                <p>Gestion Commerciale BTP</p>

                <p style="margin-top: 40px; font-size: 18px;">
                    Bienvenue dans votre espace de gestion commerciale
                </p>

                <ul class="features">
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Gestion des ventes</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Génération de factures</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Rapports et statistiques</span>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <span>Gestion des utilisateurs</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- CÔTÉ DROIT : FORMULAIRE -->
        <div class="login-right">
            <h2>Connexion</h2>
            <p>Entrez vos identifiants pour accéder à votre compte</p>

            <!-- Messages d'erreur globaux -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Message de succès -->
            @if (session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <!-- Email -->
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope" style="color: #7C3AED;"></i>
                        Email
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        placeholder="votre@email.com"
                        required
                    >
                    @error('email')
                        <div class="error-message">
                            <i class="fas fa-times-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Mot de passe -->
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock" style="color: #7C3AED;"></i>
                        Mot de passe
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="••••••••"
                        required
                    >
                    @error('password')
                        <div class="error-message">
                            <i class="fas fa-times-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Se souvenir de moi -->
                <div class="remember-forgot">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Se souvenir de moi</span>
                    </label>
                    <a href="#forgot-password">Mot de passe oublié ?</a>
                </div>

                <!-- Bouton Connexion -->
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Se connecter</span>
                </button>
            </form>

            <!-- Lien inscription -->
            <div class="signup-link">
                Pas encore de compte ? <a href="#signup">S'inscrire</a>
            </div>
        </div>
    </div>

</body>
</html>
