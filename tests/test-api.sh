#!/bin/bash

# Script de test pour tous les endpoints de l'API GloShop
# Usage: ./test-api.sh

BASE_URL="http://localhost:8000/api"
USER_TOKEN=""
ADMIN_TOKEN=""

echo "=========================================="
echo "Test des endpoints API GloShop"
echo "=========================================="
echo ""

# Couleurs pour les résultats
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Fonction pour tester un endpoint
test_endpoint() {
    local method=$1
    local endpoint=$2
    local data=$3
    local description=$4
    local requires_auth=${5:-false}
    local token=${6:-""}
    
    echo -n "Test: $description ... "
    
    if [ "$requires_auth" = true ] && [ -z "$token" ]; then
        echo -e "${RED}SKIP (token manquant)${NC}"
        return 1
    fi
    
    if [ "$method" = "GET" ]; then
        if [ "$requires_auth" = true ]; then
            response=$(curl -s -w "\n%{http_code}" -X GET "$BASE_URL$endpoint" \
                -H "Authorization: Bearer $token" \
                -H "Content-Type: application/json" \
                -H "Accept: application/json")
        else
            response=$(curl -s -w "\n%{http_code}" -X GET "$BASE_URL$endpoint" \
                -H "Content-Type: application/json" \
                -H "Accept: application/json")
        fi
    elif [ "$method" = "POST" ]; then
        if [ "$requires_auth" = true ]; then
            response=$(curl -s -w "\n%{http_code}" -X POST "$BASE_URL$endpoint" \
                -H "Authorization: Bearer $token" \
                -H "Content-Type: application/json" \
                -H "Accept: application/json" \
                -d "$data")
        else
            response=$(curl -s -w "\n%{http_code}" -X POST "$BASE_URL$endpoint" \
                -H "Content-Type: application/json" \
                -H "Accept: application/json" \
                -d "$data")
        fi
    elif [ "$method" = "PUT" ]; then
        response=$(curl -s -w "\n%{http_code}" -X PUT "$BASE_URL$endpoint" \
            -H "Authorization: Bearer $token" \
            -H "Content-Type: application/json" \
            -H "Accept: application/json" \
            -d "$data")
    elif [ "$method" = "DELETE" ]; then
        response=$(curl -s -w "\n%{http_code}" -X DELETE "$BASE_URL$endpoint" \
            -H "Authorization: Bearer $token" \
            -H "Content-Type: application/json" \
            -H "Accept: application/json")
    fi
    
    http_code=$(echo "$response" | tail -n1)
    body=$(echo "$response" | sed '$d')
    
    if [ "$http_code" -ge 200 ] && [ "$http_code" -lt 300 ]; then
        echo -e "${GREEN}OK (HTTP $http_code)${NC}"
        return 0
    else
        echo -e "${RED}FAIL (HTTP $http_code)${NC}"
        echo "Response: $body" | head -c 200
        echo ""
        return 1
    fi
}

# ==========================================
# Tests des endpoints publics
# ==========================================
echo -e "${YELLOW}=== Tests des endpoints publics ===${NC}"
echo ""

# Test register
test_endpoint "POST" "/auth/register" \
    '{"name":"Test User","email":"test'$(date +%s)'@example.com","password":"password123","password_confirmation":"password123"}' \
    "Register"

# Test login user
echo -n "Test: Login user ... "
login_response=$(curl -s -X POST "$BASE_URL/auth/login" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"email":"test@example.com","password":"password123"}')
USER_TOKEN=$(echo $login_response | grep -o '"token":"[^"]*' | cut -d'"' -f4)
if [ -n "$USER_TOKEN" ]; then
    echo -e "${GREEN}OK${NC}"
else
    echo -e "${RED}FAIL${NC}"
fi

