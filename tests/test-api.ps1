# Script PowerShell de test pour tous les endpoints de l'API GloShop
# Usage: .\test-api.ps1

$BASE_URL = "http://localhost:8000/api"
$USER_TOKEN = ""
$ADMIN_TOKEN = ""

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "Test des endpoints API GloShop" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Fonction pour tester un endpoint
function Test-Endpoint {
    param(
        [string]$Method,
        [string]$Endpoint,
        [string]$Data = "",
        [string]$Description,
        [bool]$RequiresAuth = $false,
        [string]$Token = ""
    )
    
    Write-Host -NoNewline "Test: $Description ... "
    
    if ($RequiresAuth -and -not $Token) {
        Write-Host "SKIP (token manquant)" -ForegroundColor Yellow
        return $false
    }
    
    $headers = @{
        "Content-Type" = "application/json"
        "Accept" = "application/json"
    }
    
    if ($RequiresAuth -and $Token) {
        $headers["Authorization"] = "Bearer $Token"
    }
    
    try {
        if ($Method -eq "GET") {
            $response = Invoke-RestMethod -Uri "$BASE_URL$Endpoint" -Method Get -Headers $headers -ErrorAction Stop
            $statusCode = 200
        }
        elseif ($Method -eq "POST") {
            $response = Invoke-RestMethod -Uri "$BASE_URL$Endpoint" -Method Post -Headers $headers -Body $Data -ErrorAction Stop
            $statusCode = 200
        }
        elseif ($Method -eq "PUT") {
            $response = Invoke-RestMethod -Uri "$BASE_URL$Endpoint" -Method Put -Headers $headers -Body $Data -ErrorAction Stop
            $statusCode = 200
        }
        elseif ($Method -eq "DELETE") {
            $response = Invoke-RestMethod -Uri "$BASE_URL$Endpoint" -Method Delete -Headers $headers -ErrorAction Stop
            $statusCode = 200
        }
        
        Write-Host "OK (HTTP $statusCode)" -ForegroundColor Green
        return $true
    }
    catch {
        $statusCode = $_.Exception.Response.StatusCode.value__
        Write-Host "FAIL (HTTP $statusCode)" -ForegroundColor Red
        return $false
    }
}

# ==========================================
# Tests des endpoints publics
# ==========================================
Write-Host "=== Tests des endpoints publics ===" -ForegroundColor Yellow
Write-Host ""

