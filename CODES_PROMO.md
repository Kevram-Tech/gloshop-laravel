# Codes Promo Mishop

## Liste des codes promo disponibles

### 1. **BIENVENUE2026** 🎉
- **Type**: Pourcentage
- **Réduction**: 15%
- **Montant minimum**: 5,000 FCFA
- **Réduction maximale**: 2,000 FCFA
- **Utilisation**: 1,000 fois au total, 1 fois par utilisateur
- **Validité**: 6 mois
- **Description**: Code de bienvenue pour les nouveaux clients

### 2. **PROMO10** 💰
- **Type**: Pourcentage
- **Réduction**: 10%
- **Montant minimum**: 2,000 FCFA
- **Réduction maximale**: 1,500 FCFA
- **Utilisation**: 500 fois au total, 3 fois par utilisateur
- **Validité**: 3 mois
- **Description**: Profitez de 10% de réduction sur votre commande

### 3. **SOLDES2026** 🔥
- **Type**: Pourcentage
- **Réduction**: 20%
- **Montant minimum**: 10,000 FCFA
- **Réduction maximale**: 5,000 FCFA
- **Utilisation**: 200 fois au total, 1 fois par utilisateur
- **Validité**: 2 mois
- **Description**: Code promo pour les soldes de 2026

### 4. **FIDELE500** ⭐
- **Type**: Montant fixe
- **Réduction**: 500 FCFA
- **Montant minimum**: 3,000 FCFA
- **Utilisation**: Illimitée au total, 5 fois par utilisateur
- **Validité**: 1 an
- **Description**: Réduction fixe pour nos clients fidèles

### 5. **LIVRAISON2026** 🚚
- **Type**: Livraison gratuite
- **Réduction**: Livraison offerte
- **Montant minimum**: 5,000 FCFA
- **Utilisation**: 300 fois au total, 2 fois par utilisateur
- **Validité**: 4 mois
- **Description**: Profitez de la livraison gratuite

### 6. **FLASH25** ⚡
- **Type**: Pourcentage
- **Réduction**: 25%
- **Montant minimum**: 15,000 FCFA
- **Réduction maximale**: 7,500 FCFA
- **Utilisation**: 100 fois au total, 1 fois par utilisateur
- **Validité**: 2 semaines
- **Description**: Vente flash - Offre limitée

### 7. **TEST2026** 🧪
- **Type**: Pourcentage
- **Réduction**: 10%
- **Montant minimum**: Aucun
- **Réduction maximale**: Aucune
- **Utilisation**: Illimitée
- **Validité**: 1 an
- **Description**: Code promo de test pour le développement

### 8. **MISHOP10** ✨
- **Type**: Pourcentage
- **Réduction**: 10%
- **Montant minimum**: Aucun
- **Réduction maximale**: 5,000 FCFA
- **Utilisation**: Illimitée au total, 5 fois par utilisateur
- **Validité**: 1 an
- **Description**: Code démo Mishop (app mobile)

---

## Comment utiliser les codes promo

### Via l'API

```bash
POST /api/promo-codes/validate
Authorization: Bearer {token}
Content-Type: application/json

{
  "code": "BIENVENUE2026",
  "amount": 10000
}
```

### Réponse en cas de succès

```json
{
  "success": true,
  "data": {
    "promo_code": {
      "id": 1,
      "code": "BIENVENUE2026",
      "name": "Code de bienvenue",
      "type": "percentage",
      "discount_value": "15.00",
      ...
    },
    "discount_amount": 1500.00,
    "final_amount": 8500.00
  }
}
```

### Réponse en cas d'erreur

```json
{
  "success": false,
  "message": "Code promo invalide ou inexistant"
}
```

---

## Gestion des codes promo

### Créer un nouveau code promo

```bash
php artisan tinker
```

```php
PromoCode::create([
    'code' => 'NOUVEAU2026',
    'name' => 'Nouveau code',
    'type' => 'percentage', // ou 'fixed', 'free_shipping'
    'discount_value' => 15.00,
    'min_purchase_amount' => 5000.00,
    'start_date' => now(),
    'end_date' => now()->addMonths(3),
    'is_active' => true,
    'usage_limit' => 100,
    'usage_limit_per_user' => 1,
]);
```

### Réexécuter le seeder

```bash
php artisan db:seed --class=PromoCodeSeeder
```

---

## Notes importantes

- Les codes promo sont insensibles à la casse (convertis en majuscules)
- Un utilisateur ne peut utiliser un code promo que s'il respecte toutes les conditions
- Les codes promo expirés ou désactivés ne peuvent pas être utilisés
- Le système vérifie automatiquement les limites d'utilisation globales et par utilisateur




