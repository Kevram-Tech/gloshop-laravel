#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script pour tester tous les endpoints de l'API GloShop
Usage: python test_api_endpoints.py
"""

import requests
import json
import sys
from typing import Dict, Optional, Tuple

# Configurer l'encodage UTF-8 pour Windows
if sys.platform == 'win32':
    import io
    sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')
    sys.stderr = io.TextIOWrapper(sys.stderr.buffer, encoding='utf-8')

BASE_URL = "http://31.97.185.5:8002/api"

# Couleurs pour l'affichage
class Colors:
    GREEN = '\033[92m'
    RED = '\033[91m'
    YELLOW = '\033[93m'
    BLUE = '\033[94m'
    RESET = '\033[0m'

def print_success(message: str):
    print(f"{Colors.GREEN}[OK] {message}{Colors.RESET}")

def print_error(message: str):
    print(f"{Colors.RED}[ERREUR] {message}{Colors.RESET}")

def print_warning(message: str):
    print(f"{Colors.YELLOW}[ATTENTION] {message}{Colors.RESET}")

def print_info(message: str):
    print(f"{Colors.BLUE}[INFO] {message}{Colors.RESET}")

def test_endpoint(method: str, url: str, headers: Optional[Dict] = None, 
                 data: Optional[Dict] = None, expected_status = 200,
                 description: str = "") -> Tuple[bool, Optional[Dict]]:
    """Teste un endpoint et retourne le résultat"""
    try:
        if method.upper() == 'GET':
            response = requests.get(url, headers=headers, timeout=10)
        elif method.upper() == 'POST':
            response = requests.post(url, headers=headers, json=data, timeout=10)
        elif method.upper() == 'PUT':
            response = requests.put(url, headers=headers, json=data, timeout=10)
        elif method.upper() == 'DELETE':
            response = requests.delete(url, headers=headers, timeout=10)
        else:
            print_error(f"Méthode HTTP non supportée: {method}")
            return False, None

        # Support pour un statut unique ou une liste de statuts acceptables
        if isinstance(expected_status, list):
            status_ok = response.status_code in expected_status
        else:
            status_ok = response.status_code == expected_status
        try:
            response_data = response.json()
        except:
            response_data = {"raw": response.text[:200]}

        if status_ok:
            print_success(f"{method} {url} - Status: {response.status_code}")
            if description:
                print_info(f"  → {description}")
            return True, response_data
        else:
            print_error(f"{method} {url} - Status: {response.status_code} (attendu: {expected_status})")
            if description:
                print_info(f"  → {description}")
            if response_data:
                print_warning(f"  Réponse: {json.dumps(response_data, indent=2)[:200]}")
            return False, response_data

    except requests.exceptions.RequestException as e:
        print_error(f"{method} {url} - Erreur: {str(e)}")
        if description:
            print_info(f"  → {description}")
        return False, None

def main():
    print("\n" + "="*70)
    print("TEST DE TOUS LES ENDPOINTS DE L'API GLOSHOP")
    print("="*70 + "\n")

    results = {
        "public": {"success": 0, "failed": 0},
        "protected": {"success": 0, "failed": 0},
        "admin": {"success": 0, "failed": 0}
    }

    # ============================================
    # 1. ROUTES PUBLIQUES (sans authentification)
    # ============================================
    print(f"\n{Colors.BLUE}{'='*70}{Colors.RESET}")
    print(f"{Colors.BLUE}1. ROUTES PUBLIQUES{Colors.RESET}")
    print(f"{Colors.BLUE}{'='*70}{Colors.RESET}\n")

    # Auth - Register
    print_info("Test d'inscription (peut échouer si l'email existe déjà)")
    register_data = {
        "name": "Test User",
        "email": "test@example.com",
        "password": "password123",
        "password_confirmation": "password123"
    }
    success, register_response = test_endpoint(
        "POST", f"{BASE_URL}/auth/register", 
        data=register_data, expected_status=201,
        description="Inscription d'un nouvel utilisateur"
    )
    if success:
        results["public"]["success"] += 1
    else:
        results["public"]["failed"] += 1

    # Auth - Login
    print_info("\nTest de connexion")
    login_data = {
        "email": "test@example.com",
        "password": "password123"
    }
    success, login_response = test_endpoint(
        "POST", f"{BASE_URL}/auth/login",
        data=login_data, expected_status=200,
        description="Connexion d'un utilisateur"
    )
    user_token = None
    if success and login_response and "token" in login_response:
        user_token = login_response.get("token")
        print_success(f"Token utilisateur obtenu: {user_token[:20]}...")
        results["public"]["success"] += 1
    else:
        results["public"]["failed"] += 1
        # Essayer avec des credentials par défaut
        print_warning("Tentative avec des credentials alternatifs...")
        login_data2 = {"email": "admin@gloshop.com", "password": "admin123"}
        success2, login_response2 = test_endpoint(
            "POST", f"{BASE_URL}/auth/login",
            data=login_data2, expected_status=200
        )
        if success2 and login_response2 and "token" in login_response2:
            user_token = login_response2.get("token")
            print_success(f"Token utilisateur obtenu: {user_token[:20]}...")
            results["public"]["success"] += 1
        else:
            results["public"]["failed"] += 1

    # Categories
    print_info("\nTest des catégories")
    success, _ = test_endpoint(
        "GET", f"{BASE_URL}/categories",
        description="Liste des catégories"
    )
    if success:
        results["public"]["success"] += 1
    else:
        results["public"]["failed"] += 1

    # Category by slug
    success, _ = test_endpoint(
        "GET", f"{BASE_URL}/categories/electronics",
        expected_status=[200, 404],
        description="Détails d'une catégorie par slug"
    )
    if success:
        results["public"]["success"] += 1
    else:
        results["public"]["failed"] += 1

    # Products
    print_info("\nTest des produits")
    success, products_response = test_endpoint(
        "GET", f"{BASE_URL}/products",
        description="Liste des produits"
    )
    product_slug = None
    if success:
        results["public"]["success"] += 1
        # Récupérer un slug de produit pour le test suivant
        if products_response and "data" in products_response:
            products = products_response["data"]
            if products and len(products) > 0:
                product_slug = products[0].get("slug")
    else:
        results["public"]["failed"] += 1

    # Product by slug
    if product_slug:
        success, _ = test_endpoint(
            "GET", f"{BASE_URL}/products/{product_slug}",
            description=f"Détails du produit: {product_slug}"
        )
    else:
        success, _ = test_endpoint(
            "GET", f"{BASE_URL}/products/test-product",
            expected_status=[200, 404],
            description="Détails d'un produit par slug"
        )
    if success:
        results["public"]["success"] += 1
    else:
        results["public"]["failed"] += 1

    # ============================================
    # 2. ROUTES PROTÉGÉES (avec authentification)
    # ============================================
    print(f"\n{Colors.BLUE}{'='*70}{Colors.RESET}")
    print(f"{Colors.BLUE}2. ROUTES PROTÉGÉES (nécessitent un token){Colors.RESET}")
    print(f"{Colors.BLUE}{'='*70}{Colors.RESET}\n")

    if not user_token:
        print_error("Aucun token utilisateur disponible. Les tests protégés seront ignorés.")
        print_warning("Veuillez vous connecter manuellement pour obtenir un token.")
    else:
        headers = {
            "Authorization": f"Bearer {user_token}",
            "Content-Type": "application/json",
            "Accept": "application/json"
        }

        # Auth - User
        print_info("Test des endpoints d'authentification protégés")
        success, _ = test_endpoint(
            "GET", f"{BASE_URL}/auth/user",
            headers=headers,
            description="Informations de l'utilisateur connecté"
        )
        if success:
            results["protected"]["success"] += 1
        else:
            results["protected"]["failed"] += 1

        # Auth - Update Profile
        success, _ = test_endpoint(
            "PUT", f"{BASE_URL}/auth/profile",
            headers=headers,
            data={"name": "Test User Updated"},
            description="Mise à jour du profil"
        )
        if success:
            results["protected"]["success"] += 1
        else:
            results["protected"]["failed"] += 1

        # Cart
        print_info("\nTest du panier")
        success, _ = test_endpoint(
            "GET", f"{BASE_URL}/cart",
            headers=headers,
            description="Liste des articles du panier"
        )
        if success:
            results["protected"]["success"] += 1
        else:
            results["protected"]["failed"] += 1

        # Cart - Add item
        success, _ = test_endpoint(
            "POST", f"{BASE_URL}/cart",
            headers=headers,
            data={"product_id": 1, "quantity": 1},
            expected_status=[200, 201, 404],
            description="Ajout d'un article au panier"
        )
        if success:
            results["protected"]["success"] += 1
        else:
            results["protected"]["failed"] += 1

        # Orders
        print_info("\nTest des commandes")
        success, orders_response = test_endpoint(
            "GET", f"{BASE_URL}/orders",
            headers=headers,
            description="Liste des commandes"
        )
        order_id = None
        if success:
            results["protected"]["success"] += 1
            if orders_response and "data" in orders_response:
                orders = orders_response["data"]
                if orders and len(orders) > 0:
                    order_id = orders[0].get("id")
        else:
            results["protected"]["failed"] += 1

        # Order by ID
        if order_id:
            success, _ = test_endpoint(
                "GET", f"{BASE_URL}/orders/{order_id}",
                headers=headers,
                description=f"Détails de la commande {order_id}"
            )
        else:
            success, _ = test_endpoint(
                "GET", f"{BASE_URL}/orders/1",
                expected_status=[200, 404],
                description="Détails d'une commande"
            )
        if success:
            results["protected"]["success"] += 1
        else:
            results["protected"]["failed"] += 1

        # Favorites
        print_info("\nTest des favoris")
        success, _ = test_endpoint(
            "GET", f"{BASE_URL}/favorites",
            headers=headers,
            description="Liste des favoris"
        )
        if success:
            results["protected"]["success"] += 1
        else:
            results["protected"]["failed"] += 1

        success, _ = test_endpoint(
            "POST", f"{BASE_URL}/favorites",
            headers=headers,
            data={"product_id": 1},
            expected_status=[200, 201, 404],
            description="Ajout d'un produit aux favoris"
        )
        if success:
            results["protected"]["success"] += 1
        else:
            results["protected"]["failed"] += 1

        success, _ = test_endpoint(
            "POST", f"{BASE_URL}/favorites/check",
            headers=headers,
            data={"product_id": 1},
            description="Vérifier si un produit est en favoris"
        )
        if success:
            results["protected"]["success"] += 1
        else:
            results["protected"]["failed"] += 1

        # Addresses
        print_info("\nTest des adresses")
        success, _ = test_endpoint(
            "GET", f"{BASE_URL}/addresses",
            headers=headers,
            description="Liste des adresses"
        )
        if success:
            results["protected"]["success"] += 1
        else:
            results["protected"]["failed"] += 1

        success, address_response = test_endpoint(
            "POST", f"{BASE_URL}/addresses",
            headers=headers,
            data={
                "title": "Test Address",
                "full_name": "Test User",
                "address": "123 Test Street",
                "city": "Test City",
                "postal_code": "12345",
                "country": "Togo",
                "phone": "+22812345678"
            },
            expected_status=[200, 201],
            description="Création d'une nouvelle adresse"
        )
        address_id = None
        if success:
            results["protected"]["success"] += 1
            if address_response:
                if "data" in address_response:
                    address_id = address_response["data"].get("id")
                elif "id" in address_response:
                    address_id = address_response.get("id")
        else:
            results["protected"]["failed"] += 1

        # Payment Methods
        print_info("\nTest des moyens de paiement")
        success, _ = test_endpoint(
            "GET", f"{BASE_URL}/payment-methods",
            headers=headers,
            description="Liste des moyens de paiement"
        )
        if success:
            results["protected"]["success"] += 1
        else:
            results["protected"]["failed"] += 1

        # Payments
        print_info("\nTest des paiements")
        success, _ = test_endpoint(
            "POST", f"{BASE_URL}/payments/paygate/initiate",
            headers=headers,
            data={"order_id": 1, "amount": 1000},
            expected_status=[200, 400, 404],
            description="Initiation d'un paiement PayGate"
        )
        if success:
            results["protected"]["success"] += 1
        else:
            results["protected"]["failed"] += 1

        success, _ = test_endpoint(
            "POST", f"{BASE_URL}/payments/card/process",
            headers=headers,
            data={"order_id": 1, "card_number": "4111111111111111"},
            expected_status=[200, 400, 404],
            description="Traitement d'un paiement par carte"
        )
        if success:
            results["protected"]["success"] += 1
        else:
            results["protected"]["failed"] += 1

    # ============================================
    # 3. ROUTES ADMIN
    # ============================================
    print(f"\n{Colors.BLUE}{'='*70}{Colors.RESET}")
    print(f"{Colors.BLUE}3. ROUTES ADMIN{Colors.RESET}")
    print(f"{Colors.BLUE}{'='*70}{Colors.RESET}\n")

    # Admin Login
    print_info("Test de connexion admin")
    admin_login_data = {
        "email": "admin@gloshop.com",
        "password": "admin123"
    }
    success, admin_login_response = test_endpoint(
        "POST", f"{BASE_URL}/admin/login",
        data=admin_login_data, expected_status=200,
        description="Connexion admin"
    )
    admin_token = None
    if success and admin_login_response and "token" in admin_login_response:
        admin_token = admin_login_response.get("token")
        print_success(f"Token admin obtenu: {admin_token[:20]}...")
        results["admin"]["success"] += 1
    else:
        results["admin"]["failed"] += 1
        print_warning("Impossible de se connecter en tant qu'admin. Les tests admin seront ignorés.")

    if admin_token:
        admin_headers = {
            "Authorization": f"Bearer {admin_token}",
            "Content-Type": "application/json",
            "Accept": "application/json"
        }

        # Dashboard Stats
        print_info("\nTest du dashboard admin")
        success, _ = test_endpoint(
            "GET", f"{BASE_URL}/admin/dashboard/stats",
            headers=admin_headers,
            description="Statistiques du dashboard"
        )
        if success:
            results["admin"]["success"] += 1
        else:
            results["admin"]["failed"] += 1

        # Orders
        print_info("\nTest de la gestion des commandes")
        success, _ = test_endpoint(
            "GET", f"{BASE_URL}/admin/orders",
            headers=admin_headers,
            description="Liste des commandes (admin)"
        )
        if success:
            results["admin"]["success"] += 1
        else:
            results["admin"]["failed"] += 1

        # Products
        print_info("\nTest de la gestion des produits")
        success, _ = test_endpoint(
            "GET", f"{BASE_URL}/admin/products",
            headers=admin_headers,
            description="Liste des produits (admin)"
        )
        if success:
            results["admin"]["success"] += 1
        else:
            results["admin"]["failed"] += 1

        # Categories
        print_info("\nTest de la gestion des catégories")
        success, _ = test_endpoint(
            "GET", f"{BASE_URL}/admin/categories",
            headers=admin_headers,
            description="Liste des catégories (admin)"
        )
        if success:
            results["admin"]["success"] += 1
        else:
            results["admin"]["failed"] += 1

        # Users
        print_info("\nTest de la gestion des utilisateurs")
        success, _ = test_endpoint(
            "GET", f"{BASE_URL}/admin/users",
            headers=admin_headers,
            description="Liste des utilisateurs"
        )
        if success:
            results["admin"]["success"] += 1
        else:
            results["admin"]["failed"] += 1

        # Statistics
        print_info("\nTest des statistiques")
        success, _ = test_endpoint(
            "GET", f"{BASE_URL}/admin/statistics/sales-by-period",
            headers=admin_headers,
            description="Statistiques de ventes par période"
        )
        if success:
            results["admin"]["success"] += 1
        else:
            results["admin"]["failed"] += 1

        success, _ = test_endpoint(
            "GET", f"{BASE_URL}/admin/statistics/top-selling-products",
            headers=admin_headers,
            description="Top des produits les plus vendus"
        )
        if success:
            results["admin"]["success"] += 1
        else:
            results["admin"]["failed"] += 1

        success, _ = test_endpoint(
            "GET", f"{BASE_URL}/admin/statistics/stock",
            headers=admin_headers,
            description="Statistiques de stock"
        )
        if success:
            results["admin"]["success"] += 1
        else:
            results["admin"]["failed"] += 1

    # ============================================
    # RÉSUMÉ
    # ============================================
    print(f"\n{Colors.BLUE}{'='*70}{Colors.RESET}")
    print(f"{Colors.BLUE}RÉSUMÉ DES TESTS{Colors.RESET}")
    print(f"{Colors.BLUE}{'='*70}{Colors.RESET}\n")

    total_success = sum(r["success"] for r in results.values())
    total_failed = sum(r["failed"] for r in results.values())
    total = total_success + total_failed

    print(f"Routes publiques:    {Colors.GREEN}{results['public']['success']}{Colors.RESET} réussies, {Colors.RED}{results['public']['failed']}{Colors.RESET} échouées")
    print(f"Routes protégées:    {Colors.GREEN}{results['protected']['success']}{Colors.RESET} réussies, {Colors.RED}{results['protected']['failed']}{Colors.RESET} échouées")
    print(f"Routes admin:        {Colors.GREEN}{results['admin']['success']}{Colors.RESET} réussies, {Colors.RED}{results['admin']['failed']}{Colors.RESET} échouées")
    print(f"\nTotal:               {Colors.GREEN}{total_success}{Colors.RESET} réussies, {Colors.RED}{total_failed}{Colors.RESET} échouées sur {total} tests")

    if total_failed == 0:
        print(f"\n{Colors.GREEN}✓ Tous les tests sont passés avec succès!{Colors.RESET}\n")
    else:
        print(f"\n{Colors.YELLOW}⚠ Certains tests ont échoué. Vérifiez les détails ci-dessus.{Colors.RESET}\n")

if __name__ == "__main__":
    main()