# Test login admin
echo -n "Test: Login admin ... "
admin_login_response=$(curl -s -X POST "$BASE_URL/admin/login" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{"email":"admin@gloshop.com","password":"password123"}')
ADMIN_TOKEN=$(echo $admin_login_response | grep -o '"token":"[^"]*' | cut -d'"' -f4)
if [ -n "$ADMIN_TOKEN" ]; then
    echo -e "${GREEN}OK${NC}"
else
    echo -e "${YELLOW}SKIP (créer un admin d'abord)${NC}"
fi

# Test categories
test_endpoint "GET" "/categories" "" "Get all categories"
test_endpoint "GET" "/categories/robes" "" "Get category by slug"

# Test products
test_endpoint "GET" "/products" "" "Get all products"
test_endpoint "GET" "/products?category_id=1&featured=1&page=1" "" "Get products with filters"
test_endpoint "GET" "/products/test-product" "" "Get product by slug"

# ==========================================
# Tests des endpoints protégés (user)
# ==========================================
if [ -n "$USER_TOKEN" ]; then
    echo ""
    echo -e "${YELLOW}=== Tests des endpoints protégés (user) ===${NC}"
    echo ""
    
    # Auth
    test_endpoint "GET" "/auth/user" "" "Get current user" true "$USER_TOKEN"
    test_endpoint "PUT" "/auth/profile" '{"name":"Updated Name"}' "Update profile" true "$USER_TOKEN"
    
    # Cart
    test_endpoint "GET" "/cart" "" "Get cart" true "$USER_TOKEN"
    test_endpoint "POST" "/cart" '{"product_id":1,"quantity":2,"size":"M","color":"Red"}' "Add to cart" true "$USER_TOKEN"
    
    # Orders
    test_endpoint "GET" "/orders" "" "Get orders" true "$USER_TOKEN"
    
    # Favorites
    test_endpoint "GET" "/favorites" "" "Get favorites" true "$USER_TOKEN"
    test_endpoint "POST" "/favorites" '{"product_id":1}' "Add to favorites" true "$USER_TOKEN"
    
    # Addresses
    test_endpoint "GET" "/addresses" "" "Get addresses" true "$USER_TOKEN"
    
    # Payment methods
    test_endpoint "GET" "/payment-methods" "" "Get payment methods" true "$USER_TOKEN"
else
    echo ""
    echo -e "${YELLOW}=== Tests des endpoints protégés (user) - SKIP (pas de token) ===${NC}"
fi

# ==========================================
# Tests des endpoints admin
# ==========================================
if [ -n "$ADMIN_TOKEN" ]; then
    echo ""
    echo -e "${YELLOW}=== Tests des endpoints admin ===${NC}"
    echo ""
    
    # Dashboard
    test_endpoint "GET" "/admin/dashboard/stats" "" "Get dashboard stats" true "$ADMIN_TOKEN"
    
    # Orders
    test_endpoint "GET" "/admin/orders" "" "Get all orders (admin)" true "$ADMIN_TOKEN"
    test_endpoint "GET" "/admin/orders?status=pending&page=1" "" "Get orders with filters" true "$ADMIN_TOKEN"
    test_endpoint "GET" "/admin/orders/1" "" "Get order by ID" true "$ADMIN_TOKEN"
    test_endpoint "PUT" "/admin/orders/1/status" '{"status":"processing"}' "Update order status" true "$ADMIN_TOKEN"
    
    # Products
    test_endpoint "GET" "/admin/products" "" "Get all products (admin)" true "$ADMIN_TOKEN"
    test_endpoint "GET" "/admin/products?category_id=1&page=1" "" "Get products with filters" true "$ADMIN_TOKEN"
    test_endpoint "GET" "/admin/products/1" "" "Get product by ID" true "$ADMIN_TOKEN"
    
    # Categories
    test_endpoint "GET" "/admin/categories" "" "Get all categories (admin)" true "$ADMIN_TOKEN"
    
    # Users
    test_endpoint "GET" "/admin/users" "" "Get all users" true "$ADMIN_TOKEN"
    test_endpoint "GET" "/admin/users?search=test&page=1" "" "Get users with search" true "$ADMIN_TOKEN"
    test_endpoint "GET" "/admin/users/1" "" "Get user by ID" true "$ADMIN_TOKEN"
    
    # Statistics
    test_endpoint "GET" "/admin/statistics/sales-by-period" "" "Get sales by period" true "$ADMIN_TOKEN"
    test_endpoint "GET" "/admin/statistics/top-selling-products" "" "Get top selling products" true "$ADMIN_TOKEN"
    test_endpoint "GET" "/admin/statistics/stock" "" "Get stock statistics" true "$ADMIN_TOKEN"
else
    echo ""
    echo -e "${YELLOW}=== Tests des endpoints admin - SKIP (pas de token admin) ===${NC}"
fi

echo ""
echo "=========================================="
echo "Tests terminés"
echo "=========================================="

