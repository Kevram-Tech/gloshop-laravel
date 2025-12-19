# Guide de test des endpoints API

Ce document décrit comment tester tous les endpoints de l'API GloShop.

## Prérequis

1. **Serveur Laravel démarré** :
   ```bash
   php artisan serve
   ```
   Le serveur doit être accessible sur `http://localhost:8000`

2. **Base de données configurée** :
   - Créer la base de données
   - Exécuter les migrations : `php artisan migrate`
   - Optionnel : Exécuter les seeders : `php artisan db:seed`

3. **Créer un utilisateur admin** (pour tester les endpoints admin) :
   ```bash
   php artisan tinker
   ```
   Puis :
   ```php
   $admin = User::create([
       'name' => 'Admin',
       'email' => 'admin@gloshop.com',
       'password' => Hash::make('password123'),
   ]);
   ```

## Méthodes de test

### 1. Tests automatisés avec PHPUnit

Exécuter tous les tests :
```bash
php artisan test --filter ApiEndpointsTest
```

**Note** : Les tests nécessitent une base de données de test configurée dans `phpunit.xml`.

### 2. Tests manuels avec scripts

#### Windows (PowerShell)
```powershell
cd D:\gloshop-laravel\tests
.\test-api.ps1
```

#### Linux/Mac (Bash)
```bash
cd D:/gloshop-laravel/tests
chmod +x test-api.sh
./test-api.sh
```

### 3. Tests manuels avec Postman/Insomnia

Importer la collection d'endpoints (voir section suivante).

### 4. Tests manuels avec curl

Voir les exemples ci-dessous pour chaque endpoint.

---

## Liste complète des endpoints

### Endpoints publics

#### 1. Authentification

**Register**
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**Login**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

#### 2. Catégories

**Get all categories**
```bash
curl -X GET http://localhost:8000/api/categories
```

**Get category by slug**
```bash
curl -X GET http://localhost:8000/api/categories/robes
```

#### 3. Produits

**Get all products**
```bash
curl -X GET http://localhost:8000/api/products
```

**Get products with filters**
```bash
curl -X GET "http://localhost:8000/api/products?category_id=1&featured=1&search=robe&page=1"
```

**Get product by slug**
```bash
curl -X GET http://localhost:8000/api/products/test-product
```

---

### Endpoints protégés (nécessitent authentification)

**Récupérer le token** :
```bash
TOKEN=$(curl -s -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}' \
  | jq -r '.data.token')
```

#### 1. Authentification

**Get current user**
```bash
curl -X GET http://localhost:8000/api/auth/user \
  -H "Authorization: Bearer $TOKEN"
```

**Update profile**
```bash
curl -X PUT http://localhost:8000/api/auth/profile \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name": "Updated Name", "phone": "+225123456789"}'
```

**Change password**
```bash
curl -X PUT http://localhost:8000/api/auth/password \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "current_password": "password123",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
  }'
```

**Logout**
```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer $TOKEN"
```

#### 2. Panier (Cart)

**Get cart**
```bash
curl -X GET http://localhost:8000/api/cart \
  -H "Authorization: Bearer $TOKEN"
```

**Add to cart**
```bash
curl -X POST http://localhost:8000/api/cart \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "product_id": 1,
    "quantity": 2,
    "size": "M",
    "color": "Red"
  }'
```

**Update cart item**
```bash
curl -X PUT http://localhost:8000/api/cart/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"quantity": 3}'
```

**Remove from cart**
```bash
curl -X DELETE http://localhost:8000/api/cart/1 \
  -H "Authorization: Bearer $TOKEN"
```

#### 3. Commandes (Orders)

**Get orders**
```bash
curl -X GET http://localhost:8000/api/orders \
  -H "Authorization: Bearer $TOKEN"
```

**Create order**
```bash
curl -X POST http://localhost:8000/api/orders \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "shipping_address": "123 Test Street",
    "shipping_name": "Test User",
    "shipping_phone": "+225123456789",
    "shipping_email": "test@example.com",
    "payment_method": "card",
    "notes": "Livraison le matin"
  }'
```