# Test register
$timestamp = [DateTimeOffset]::UtcNow.ToUnixTimeSeconds()
Test-Endpoint -Method "POST" -Endpoint "/auth/register" `
    -Data "{`"name`":`"Test User`",`"email`":`"test$timestamp@example.com`",`"password`":`"password123`",`"password_confirmation`":`"password123`"}" `
    -Description "Register"

# Test login user
Write-Host -NoNewline "Test: Login user ... "
try {
    $loginData = @{
        email = "test@example.com"
        password = "password123"
    } | ConvertTo-Json
    
    $loginResponse = Invoke-RestMethod -Uri "$BASE_URL/auth/login" -Method Post `
        -Headers @{"Content-Type"="application/json"; "Accept"="application/json"} `
        -Body $loginData -ErrorAction Stop
    
    if ($loginResponse.data.token) {
        $USER_TOKEN = $loginResponse.data.token
        Write-Host "OK" -ForegroundColor Green
    }
    else {
        Write-Host "FAIL" -ForegroundColor Red
    }
}
catch {
    Write-Host "FAIL" -ForegroundColor Red
}

# Test login admin
Write-Host -NoNewline "Test: Login admin ... "
try {
    $adminLoginData = @{
        email = "admin@gloshop.com"
        password = "password123"
    } | ConvertTo-Json
    
    $adminLoginResponse = Invoke-RestMethod -Uri "$BASE_URL/admin/login" -Method Post `
        -Headers @{"Content-Type"="application/json"; "Accept"="application/json"} `
        -Body $adminLoginData -ErrorAction Stop
    
    if ($adminLoginResponse.data.token) {
        $ADMIN_TOKEN = $adminLoginResponse.data.token
        Write-Host "OK" -ForegroundColor Green
    }
    else {
        Write-Host "SKIP (créer un admin d'abord)" -ForegroundColor Yellow
    }
}
catch {
    Write-Host "SKIP (créer un admin d'abord)" -ForegroundColor Yellow
}

# Test categories
Test-Endpoint -Method "GET" -Endpoint "/categories" -Description "Get all categories"
Test-Endpoint -Method "GET" -Endpoint "/categories/robes" -Description "Get category by slug"

# Test products
Test-Endpoint -Method "GET" -Endpoint "/products" -Description "Get all products"
Test-Endpoint -Method "GET" -Endpoint "/products?category_id=1&featured=1&page=1" -Description "Get products with filters"
Test-Endpoint -Method "GET" -Endpoint "/products/test-product" -Description "Get product by slug"

# ==========================================
# Tests des endpoints protégés (user)
# ==========================================
if ($USER_TOKEN) {
    Write-Host ""
    Write-Host "=== Tests des endpoints protégés (user) ===" -ForegroundColor Yellow
    Write-Host ""
    
    # Auth
    Test-Endpoint -Method "GET" -Endpoint "/auth/user" -Description "Get current user" -RequiresAuth $true -Token $USER_TOKEN
    Test-Endpoint -Method "PUT" -Endpoint "/auth/profile" -Data "{`"name`":`"Updated Name`"}" -Description "Update profile" -RequiresAuth $true -Token $USER_TOKEN
    
    # Cart
    Test-Endpoint -Method "GET" -Endpoint "/cart" -Description "Get cart" -RequiresAuth $true -Token $USER_TOKEN
    Test-Endpoint -Method "POST" -Endpoint "/cart" -Data "{`"product_id`":1,`"quantity`":2,`"size`":`"M`",`"color`":`"Red`"}" -Description "Add to cart" -RequiresAuth $true -Token $USER_TOKEN
    
    # Orders
    Test-Endpoint -Method "GET" -Endpoint "/orders" -Description "Get orders" -RequiresAuth $true -Token $USER_TOKEN
    
    # Favorites
    Test-Endpoint -Method "GET" -Endpoint "/favorites" -Description "Get favorites" -RequiresAuth $true -Token $USER_TOKEN
    Test-Endpoint -Method "POST" -Endpoint "/favorites" -Data "{`"product_id`":1}" -Description "Add to favorites" -RequiresAuth $true -Token $USER_TOKEN
    
    # Addresses
    Test-Endpoint -Method "GET" -Endpoint "/addresses" -Description "Get addresses" -RequiresAuth $true -Token $USER_TOKEN
    
    # Payment methods
    Test-Endpoint -Method "GET" -Endpoint "/payment-methods" -Description "Get payment methods" -RequiresAuth $true -Token $USER_TOKEN
}
else {
    Write-Host ""
    Write-Host "=== Tests des endpoints protégés (user) - SKIP (pas de token) ===" -ForegroundColor Yellow
}

# ==========================================
# Tests des endpoints admin
# ==========================================
if ($ADMIN_TOKEN) {
    Write-Host ""
    Write-Host "=== Tests des endpoints admin ===" -ForegroundColor Yellow
    Write-Host ""
    
    # Dashboard
    Test-Endpoint -Method "GET" -Endpoint "/admin/dashboard/stats" -Description "Get dashboard stats" -RequiresAuth $true -Token $ADMIN_TOKEN
    
    # Orders
    Test-Endpoint -Method "GET" -Endpoint "/admin/orders" -Description "Get all orders (admin)" -RequiresAuth $true -Token $ADMIN_TOKEN
    Test-Endpoint -Method "GET" -Endpoint "/admin/orders?status=pending&page=1" -Description "Get orders with filters" -RequiresAuth $true -Token $ADMIN_TOKEN
    Test-Endpoint -Method "GET" -Endpoint "/admin/orders/1" -Description "Get order by ID" -RequiresAuth $true -Token $ADMIN_TOKEN
    Test-Endpoint -Method "PUT" -Endpoint "/admin/orders/1/status" -Data "{`"status`":`"processing`"}" -Description "Update order status" -RequiresAuth $true -Token $ADMIN_TOKEN
    
    # Products
    Test-Endpoint -Method "GET" -Endpoint "/admin/products" -Description "Get all products (admin)" -RequiresAuth $true -Token $ADMIN_TOKEN
    Test-Endpoint -Method "GET" -Endpoint "/admin/products?category_id=1&page=1" -Description "Get products with filters" -RequiresAuth $true -Token $ADMIN_TOKEN
    Test-Endpoint -Method "GET" -Endpoint "/admin/products/1" -Description "Get product by ID" -RequiresAuth $true -Token $ADMIN_TOKEN
    
    # Categories
    Test-Endpoint -Method "GET" -Endpoint "/admin/categories" -Description "Get all categories (admin)" -RequiresAuth $true -Token $ADMIN_TOKEN
    
    # Users
    Test-Endpoint -Method "GET" -Endpoint "/admin/users" -Description "Get all users" -RequiresAuth $true -Token $ADMIN_TOKEN
    Test-Endpoint -Method "GET" -Endpoint "/admin/users?search=test&page=1" -Description "Get users with search" -RequiresAuth $true -Token $ADMIN_TOKEN
    Test-Endpoint -Method "GET" -Endpoint "/admin/users/1" -Description "Get user by ID" -RequiresAuth $true -Token $ADMIN_TOKEN
    
    # Statistics
    Test-Endpoint -Method "GET" -Endpoint "/admin/statistics/sales-by-period" -Description "Get sales by period" -RequiresAuth $true -Token $ADMIN_TOKEN
    Test-Endpoint -Method "GET" -Endpoint "/admin/statistics/top-selling-products" -Description "Get top selling products" -RequiresAuth $true -Token $ADMIN_TOKEN
    Test-Endpoint -Method "GET" -Endpoint "/admin/statistics/stock" -Description "Get stock statistics" -RequiresAuth $true -Token $ADMIN_TOKEN
}
else {
    Write-Host ""
    Write-Host "=== Tests des endpoints admin - SKIP (pas de token admin) ===" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "Tests terminés" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan

