# GloShop API - Guide d'installation et d'utilisation

## Prérequis

- PHP >= 8.1
- Composer
- MySQL/PostgreSQL
- Laravel 10.x

## Installation

1. **Installer les dépendances**
```bash
composer install
```

2. **Configurer l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Configurer la base de données dans `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gloshop
DB_USERNAME=root
DB_PASSWORD=
```

4. **Exécuter les migrations**
```bash
php artisan migrate
```

5. **Installer Laravel Sanctum (si pas déjà fait)**
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

6. **Démarrer le serveur**
```bash
php artisan serve
```

L'API sera accessible sur `http://localhost:8000/api`

## Configuration CORS

Pour permettre les requêtes depuis votre application Flutter, assurez-vous que CORS est configuré dans `config/cors.php` :

```php
'allowed_origins' => ['*'], // En développement
// Ou spécifiez votre domaine en production
'allowed_origins' => ['https://votre-domaine.com'],
```

## Structure de la base de données

L'API utilise les tables suivantes :
- `users` - Utilisateurs
- `categories` - Catégories de produits
- `products` - Produits
- `carts` - Panier d'achat
- `orders` - Commandes
- `order_items` - Articles de commande
- `wishlists` - Liste de souhaits (favoris)
- `addresses` - Adresses de livraison
- `payment_methods` - Moyens de paiement

## Authentification

L'API utilise Laravel Sanctum pour l'authentification par token.

### Obtenir un token

1. **Inscription** : `POST /api/auth/register`
2. **Connexion** : `POST /api/auth/login`

Les deux endpoints retournent un token dans la réponse.

### Utiliser le token

Inclure le token dans le header `Authorization` :
```
Authorization: Bearer {votre_token}
```

## Endpoints principaux

### Public
- `GET /api/categories` - Liste des catégories
- `GET /api/categories/{slug}` - Détails d'une catégorie
- `GET /api/products` - Liste des produits (avec filtres)
- `GET /api/products/{slug}` - Détails d'un produit

### Authentification
- `POST /api/auth/register` - Inscription
- `POST /api/auth/login` - Connexion
- `POST /api/auth/logout` - Déconnexion (protégé)
- `GET /api/auth/user` - Utilisateur actuel (protégé)
- `PUT /api/auth/profile` - Mettre à jour le profil (protégé)
- `PUT /api/auth/password` - Changer le mot de passe (protégé)

### Panier (protégé)
- `GET /api/cart` - Obtenir le panier
- `POST /api/cart` - Ajouter au panier
- `PUT /api/cart/{id}` - Mettre à jour un article
- `DELETE /api/cart/{id}` - Retirer du panier

### Commandes (protégé)
- `GET /api/orders` - Liste des commandes
- `POST /api/orders` - Créer une commande
- `GET /api/orders/{id}` - Détails d'une commande

### Favoris (protégé)
- `GET /api/favorites` - Liste des favoris
- `POST /api/favorites` - Ajouter aux favoris
- `DELETE /api/favorites/{id}` - Retirer des favoris
- `POST /api/favorites/check` - Vérifier si un produit est en favoris

### Adresses (protégé)
- `GET /api/addresses` - Liste des adresses
- `POST /api/addresses` - Créer une adresse
- `PUT /api/addresses/{id}` - Mettre à jour une adresse
- `DELETE /api/addresses/{id}` - Supprimer une adresse
- `POST /api/addresses/{id}/set-default` - Définir comme adresse par défaut

### Moyens de paiement (protégé)
- `GET /api/payment-methods` - Liste des moyens de paiement
- `POST /api/payment-methods` - Ajouter un moyen de paiement
- `PUT /api/payment-methods/{id}` - Mettre à jour un moyen de paiement
- `DELETE /api/payment-methods/{id}` - Supprimer un moyen de paiement
- `POST /api/payment-methods/{id}/set-default` - Définir comme moyen par défaut

## Format des réponses

Toutes les réponses suivent un format standardisé :

**Succès:**
```json
{
  "success": true,
  "message": "Message optionnel",
  "data": {...}
}
```

**Erreur:**
```json
{
  "success": false,
  "message": "Message d'erreur",
  "errors": {
    "field": ["Message d'erreur de validation"]
  }
}
```

## Codes de statut HTTP

- `200` - Succès
- `201` - Créé avec succès
- `400` - Requête invalide
- `401` - Non authentifié
- `403` - Non autorisé
- `404` - Ressource non trouvée
- `422` - Erreur de validation
- `500` - Erreur serveur

## Sécurité

1. **Données sensibles** : Les numéros de carte et CVV sont cryptés dans la base de données
2. **Validation** : Toutes les entrées sont validées avant traitement
3. **Autorisation** : Les utilisateurs ne peuvent accéder qu'à leurs propres ressources
4. **Tokens** : Les tokens Sanctum expirent après une période d'inactivité

## Tests

Pour tester l'API, vous pouvez utiliser :
- Postman
- Insomnia
- cURL
- Votre application Flutter

## Documentation complète

Voir `API_DOCUMENTATION.md` pour la documentation complète de tous les endpoints.

## Support

Pour toute question ou problème, consultez la documentation Laravel ou contactez l'équipe de développement.