**Get order details**
```bash
curl -X GET http://localhost:8000/api/orders/1 \
  -H "Authorization: Bearer $TOKEN"
```

#### 4. Favoris (Favorites)

**Get favorites**
```bash
curl -X GET http://localhost:8000/api/favorites \
  -H "Authorization: Bearer $TOKEN"
```

**Add to favorites**
```bash
curl -X POST http://localhost:8000/api/favorites \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"product_id": 1}'
```

**Check if favorite**
```bash
curl -X POST http://localhost:8000/api/favorites/check \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"product_id": 1}'
```

**Remove from favorites**
```bash
curl -X DELETE http://localhost:8000/api/favorites/1 \
  -H "Authorization: Bearer $TOKEN"
```

#### 5. Adresses (Addresses)

**Get addresses**
```bash
curl -X GET http://localhost:8000/api/addresses \
  -H "Authorization: Bearer $TOKEN"
```

**Create address**
```bash
curl -X POST http://localhost:8000/api/addresses \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Home",
    "full_name": "Test User",
    "phone": "+225123456789",
    "address": "123 Test Street",
    "city": "Abidjan",
    "postal_code": "01",
    "country": "Côte d'\''Ivoire",
    "is_default": true
  }'
```

**Update address**
```bash
curl -X PUT http://localhost:8000/api/addresses/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"title": "Work"}'
```

**Set default address**
```bash
curl -X POST http://localhost:8000/api/addresses/1/set-default \
  -H "Authorization: Bearer $TOKEN"
```

**Delete address**
```bash
curl -X DELETE http://localhost:8000/api/addresses/1 \
  -H "Authorization: Bearer $TOKEN"
```

#### 6. Moyens de paiement (Payment Methods)

**Get payment methods**
```bash
curl -X GET http://localhost:8000/api/payment-methods \
  -H "Authorization: Bearer $TOKEN"
```

**Create payment method (Card)**
```bash
curl -X POST http://localhost:8000/api/payment-methods \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "card",
    "card_number": "1234567890123456",
    "card_holder": "Test User",
    "expiry_date": "12/25",
    "cvv": "123",
    "is_default": true
  }'
```

**Update payment method**
```bash
curl -X PUT http://localhost:8000/api/payment-methods/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"card_holder": "Updated Name"}'
```

**Set default payment method**
```bash
curl -X POST http://localhost:8000/api/payment-methods/1/set-default \
  -H "Authorization: Bearer $TOKEN"
```

**Delete payment method**
```bash
curl -X DELETE http://localhost:8000/api/payment-methods/1 \
  -H "Authorization: Bearer $TOKEN"
```

---

### Endpoints Admin

**Récupérer le token admin** :
```bash
ADMIN_TOKEN=$(curl -s -X POST http://localhost:8000/api/admin/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@gloshop.com","password":"password123"}' \
  | jq -r '.data.token')
```

#### 1. Dashboard

**Get dashboard stats**
```bash
curl -X GET http://localhost:8000/api/admin/dashboard/stats \
  -H "Authorization: Bearer $ADMIN_TOKEN"
```

#### 2. Commandes (Admin)

**Get all orders**
```bash
curl -X GET http://localhost:8000/api/admin/orders \
  -H "Authorization: Bearer $ADMIN_TOKEN"
```

**Get orders with filters**
```bash
curl -X GET "http://localhost:8000/api/admin/orders?status=pending&payment_status=paid&page=1&per_page=10" \
  -H "Authorization: Bearer $ADMIN_TOKEN"
```

**Get order by ID**
```bash
curl -X GET http://localhost:8000/api/admin/orders/1 \
  -H "Authorization: Bearer $ADMIN_TOKEN"
```

**Update order status**
```bash
curl -X PUT http://localhost:8000/api/admin/orders/1/status \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"status": "processing"}'
```

