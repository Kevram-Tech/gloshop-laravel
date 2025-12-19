# API Documentation - GloShop

## Base URL
```
http://localhost:8000/api
```

## Authentication
L'API utilise Laravel Sanctum pour l'authentification. Les routes protégées nécessitent un token Bearer dans le header `Authorization`.

### Headers requis pour les routes protégées
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

---

## Routes Publiques

### Authentication

#### Register
```http
POST /api/auth/register
```

**Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Inscription réussie",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "token": "1|..."
  }
}
```

#### Login
```http
POST /api/auth/login
```

**Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Connexion réussie",
  "data": {
    "user": {...},
    "token": "1|..."
  }
}
```

### Categories

#### Get all categories
```http
GET /api/categories
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Robes",
      "slug": "robes",
      "image": "https://...",
      "is_active": true,
      "products_count": 10
    }
  ]
}
```

#### Get category by slug
```http
GET /api/categories/{slug}
```

### Products

#### Get all products
```http
GET /api/products?category_id=1&featured=1&search=robe&sort_by=price&sort_order=asc&page=1&per_page=15
```

**Query Parameters:**
- `category_id` (optional): Filter by category
- `featured` (optional): Filter featured products (1 or 0)
- `search` (optional): Search in name and description
- `sort_by` (optional): Field to sort by (default: created_at)
- `sort_order` (optional): asc or desc (default: desc)
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 15)

#### Get product by slug
```http
GET /api/products/{slug}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Robe d'été",
    "slug": "robe-ete",
    "price": 25000,
    "discount_price": 19900,
    "images": ["https://..."],
    "related_products": [...]
  }
}
```

---

## Routes Protégées

### Authentication

#### Get current user
```http
GET /api/auth/user
```

#### Update profile
```http
PUT /api/auth/profile
```

**Body:**
```json
{
  "name": "John Doe Updated",
  "email": "newemail@example.com",
  "phone": "+33123456789"
}
```

#### Change password
```http
PUT /api/auth/password
```

