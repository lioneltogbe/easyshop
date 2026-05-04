# easyShop 

easyShop est une application de gestion commerciale complète développée avec Laravel 12.
Elle permet de piloter les ventes, les achats, les stocks, les clients, les fournisseurs, les utilisateurs et la comptabilité.

## Présentation

easyShop est conçu pour les petites et moyennes entreprises souhaitant centraliser leurs opérations commerciales dans un tableau de bord unique.
L’application prend en charge la gestion des permissions, la génération de factures PDF, les transactions financières et le suivi des mouvements de stock.

## Fonctionnalités principales

- Authentification sécurisée
- Gestion des utilisateurs (création, modification, suppression)
- Gestion des rôles et permissions avec `spatie/laravel-permission`
- Gestion des produits, catégories et lots
- Suivi des mouvements de stock (entrée, sortie, ajustement)
- Alertes de stock pour produits en rupture ou seuil critique
- Gestion des ventes et des achats avec états de commande
- Gestion des clients et fournisseurs
- Génération et affichage de factures PDF
- Vérification d’authenticité des factures
- Gestion des transactions financières et visualisation de métriques
- Pages de paramètres pour profil, sécurité, notifications et informations de l’entreprise



## Installation

1. Cloner le dépôt :
   ```powershell
   git clone https://github.com/lionel/NOM_DU_REPO.git
   cd "easyShop versiopn 2"
   ```

2. Installer les dépendances PHP :
   ```powershell
   composer install
   ```

3. Installer les dépendances frontend :
   ```powershell
   npm install
   ```

4. Copier le fichier d’environnement :
   ```powershell
   copy .env.example .env
   ```

5. Générer la clé d’application :
   ```powershell
   php artisan key:generate
   ```

6. Configurer la base de données dans le fichier `.env`

7. Lancer les migrations :
   ```powershell
   php artisan migrate
   ```

8. Compiler les assets :
   ```powershell
   npm run build
   ```

## Exécution

Lancer l’application en local avec :

```powershell
php artisan serve
```

Puis ouvrir `http://127.0.0.1:8000` dans un navigateur.

## Commandes utiles

- `php artisan migrate` : exécuter les migrations
- `php artisan db:seed` : exécuter les seeders
- `php artisan test` : lancer les tests
- `npm run dev` : lancer le mode développement Vite

## Gestion des rôles et permissions

Le projet utilise des rôles et permissions pour limiter l’accès aux sections critiques :
- `admin`
- `manager`
- 'magasinier'
- `comptable`
- `vendeur`

Les sections produits, achats, ventes, finances, factures et paramètres sont protégées selon ces rôles et permissions.

## Améliorations possibles

- Ajouter une API REST pour l’export et l’intégration externe
- Envoyer les factures par email automatiquement
- Ajouter un tableau de bord analytique plus complet
- Renforcer les tests fonctionnels

## Aide

Pour toute question ou configuration supplémentaire, consulter la documentation Laravel ou demander un support dédié.