#### 3. Produits (Admin)

**Get all products**
```bash
curl -X GET http://localhost:8000/api/admin/products \
  -H "Authorization: Bearer $ADMIN_TOKEN"
```

**Get products with filters**
```bash
curl -X GET "http://localhost:8000/api/admin/products?category_id=1&featured=1&search=test&page=1&per_page=10" \
  -H "Authorization: Bearer $ADMIN_TOKEN"
```

**Get product by ID**
```bash
curl -X GET http://localhost:8000/api/admin/products/1 \
  -H "Authorization: Bearer $ADMIN_TOKEN"
```

**Create product**
```bash
curl -X POST http://localhost:8000/api/admin/products \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "category_id": 1,
    "name": "New Product",
    "description": "Product description",
    "price": 15000,
    "discount_price": 12000,
    "sku": "NEW-001",
    "stock": 30,
    "images": ["https://example.com/image.jpg"],
    "sizes": ["S", "M", "L"],
    "colors": ["Red", "Blue"],
    "is_featured": true,
    "is_active": true
  }'
```

**Update product**
```bash
curl -X PUT http://localhost:8000/api/admin/products/1 \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Product",
    "price": 18000
  }'
```

**Delete product**
```bash
curl -X DELETE http://localhost:8000/api/admin/products/1 \
  -H "Authorization: Bearer $ADMIN_TOKEN"
```

#### 4. Catégories (Admin)

**Get all categories**
```bash
curl -X GET http://localhost:8000/api/admin/categories \
  -H "Authorization: Bearer $ADMIN_TOKEN"
```

**Create category**
```bash
curl -X POST http://localhost:8000/api/admin/categories \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "New Category",
    "description": "Category description",
    "image": "https://example.com/category.jpg",
    "is_active": true
  }'
```

**Update category**
```bash
curl -X PUT http://localhost:8000/api/admin/categories/1 \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Category",
    "description": "Updated description"
  }'
```

**Delete category**
```bash
curl -X DELETE http://localhost:8000/api/admin/categories/1 \
  -H "Authorization: Bearer $ADMIN_TOKEN"
```

#### 5. Utilisateurs (Admin)

**Get all users**
```bash
curl -X GET http://localhost:8000/api/admin/users \
  -H "Authorization: Bearer $ADMIN_TOKEN"
```

**Get users with search**
```bash
curl -X GET "http://localhost:8000/api/admin/users?search=test&page=1&per_page=10" \
  -H "Authorization: Bearer $ADMIN_TOKEN"
```

**Get user by ID**
```bash
curl -X GET http://localhost:8000/api/admin/users/1 \
  -H "Authorization: Bearer $ADMIN_TOKEN"
```

#### 6. Statistiques (Admin)

**Get sales by period**
```bash
curl -X GET http://localhost:8000/api/admin/statistics/sales-by-period \
  -H "Authorization: Bearer $ADMIN_TOKEN"
```

**Get top selling products**
```bash
curl -X GET http://localhost:8000/api/admin/statistics/top-selling-products \
  -H "Authorization: Bearer $ADMIN_TOKEN"
```

**Get stock statistics**
```bash
curl -X GET http://localhost:8000/api/admin/statistics/stock \
  -H "Authorization: Bearer $ADMIN_TOKEN"
```

---

## Format des réponses

### Succès
```json
{
  "success": true,
  "message": "Message optionnel",
  "data": {...}
}
```

### Erreur
```json
{
  "success": false,
  "message": "Message d'erreur",
  "errors": {
    "field": ["Message d'erreur"]
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

## Notes importantes

1. **Authentification** : Tous les endpoints protégés nécessitent un token Bearer dans le header `Authorization`.
2. **Content-Type** : Tous les endpoints POST/PUT nécessitent `Content-Type: application/json`.
3. **Pagination** : Les endpoints de liste supportent les paramètres `page` et `per_page`.
4. **Filtres** : Les endpoints de liste supportent différents filtres selon l'endpoint.

