# Guide d'Intégration des Paiements

## Configuration PayGateGlobal

### URL de Callback

Pour recevoir les confirmations de paiement de PayGateGlobal, vous devez configurer l'URL de callback dans votre tableau de bord PayGateGlobal :

```
https://votre-domaine.com/api/payments/paygate/callback
```

**Important** : Cette route est publique (sans authentification) car PayGateGlobal doit pouvoir y accéder.

### Clé API

La clé API PayGateGlobal est configurée dans `PaymentController.php` :
```php
private const PAYGATE_AUTH_TOKEN = 'bb8f5926-4460-46b3-8b3a-9b4abbbad46f';
```

**Sécurité** : En production, déplacez cette clé dans le fichier `.env` :
```env
PAYGATE_AUTH_TOKEN=bb8f5926-4460-46b3-8b3a-9b4abbbad46f
```

Puis dans `PaymentController.php` :
```php
private const PAYGATE_AUTH_TOKEN = env('PAYGATE_AUTH_TOKEN');
```

## Migration de la Base de Données

Exécutez la migration pour créer la table `payment_transactions` :

```bash
php artisan migrate
```

## Endpoints API

### Initier un paiement PayGate (T-Money/Flooz)

**POST** `/api/payments/paygate/initiate`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
  "order_id": 1,
  "phone_number": "+22890123456",
  "network": "FLOOZ"
}
```

**Réponse:**
```json
{
  "success": true,
  "message": "Paiement initié avec succès",
  "data": {
    "tx_reference": "TX123456789",
    "transaction_id": 1,
    "order_id": 1
  }
}
```

### Vérifier le statut d'un paiement

**POST** `/api/payments/paygate/check-status`

**Body:**
```json
{
  "tx_reference": "TX123456789"
}
```

ou

```json
{
  "identifier": "ORD-ABC123XYZ"
}
```

### Traiter un paiement par carte Visa

**POST** `/api/payments/card/process`

**Body:**
```json
{
  "order_id": 1,
  "card_number": "4111111111111111",
  "card_holder": "JOHN DOE",
  "expiry_month": "12",
  "expiry_year": "25",
  "cvv": "123"
}
```

### Callback PayGateGlobal (Public)

**POST** `/api/payments/paygate/callback`

Cette route reçoit automatiquement les notifications de PayGateGlobal lorsque le paiement est effectué.

## Paiements par Carte Visa

Actuellement, le paiement par carte Visa est simulé. Pour une intégration réelle, vous devez :

1. Choisir un processeur de paiement (Stripe, PayPal, etc.)
2. Créer un compte et obtenir les clés API
3. Mettre à jour la méthode `processCardPayment` dans `PaymentController.php`

### Exemple avec Stripe

```php
use Stripe\Stripe;
use Stripe\Charge;

Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

$charge = Charge::create([
    'amount' => (int)($order->total_amount * 100), // Convert to cents
    'currency' => 'xof',
    'source' => $validated['card_token'], // Token from Stripe.js
    'description' => "Commande #{$order->order_number}",
]);
```

## États des Paiements

- `pending` : Paiement en attente
- `completed` : Paiement réussi
- `failed` : Paiement échoué
- `cancelled` : Paiement annulé

## Codes de Statut PayGateGlobal

- `0` : Transaction réussie
- `2` : Transaction en cours
- `4` : Transaction expirée
- `6` : Transaction annulée

