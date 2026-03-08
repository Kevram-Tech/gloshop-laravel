# Configuration PayGate Callback

## URL de Callback

L'URL de callback PayGate pour cette application est :

```
http://72.60.188.146:6500/api/payments/paygate/callback
```

## Configuration dans PayGate

1. Connectez-vous à votre compte PayGate Global
2. Allez dans les paramètres de votre compte
3. Configurez l'URL de callback webhook avec l'URL ci-dessus
4. Assurez-vous que la méthode HTTP est **POST**

## Format des données reçues

PayGate enverra les données suivantes au callback :

```json
{
    "tx_reference": "TX123456789",
    "identifier": "ORD-ABC123XYZ",
    "status": 0,
    "payment_reference": "REF123456",
    "datetime": "2026-01-10 12:00:00",
    "payment_method": "FLOOZ",
    "phone_number": "+22890123456"
}
```

### Codes de statut PayGate

- `0` : Paiement réussi
- `2` : En attente
- `4` : Expiré
- `6` : Annulé

## Test du callback

Pour tester le callback, vous pouvez utiliser curl :

```bash
curl -X POST http://72.60.188.146:6500/api/payments/paygate/callback \
  -H "Content-Type: application/json" \
  -d '{
    "tx_reference": "TEST123",
    "identifier": "ORD-TEST123",
    "status": 0,
    "payment_reference": "TEST-REF",
    "datetime": "2026-01-10 12:00:00",
    "payment_method": "FLOOZ",
    "phone_number": "+22890123456"
  }'
```

## Sécurité

- Le callback est accessible publiquement (pas d'authentification requise)
- La sécurité est assurée par la validation des transactions existantes
- Les logs sont enregistrés pour le débogage
- La prévention des doublons est implémentée

## Logs

Tous les callbacks sont enregistrés dans les logs Laravel :
- Succès : `storage/logs/laravel.log`
- Erreurs : `storage/logs/laravel.log` avec niveau ERROR






