<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>easyShop - Gestion Commerciale & Financière</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #1a1a1a;
            background-color: #FFFFFF;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
        }

        h1 {
            font-size: 56px;
            line-height: 1.2;
            letter-spacing: -0.5px;
        }

        h2 {
            font-size: 36px;
            line-height: 1.3;
            letter-spacing: -0.3px;
        }

        h3 {
            font-size: 28px;
            line-height: 1.4;
        }

        p {
            font-family: 'Inter', sans-serif;
            font-weight: 400;
        }

        .container {
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        @media (min-width: 640px) {
            .container {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }
        }

        @media (min-width: 1024px) {
            .container {
                padding-left: 2rem;
                padding-right: 2rem;
                max-width: 1280px;
            }
        }

        /* Navigation */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            background-color: #FFFFFF;
            transition: all 0.3s ease;
        }

        nav.scrolled {
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .nav-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-logo-icon {
            width: 32px;
            height: 32px;
            background-color: #0066CC;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .nav-logo-text {
            font-weight: bold;
            font-size: 20px;
            color: #1a1a1a;
        }

        .nav-links {
            display: none;
            gap: 32px;
            align-items: center;
        }

        @media (min-width: 768px) {
            .nav-links {
                display: flex;
            }
        }

        .nav-links a {
            color: #666666;
            text-decoration: none;
            transition: color 0.3s ease;
            font-weight: bold;
        }

        .nav-links a:hover {
            color: #0066CC;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .btn-primary {
            background-color: #0066CC;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0052A3;
        }

        .btn-outline {
            background-color: transparent;
            color: #1a1a1a;
            border: 2px solid #E5E5E5;
        }

        .btn-outline:hover {
            border-color: #0066CC;
        }

        .btn-large {
            padding: 24px 32px;
            font-size: 18px;
        }

        /* Hero Section */
        section {
            padding: 80px 0;
        }

        .hero {
            padding-top: 128px;
            padding-bottom: 80px;
            overflow: hidden;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 48px;
            align-items: center;
        }

        @media (min-width: 1024px) {
            .hero-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .hero-content {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }

        .hero-badge {
            display: inline-block;
            background-color: #F0F7FF;
            color: #0066CC;
            padding: 8px 16px;
            border-radius: 24px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 24px;
        }

        .hero-content h1 {
            margin-bottom: 24px;
            color: #1a1a1a;
        }

        .hero-content p {
            font-size: 20px;
            color: #666666;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .hero-buttons {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-top: 32px;
        }

        @media (min-width: 640px) {
            .hero-buttons {
                flex-direction: row;
            }
        }

        .hero-visuals {
            position: relative;
            height: 384px;
            display: flex;
            align-items: flex-end;
            justify-content: flex-end;
        }

        .hero-desktop {
            position: absolute;
            bottom: -48px;
            right: -48px;
            width: 384px;
            height: 288px;
            animation: fadeInDown 0.6s ease-out 0.2s forwards;
            opacity: 0;
        }

        .hero-mobile {
            position: absolute;
            bottom: -80px;
            left: -32px;
            width: 192px;
            height: 384px;
            animation: fadeInUp 0.6s ease-out 0.4s forwards;
            opacity: 0;
        }

        .hero-desktop img,
        .hero-mobile img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .hero-mobile img {
            border-radius: 24px;
        }

        /* Divider */
        .divider {
            position: relative;
            height: 80px;
            background-color: white;
            overflow: hidden;
        }

        .divider svg {
            width: 100%;
            height: 100%;
        }

        /* Features Section */
        .features-section {
            background-color: #F0F7FF;
            padding: 80px 0;
        }

        .section-header {
            text-align: center;
            margin-bottom: 64px;
        }

        .section-header h2 {
            margin-bottom: 16px;
        }

        .section-header p {
            font-size: 20px;
            color: #666666;
            max-width: 672px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 32px;
        }

        @media (min-width: 768px) {
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .features-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .feature-card {
            background-color: white;
            padding: 32px;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s ease;
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }

        .feature-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            background-color: #F0F7FF;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            color: #0066CC;
        }

        .feature-card h3 {
            font-size: 20px;
            margin-bottom: 12px;
        }

        .feature-card p {
            color: #666666;
        }

        /* Benefits Section */
        .benefits-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 48px;
            align-items: center;
        }

        @media (min-width: 1024px) {
            .benefits-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .benefits-image {
            position: relative;
            height: 384px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeInLeft 0.6s ease-out forwards;
            opacity: 0;
        }

        .benefits-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .benefits-content {
            animation: fadeInRight 0.6s ease-out forwards;
            opacity: 0;
        }

        .benefits-content h2 {
            margin-bottom: 24px;
        }

        .benefits-content p {
            font-size: 18px;
            color: #666666;
            margin-bottom: 24px;
        }

        .benefits-list {
            list-style: none;
            margin-bottom: 24px;
        }

        .benefits-list li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 16px;
            color: #1a1a1a;
        }

        .benefits-list li::before {
            content: "✓";
            width: 24px;
            height: 24px;
            background-color: #0066CC;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            flex-shrink: 0;
            margin-top: 4px;
        }

        /* Analytics Section */
        .analytics-section {
            background-color: #F0F7FF;
            padding: 80px 0;
        }

        .analytics-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 48px;
            align-items: center;
        }

        @media (min-width: 1024px) {
            .analytics-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .analytics-image {
            position: relative;
            height: 384px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeInRight 0.6s ease-out forwards;
            opacity: 0;
            order: 2;
        }

        @media (max-width: 1023px) {
            .analytics-image {
                order: 1;
            }
        }

        .analytics-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .analytics-content {
            animation: fadeInLeft 0.6s ease-out forwards;
            opacity: 0;
            order: 1;
        }

        @media (max-width: 1023px) {
            .analytics-content {
                order: 2;
            }
        }

        .analytics-content h2 {
            margin-bottom: 24px;
        }

        .analytics-content p {
            font-size: 18px;
            color: #666666;
            margin-bottom: 24px;
        }

        /* Pricing Section */
        .pricing-section {
            background-color: white;
            padding: 80px 0;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 32px;
        }

        @media (min-width: 768px) {
            .pricing-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .pricing-card {
            background-color: white;
            border: 2px solid #E5E5E5;
            border-radius: 8px;
            padding: 32px;
            transition: border-color 0.3s ease;
        }

        .pricing-card:hover {
            border-color: #0066CC;
        }

        .pricing-card.popular {
            background-color: #0066CC;
            color: white;
            transform: scale(1.05);
            box-shadow: 0 20px 25px -5px rgba(0, 102, 204, 0.2);
        }

        .pricing-badge {
            display: inline-block;
            background-color: #0052A3;
            color: white;
            padding: 8px 12px;
            border-radius: 24px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 16px;
        }

        .pricing-card h3 {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .pricing-card p {
            margin-bottom: 24px;
        }

        .pricing-card.popular p {
            color: #E6F0FF;
        }

        .pricing-card:not(.popular) p {
            color: #666666;
        }

        .pricing-amount {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 24px;
        }

        .pricing-card.popular .pricing-amount {
            color: white;
        }

        .pricing-card:not(.popular) .pricing-amount {
            color: #1a1a1a;
        }

        .pricing-card .btn {
            width: 100%;
            margin-bottom: 24px;
        }

        .pricing-card.popular .btn {
            background-color: white;
            color: #0066CC;
        }

        .pricing-card.popular .btn:hover {
            background-color: #F0F7FF;
        }

        .pricing-features {
            list-style: none;
        }

        .pricing-features li {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .pricing-features li::before {
            content: "✓";
            color: #0066CC;
            font-weight: bold;
        }

        .pricing-card.popular .pricing-features li::before {
            color: white;
        }

        /* CTA Section */
        .cta-section {
            background-color: #0066CC;
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .cta-section h2 {
            color: white;
            margin-bottom: 24px;
            font-size: 48px;
        }

        .cta-section p {
            color: #E6F0FF;
            font-size: 20px;
            max-width: 672px;
            margin: 0 auto 32px;
        }

        .cta-section .btn {
            background-color: white;
            color: #0066CC;
            font-weight: bold;
        }

        .cta-section .btn:hover {
            background-color: #F0F7FF;
        }

        /* Footer */
        footer {
            background-color: #1a1a1a;
            color: #999999;
            padding: 48px 0;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 32px;
            margin-bottom: 32px;
        }

        @media (min-width: 768px) {
            .footer-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        .footer-section h4 {
            color: white;
            font-weight: bold;
            margin-bottom: 16px;
            font-size: 16px;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 8px;
        }

        .footer-section ul li a {
            color: #999999;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .footer-section ul li a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid #333333;
            padding-top: 32px;
            text-align: center;
            font-size: 14px;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .feature-card:nth-child(1) { animation-delay: 0s; }
        .feature-card:nth-child(2) { animation-delay: 0.1s; }
        .feature-card:nth-child(3) { animation-delay: 0.2s; }
        .feature-card:nth-child(4) { animation-delay: 0.3s; }
        .feature-card:nth-child(5) { animation-delay: 0.4s; }
        .feature-card:nth-child(6) { animation-delay: 0.5s; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav id="navbar">
        <div class="container">
            <div class="nav-content">
                <div class="nav-logo">
                    <div class="nav-logo-icon">e</div>
                    <div class="nav-logo-text">easyShop</div>
                </div>
                <div class="nav-links">
                    <a href="#features">Fonctionnalités</a>
                    <a href="#benefits">Avantages</a>
                    <a href="#pricing">Tarifs</a>
                </div>
                 <a href="{{ route('home') }}" class="btn btn-primary" style=" text-decoration: none;">
                    Commencer
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-grid">
                <div class="hero-content">
                    <!-- <div class="hero-badge">✨ Gestion Commerciale Complète</div> -->
                    <h1>Votre boutique en ligne en quelques minutes</h1>
                    <p>easyShop est une solution complète de gestion commerciale et financière. Gérez vos factures, ventes, dépenses et bien plus encore, tout en un seul endroit.</p>
                    <div class="hero-buttons">
                        <a href="{{ route('home') }}" class="btn btn-primary btn-large" style=" text-decoration: none;">
                            Commencer gratuitement
                        </a>
                        <button class="btn btn-outline btn-large">En savoir plus</button>
                    </div>
                </div>
                <div class="hero-visuals">
                    <div class="hero-desktop">
                        <img src="img/laptop.webp" alt="Dashboard easyShop">
                    </div>
                    <div class="hero-mobile">
                        <img src="img/mobile.webp" alt="Application Mobile easyShop">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Divider -->
    <div class="divider">
        <svg viewBox="0 0 1200 100" preserveAspectRatio="none">
            <path d="M0,50 Q300,0 600,50 T1200,50 L1200,100 L0,100 Z" fill="#F0F7FF" opacity="0.6"/>
        </svg>
    </div>

    <!-- Features Section -->
    <section class="features-section" id="features">
        <div class="container">
            <div class="section-header">
                <h2>Fonctionnalités</h2>
                <p>Tout ce dont vous avez besoin pour gérer votre commerce et vos finances efficacement</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">📄</div>
                    <h3>Gestion des Factures</h3>
                    <p>Créez, envoyez et suivez vos factures automatiquement. Gérez les paiements et les relances.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📈</div>
                    <h3>Suivi des Ventes</h3>
                    <p>Analysez vos ventes en temps réel avec des graphiques détaillés et des rapports personnalisés.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Rapports Financiers</h3>
                    <p>Générez des rapports financiers complets pour mieux comprendre votre rentabilité.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>Automatisation</h3>
                    <p>Automatisez vos tâches répétitives et gagnez du temps sur l'administration.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔒</div>
                    <h3>Sécurité</h3>
                    <p>Vos données sont protégées avec le chiffrement SSL et les sauvegardes automatiques.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🕐</div>
                    <h3>Support 24/7</h3>
                    <p>Notre équipe est disponible pour vous aider à tout moment, jour et nuit.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="benefits">
        <div class="container">
            <div class="benefits-grid">
                <div class="benefits-image">
                    <img src="img/analytics.webp" alt="Gestion Commerciale">
                </div>
                <div class="benefits-content">
                    <h2>Simplifiez votre gestion commerciale</h2>
                    <p>easyShop vous permet de centraliser toutes vos opérations commerciales et financières en un seul endroit. Dites adieu aux feuilles de calcul complexes et aux processus manuels.</p>
                    <ul class="benefits-list">
                        <li>Gestion des stocks en temps réel</li>
                        <li>Intégration avec les paiements en ligne</li>
                        <li>Rapports fiscaux automatiques</li>
                        <li>Gestion multi-canaux (web, mobile, API)</li>
                    </ul>
                    <button class="btn btn-primary btn-large">En savoir plus</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Analytics Section -->
    <section class="analytics-section">
        <div class="container">
            <div class="analytics-grid">
                <div class="analytics-content">
                    <h2>Analysez vos performances</h2>
                    <p>Accédez à des analytics détaillées pour comprendre votre business et prendre les bonnes décisions. Nos tableaux de bord intuitifs vous donnent une vue d'ensemble en un coup d'œil.</p>
                    <ul class="benefits-list">
                        <li>Tableaux de bord personnalisables</li>
                        <li>Exports de données en plusieurs formats</li>
                        <li>Alertes et notifications intelligentes</li>
                    </ul>
                </div>
                <div class="analytics-image">
                    <img src="img/performance.webp" alt="Analytics">
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing-section" id="pricing">
        <div class="container">
            <div class="section-header">
                <h2>Tarifs Simples et Transparents</h2>
                <p>Choisissez le plan qui convient à votre business</p>
            </div>
            <div class="pricing-grid">
                <div class="pricing-card">
                    <h3>Starter</h3>
                    <p>Pour les petits commerces</p>
                    <div class="pricing-amount">29€<span style="font-size: 18px; color: #666666;">/mois</span></div>
                    <button class="btn btn-primary">Commencer</button>
                    <ul class="pricing-features">
                        <li>Jusqu'à 100 factures/mois</li>
                        <li>1 utilisateur</li>
                        <li>Support par email</li>
                    </ul>
                </div>
                <div class="pricing-card popular">
                    <div class="pricing-badge">Populaire</div>
                    <h3>Pro</h3>
                    <p>Pour les commerces en croissance</p>
                    <div class="pricing-amount">79€<span style="font-size: 18px; color: #E6F0FF;">/mois</span></div>
                    <button class="btn btn-primary">Commencer</button>
                    <ul class="pricing-features">
                        <li>Factures illimitées</li>
                        <li>Jusqu'à 5 utilisateurs</li>
                        <li>Support prioritaire</li>
                        <li>Intégrations avancées</li>
                    </ul>
                </div>
                <div class="pricing-card">
                    <h3>Enterprise</h3>
                    <p>Pour les grandes entreprises</p>
                    <div class="pricing-amount">Sur devis</div>
                    <button class="btn btn-primary">Nous contacter</button>
                    <ul class="pricing-features">
                        <li>Tout inclus</li>
                        <li>Utilisateurs illimités</li>
                        <li>Support dédié</li>
                        <li>Personnalisations</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Prêt à transformer votre commerce?</h2>
            <p>Rejoignez des milliers d'entrepreneurs qui font confiance à easyShop pour gérer leur business</p>
            <button class="btn btn-primary btn-large">Démarrer Gratuitement →</button>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-section">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                        <div style="width: 32px; height: 32px; background-color: #0066CC; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">e</div>
                        <span style="font-weight: bold; color: white;">easyShop</span>
                    </div>
                    <p style="font-size: 14px;">Gestion commerciale et financière simplifiée</p>
                </div>
                <div class="footer-section">
                    <h4>Produit</h4>
                    <ul>
                        <li><a href="#">Fonctionnalités</a></li>
                        <li><a href="#">Tarifs</a></li>
                        <li><a href="#">Sécurité</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Entreprise</h4>
                    <ul>
                        <li><a href="#">À propos</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Carrières</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Légal</h4>
                    <ul>
                        <li><a href="#">Confidentialité</a></li>
                        <li><a href="#">Conditions</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 easyShop. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>