**Body:**
```json
{
  "current_password": "oldpassword",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

#### Logout
```http
POST /api/auth/logout
```

### Cart

#### Get cart
```http
GET /api/cart
```

#### Add to cart
```http
POST /api/cart
```

**Body:**
```json
{
  "product_id": 1,
  "quantity": 2,
  "size": "M",
  "color": "Rose"
}
```

#### Update cart item
```http
PUT /api/cart/{cartId}
```

**Body:**
```json
{
  "quantity": 3
}
```

#### Remove from cart
```http
DELETE /api/cart/{cartId}
```

### Orders

#### Get orders
```http
GET /api/orders
```

#### Create order
```http
POST /api/orders
```

**Body:**
```json
{
  "shipping_address": "123 Rue de la Mode, 75001 Paris",
  "shipping_name": "John Doe",
  "shipping_phone": "+33123456789",
  "shipping_email": "john@example.com",
  "payment_method": "card",
  "notes": "Livraison le matin"
}
```

#### Get order details
```http
GET /api/orders/{orderId}
```

### Favorites

#### Get favorites
```http
GET /api/favorites
```

#### Add to favorites
```http
POST /api/favorites
```

**Body:**
```json
{
  "product_id": 1
}
```

#### Remove from favorites
```http
DELETE /api/favorites/{id}
```

#### Check if product is favorite
```http
POST /api/favorites/check
```

**Body:**
```json
{
  "product_id": 1
}
```

**Response:**
```json
{
  "success": true,
  "is_favorite": true
}
```

### Addresses

#### Get addresses
```http
GET /api/addresses
```

#### Create address
```http
POST /api/addresses
```

**Body:**
```json
{
  "title": "Domicile",
  "full_name": "John Doe",
  "phone": "+33123456789",
  "address": "123 Rue de la Mode",
  "city": "Paris",
  "postal_code": "75001",
  "country": "France",
  "is_default": true
}
```

#### Update address
```http
PUT /api/addresses/{addressId}
```

#### Delete address
```http
DELETE /api/addresses/{addressId}
```

#### Set default address
```http
POST /api/addresses/{addressId}/set-default
```

### Payment Methods

#### Get payment methods
```http
GET /api/payment-methods
```

#### Create payment method
```http
POST /api/payment-methods
```

**Body (Card):**
```json
{
  "type": "card",
  "card_number": "1234567890123456",
  "card_holder": "John Doe",
  "expiry_date": "12/25",
  "cvv": "123",
  "is_default": true
}
```

**Body (PayPal):**
```json
{
  "type": "paypal",
  "is_default": false
}
```

**Body (Mobile Money):**
```json
{
  "type": "mobile_money",
  "phone": "+33123456789",
  "provider": "Orange Money",
  "is_default": false
}
```

#### Update payment method
```http
PUT /api/payment-methods/{paymentMethodId}
```

#### Delete payment method
```http
DELETE /api/payment-methods/{paymentMethodId}
```

#### Set default payment method
```http
POST /api/payment-methods/{paymentMethodId}/set-default
```

---

## Routes Admin

Toutes les routes admin nécessitent l'authentification avec un token Bearer.

### Authentication

#### Admin Login
```http
POST /api/admin/login
```

**Body:**
```json
{
  "email": "admin@gloshop.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Connexion réussie",
  "data": {
    "user": {...},
    "token": "1|..."
  }
}
```

#### Admin Logout
```http
POST /api/admin/logout
```

### Dashboard

#### Get Dashboard Statistics
```http
GET /api/admin/dashboard/stats
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total_orders": 150,
    "total_products": 45,
    "total_users": 320,
    "total_revenue": 12500000,
    "pending_orders": 12,
    "active_products": 42
  }
}
```

### Orders (Admin)

#### Get all orders
```http
GET /api/admin/orders?status=pending&payment_status=paid&page=1&per_page=20
```

**Query Parameters:**
- `status` (optional): Filter by status (pending, processing, shipped, delivered, cancelled)
- `payment_status` (optional): Filter by payment status (paid, pending, failed)
- `page` (optional): Page number
- `per_page` (optional): Items per page

#### Get order by ID
```http
GET /api/admin/orders/{id}
```

#### Update order status
```http
PUT /api/admin/orders/{id}/status
```

**Body:**
```json
{
  "status": "processing"
}
```

### Products (Admin)

#### Get all products
```http
GET /api/admin/products?category_id=1&featured=1&search=robe&page=1&per_page=20
```

#### Get product by ID
```http
GET /api/admin/products/{id}
```

#### Create product
```http
POST /api/admin/products
```

**Body:**
```json
{
  "category_id": 1,
  "name": "Robe d'été",
  "description": "Belle robe d'été",
  "price": 25000,
  "discount_price": 19900,
  "sku": "ROBE-001",
  "stock": 50,
  "images": ["https://..."],
  "sizes": ["S", "M", "L"],
  "colors": ["Rose", "Bleu"],
  "is_featured": true,
  "is_active": true
}
```

#### Update product
```http
PUT /api/admin/products/{id}
```

#### Delete product
```http
DELETE /api/admin/products/{id}
```

### Categories (Admin)

#### Get all categories
```http
GET /api/admin/categories
```

#### Create category
```http
POST /api/admin/categories
```

**Body:**
```json
{
  "name": "Robes",
  "description": "Belles robes",
  "image": "https://...",
  "is_active": true
}
```

#### Update category
```http
PUT /api/admin/categories/{id}
```

#### Delete category
```http
DELETE /api/admin/categories/{id}
```

### Users (Admin)

#### Get all users
```http
GET /api/admin/users?search=john&page=1&per_page=20
```

**Query Parameters:**
- `search` (optional): Search in name and email
- `page` (optional): Page number
- `per_page` (optional): Items per page

#### Get user by ID
```http
GET /api/admin/users/{id}
```

### Statistics (Admin)

#### Get sales by period
```http
GET /api/admin/statistics/sales-by-period
```

**Response:**
```json
{
  "success": true,
  "data": {
    "by_day": {
      "01/12": 150000,
      "02/12": 200000,
      ...
    },
    "by_week": {
      "Sem 25/11": 1200000,
      "Sem 02/12": 1500000,
      ...
    },
    "by_month": {
      "Nov 2024": 5000000,
      "Dec 2024": 6000000,
      ...
    }
  }
}
```

#### Get top selling products
```http
GET /api/admin/statistics/top-selling-products
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "product_id": 1,
      "product_name": "Robe d'été",
      "sku": "ROBE-001",
      "product_image": "https://...",
      "quantity": 150,
      "revenue": 3750000,
      "unit_price": 25000
    },
    ...
  ]
}
```

#### Get stock statistics
```http
GET /api/admin/statistics/stock
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total_stock_value": 50000000,
    "total_products": 45,
    "total_units": 1200,
    "by_product": [
      {
        "product_id": 1,
        "product_name": "Robe d'été",
        "product_image": "https://...",
        "stock": 50,
        "unit_price": 25000,
        "total_value": 1250000,
        "sku": "ROBE-001"
      },
      ...
    ]
  }
}
```

---

## Codes de réponse

- `200` - Succès
- `201` - Créé avec succès
- `400` - Requête invalide
- `401` - Non authentifié
- `403` - Non autorisé
- `404` - Ressource non trouvée
- `422` - Erreur de validation
- `500` - Erreur serveur

---

## Format des réponses

Toutes les réponses suivent ce format :

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
    "field": ["Message d'erreur"]
  }
}
```

---

## Notes importantes

1. **Sécurité des données de paiement** : Les numéros de carte et CVV sont cryptés dans la base de données
2. **Adresses par défaut** : Une seule adresse peut être définie comme par défaut à la fois
3. **Moyens de paiement par défaut** : Un seul moyen de paiement peut être défini comme par défaut à la fois
4. **Stock** : Le stock est vérifié avant l'ajout au panier et mis à jour lors de la commande
5. **Pagination** : Les listes de produits utilisent la pagination Laravel

