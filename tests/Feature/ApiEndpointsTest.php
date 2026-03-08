<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Address;
use App\Models\PaymentMethod;
use App\Models\Wishlist;
use App\Models\Promotion;
use App\Models\PromoCode;
use App\Models\PaymentTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected $baseUrl = 'http://localhost:8000/api';
    protected $userToken = null;
    protected $adminToken = null;
    protected $user = null;
    protected $admin = null;
    protected $category = null;
    protected $product = null;
    protected $order = null;
    protected $promotion = null;
    protected $promoCode = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Create admin user
        $this->admin = User::factory()->create([
            'email' => 'admin@gloshop.com',
            'password' => Hash::make('password123'),
            'is_admin' => true,
        ]);

        // Create test category
        $this->category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'description' => 'Test category description',
            'is_active' => true,
        ]);

        // Create test product
        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'Test product description',
            'price' => 10000,
            'discount_price' => 8000,
            'sku' => 'TEST-001',
            'stock' => 50,
            'images' => ['https://example.com/image.jpg'],
            'sizes' => ['S', 'M', 'L'],
            'colors' => ['Red', 'Blue'],
            'is_featured' => true,
            'is_active' => true,
        ]);

        // Create test order
        $this->order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'ORD-TEST-001',
            'subtotal_amount' => 20000,
            'discount_amount' => 0,
            'total_amount' => 20000,
            'status' => 'pending',
            'payment_status' => 'paid',
            'payment_method' => 'card',
            'shipping_address' => '123 Test Street',
            'shipping_name' => 'Test User',
            'shipping_phone' => '+225123456789',
            'shipping_email' => 'test@example.com',
        ]);

        OrderItem::create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'price' => 10000,
            'quantity' => 2,
            'size' => 'M',
            'color' => 'Red',
            'image' => 'https://example.com/image.jpg',
        ]);

        // Active promotion for public API
        $this->promotion = Promotion::create([
            'title' => 'Test Promotion',
            'description' => 'Test promotion description',
            'type' => 'percentage',
            'discount_value' => 10,
            'min_purchase_amount' => 5000,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'is_active' => true,
        ]);

        // Active promo code for tests
        $this->promoCode = PromoCode::create([
            'code' => 'TEST10',
            'name' => 'Test 10%',
            'type' => 'percentage',
            'discount_value' => 10,
            'min_purchase_amount' => 1000,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
            'is_active' => true,
        ]);
    }

    /**
     * Test public authentication endpoints
     */
    public function test_public_auth_endpoints(): void
    {
        // Test register
        $response = $this->postJson('/api/auth/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertStatus(201);
        $response->assertJsonStructure(['success', 'data' => ['user', 'token']]);

        // Test login
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['user', 'token']]);
        $this->userToken = $response->json('data.token');
    }

    /**
     * Test public category endpoints
     */
    public function test_public_category_endpoints(): void
    {
        // Test get all categories
        $response = $this->getJson('/api/categories');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test get category by slug
        $response = $this->getJson('/api/categories/test-category');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
    }

    /**
     * Test public promotions endpoints
     */
    public function test_public_promotions_endpoints(): void
    {
        $response = $this->getJson('/api/promotions');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        $response = $this->getJson('/api/promotions/' . $this->promotion->id);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
    }

    /**
     * Test public product endpoints
     */
    public function test_public_product_endpoints(): void
    {
        // Test get all products
        $response = $this->getJson('/api/products');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test get products with filters
        $response = $this->getJson('/api/products?category_id=' . $this->category->id . '&featured=1&page=1');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test get product by slug
        $response = $this->getJson('/api/products/test-product');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
    }

    /**
     * Test protected auth endpoints
     */
    public function test_protected_auth_endpoints(): void
    {
        // Login first
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('data.token');

        // Test get current user
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/auth/user');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test update profile
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/auth/profile', [
                'name' => 'Updated Name',
                'phone' => '+225987654321',
            ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test change password
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/auth/password', [
                'current_password' => 'password123',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message']);

        // Test logout
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/auth/logout');
        $response->assertStatus(200);
    }

    /**
     * Test cart endpoints
     */
    public function test_cart_endpoints(): void
    {
        // Login first
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('data.token');

        // Test get cart
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/cart');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test add to cart
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/cart', [
                'product_id' => $this->product->id,
                'quantity' => 2,
                'size' => 'M',
                'color' => 'Red',
            ]);
        $response->assertStatus(201);
        $response->assertJsonStructure(['success', 'data']);
        $cartId = $response->json('data.id');

        // Test update cart
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/cart/' . $cartId, [
                'quantity' => 3,
            ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test delete cart
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/cart/' . $cartId);
        $response->assertStatus(200);
    }

    /**
     * Test order endpoints
     */
    public function test_order_endpoints(): void
    {
        // Login first
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('data.token');

        // Add item to cart first
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/cart', [
                'product_id' => $this->product->id,
                'quantity' => 1,
            ]);

        // Test get orders
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/orders');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test create order
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/orders', [
                'shipping_address' => '123 Test Street',
                'shipping_name' => 'Test User',
                'shipping_phone' => '+225123456789',
                'shipping_email' => 'test@example.com',
                'payment_method' => 'card',
            ]);
        $response->assertStatus(201);
        $response->assertJsonStructure(['success', 'data']);
        $orderId = $response->json('data.id');

        // Test get order details
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/orders/' . $orderId);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
    }

    /**
     * Test favorites endpoints
     */
    public function test_favorites_endpoints(): void
    {
        // Login first
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('data.token');

        // Test get favorites
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/favorites');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test add to favorites
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/favorites', [
                'product_id' => $this->product->id,
            ]);
        $response->assertStatus(201);
        $response->assertJsonStructure(['success', 'data']);
        $favoriteId = $response->json('data.id');

        // Test check favorite
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/favorites/check', [
                'product_id' => $this->product->id,
            ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'is_favorite']);

        // Test delete favorite
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/favorites/' . $favoriteId);
        $response->assertStatus(200);
    }

    /**
     * Test addresses endpoints
     */
    public function test_addresses_endpoints(): void
    {
        // Login first
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('data.token');

        // Test get addresses
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/addresses');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test create address
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/addresses', [
                'title' => 'Home',
                'full_name' => 'Test User',
                'phone' => '+225123456789',
                'address' => '123 Test Street',
                'city' => 'Abidjan',
                'postal_code' => '01',
                'country' => 'Côte d\'Ivoire',
                'is_default' => true,
            ]);
        $response->assertStatus(201);
        $response->assertJsonStructure(['success', 'data']);
        $addressId = $response->json('data.id');

        // Test update address
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/addresses/' . $addressId, [
                'title' => 'Work',
            ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test set default address
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/addresses/' . $addressId . '/set-default');
        $response->assertStatus(200);

        // Test delete address
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/addresses/' . $addressId);
        $response->assertStatus(200);
    }

    /**
     * Test payment methods endpoints
     */
    public function test_payment_methods_endpoints(): void
    {
        // Login first
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('data.token');

        // Test get payment methods
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/payment-methods');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test create payment method (card)
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/payment-methods', [
                'type' => 'card',
                'card_number' => '1234567890123456',
                'card_holder' => 'Test User',
                'expiry_date' => '12/25',
                'cvv' => '123',
                'is_default' => true,
            ]);
        $response->assertStatus(201);
        $response->assertJsonStructure(['success', 'data']);
        $paymentMethodId = $response->json('data.id');

        // Test update payment method
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/payment-methods/' . $paymentMethodId, [
                'card_holder' => 'Updated Name',
            ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test set default payment method
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/payment-methods/' . $paymentMethodId . '/set-default');
        $response->assertStatus(200);

        // Test delete payment method
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/payment-methods/' . $paymentMethodId);
        $response->assertStatus(200);
    }

    /**
     * Test promo-codes endpoints (validate, available)
     */
    public function test_promo_codes_endpoints(): void
    {
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('data.token');

        // Validate valid code
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/promo-codes/validate', [
                'code' => 'TEST10',
                'amount' => 5000,
            ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['promo_code', 'discount_amount', 'final_amount']]);

        // Validate invalid code
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/promo-codes/validate', [
                'code' => 'INVALID',
                'amount' => 5000,
            ]);
        $response->assertStatus(404);

        // Get available promo codes
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/promo-codes/available');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
    }

    /**
     * Test payments endpoints (auth required, validation)
     */
    public function test_payments_endpoints(): void
    {
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('data.token');

        // PayGate initiate: missing params -> 422
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/payments/paygate/initiate', []);
        $response->assertStatus(422);

        // PayGate initiate: valid payload, mock Http to avoid real call
        Http::fake([
            'paygateglobal.com/*' => Http::response(['status' => 0, 'tx_reference' => 'TX123'], 200),
        ]);
        $orderPending = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'ORD-PAY-001',
            'subtotal_amount' => 5000,
            'discount_amount' => 0,
            'total_amount' => 5000,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'paygate',
            'shipping_address' => '123 Test',
            'shipping_name' => 'Test',
            'shipping_phone' => '+225123456789',
            'shipping_email' => 'test@example.com',
        ]);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/payments/paygate/initiate', [
                'order_id' => $orderPending->id,
                'phone_number' => '0700000000',
                'network' => 'FLOOZ',
            ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['tx_reference', 'transaction_id', 'order_id']]);

        // Check payment status: missing params -> 422
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/payments/paygate/check-status', []);
        $response->assertStatus(422);
    }

    /**
     * Test PayGate callback (public, no auth)
     */
    public function test_paygate_callback(): void
    {
        // Missing required fields -> 400
        $response = $this->postJson('/api/payments/paygate/callback', []);
        $response->assertStatus(400);
        $response->assertJson(['success' => false, 'message' => 'Missing required fields']);

        // Unknown transaction -> 404
        $response = $this->postJson('/api/payments/paygate/callback', [
            'tx_reference' => 'UNKNOWN-TX',
            'identifier' => 'UNKNOWN-ORD',
            'status' => 0,
        ]);
        $response->assertStatus(404);
    }

    /**
     * Test admin authentication endpoints
     */
    public function test_admin_auth_endpoints(): void
    {
        // Test admin login
        $response = $this->postJson('/api/admin/login', [
            'email' => 'admin@gloshop.com',
            'password' => 'password123',
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['user', 'token']]);
        $this->adminToken = $response->json('data.token');

        // Test admin logout
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->adminToken)
            ->postJson('/api/admin/logout');
        $response->assertStatus(200);
    }

    /**
     * Test admin dashboard endpoints
     */
    public function test_admin_dashboard_endpoints(): void
    {
        // Login as admin
        $loginResponse = $this->postJson('/api/admin/login', [
            'email' => 'admin@gloshop.com',
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('data.token');

        // Test get dashboard stats
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/dashboard/stats');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
    }

    /**
     * Test admin order endpoints
     */
    public function test_admin_order_endpoints(): void
    {
        // Login as admin
        $loginResponse = $this->postJson('/api/admin/login', [
            'email' => 'admin@gloshop.com',
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('data.token');

        // Test get all orders
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/orders');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test get orders with filters
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/orders?status=pending&payment_status=paid&page=1&per_page=10');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test get order by ID
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/orders/' . $this->order->id);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test update order status
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/admin/orders/' . $this->order->id . '/status', [
                'status' => 'processing',
            ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message', 'data']);
    }

    /**
     * Test admin product endpoints
     */
    public function test_admin_product_endpoints(): void
    {
        // Login as admin
        $loginResponse = $this->postJson('/api/admin/login', [
            'email' => 'admin@gloshop.com',
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('data.token');

        // Test get all products
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/products');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test get products with filters
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/products?category_id=' . $this->category->id . '&featured=1&search=test&page=1&per_page=10');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test get product by ID
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/products/' . $this->product->id);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test create product
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/admin/products', [
                'category_id' => $this->category->id,
                'name' => 'New Product',
                'description' => 'New product description',
                'price' => 15000,
                'discount_price' => 12000,
                'sku' => 'NEW-001',
                'stock' => 30,
                'images' => ['https://example.com/new-image.jpg'],
                'sizes' => ['S', 'M'],
                'colors' => ['Green'],
                'is_featured' => false,
                'is_active' => true,
            ]);
        $response->assertStatus(201);
        $response->assertJsonStructure(['success', 'message', 'data']);
        $newProductId = $response->json('data.id');

        // Test update product
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/admin/products/' . $newProductId, [
                'name' => 'Updated Product',
                'price' => 18000,
            ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message', 'data']);

        // Test delete product
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/admin/products/' . $newProductId);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message']);
    }

    /**
     * Test admin category endpoints
     */
    public function test_admin_category_endpoints(): void
    {
        // Login as admin
        $loginResponse = $this->postJson('/api/admin/login', [
            'email' => 'admin@gloshop.com',
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('data.token');

        // Test get all categories
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/categories');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test create category
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/admin/categories', [
                'name' => 'New Category',
                'description' => 'New category description',
                'image' => 'https://example.com/category.jpg',
                'is_active' => true,
            ]);
        $response->assertStatus(201);
        $response->assertJsonStructure(['success', 'message', 'data']);
        $newCategoryId = $response->json('data.id');

        // Test update category
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/admin/categories/' . $newCategoryId, [
                'name' => 'Updated Category',
                'description' => 'Updated description',
            ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message', 'data']);

        // Test delete category
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/admin/categories/' . $newCategoryId);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message']);
    }

    /**
     * Test admin user endpoints
     */
    public function test_admin_user_endpoints(): void
    {
        // Login as admin
        $loginResponse = $this->postJson('/api/admin/login', [
            'email' => 'admin@gloshop.com',
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('data.token');

        // Test get all users
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/users');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test get users with search
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/users?search=test&page=1&per_page=10');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test get user by ID
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/users/' . $this->user->id);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
    }

    /**
     * Test admin statistics endpoints
     */
    public function test_admin_statistics_endpoints(): void
    {
        // Login as admin
        $loginResponse = $this->postJson('/api/admin/login', [
            'email' => 'admin@gloshop.com',
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('data.token');

        // Test get sales by period
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/statistics/sales-by-period');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'by_day',
                'by_week',
                'by_month',
            ],
        ]);

        // Test get top selling products
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/statistics/top-selling-products');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        // Test get stock statistics
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/statistics/stock');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'total_stock_value',
                'total_products',
                'total_units',
                'by_product',
            ],
        ]);
    }

    /**
     * Test admin promotions endpoints (CRUD)
     */
    public function test_admin_promotions_endpoints(): void
    {
        $loginResponse = $this->postJson('/api/admin/login', [
            'email' => 'admin@gloshop.com',
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('data.token');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/promotions');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/promotions/' . $this->promotion->id);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/admin/promotions', [
                'title' => 'New Promotion',
                'description' => 'New promo',
                'type' => 'percentage',
                'discount_value' => 15,
                'min_purchase_amount' => 3000,
                'start_date' => now()->addDay()->format('Y-m-d'),
                'end_date' => now()->addMonths(2)->format('Y-m-d'),
                'is_active' => true,
            ]);
        $response->assertStatus(201);
        $response->assertJsonStructure(['success', 'message', 'data']);
        $newPromoId = $response->json('data.id');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/admin/promotions/' . $newPromoId, [
                'title' => 'Updated Promotion',
                'description' => 'Updated',
                'type' => 'percentage',
                'discount_value' => 20,
                'min_purchase_amount' => 3000,
                'start_date' => now()->addDay()->format('Y-m-d'),
                'end_date' => now()->addMonths(2)->format('Y-m-d'),
                'is_active' => true,
            ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message', 'data']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/admin/promotions/' . $newPromoId);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message']);
    }

    /**
     * Test admin promo-codes endpoints (CRUD)
     */
    public function test_admin_promo_codes_endpoints(): void
    {
        $loginResponse = $this->postJson('/api/admin/login', [
            'email' => 'admin@gloshop.com',
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('data.token');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/promo-codes');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/promo-codes/' . $this->promoCode->id);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/admin/promo-codes', [
                'code' => 'ADMIN20',
                'name' => 'Admin 20%',
                'type' => 'percentage',
                'discount_value' => 20,
                'min_purchase_amount' => 2000,
                'start_date' => now()->addDay()->format('Y-m-d'),
                'end_date' => now()->addMonths(2)->format('Y-m-d'),
                'is_active' => true,
            ]);
        $response->assertStatus(201);
        $response->assertJsonStructure(['success', 'message', 'data']);
        $newCodeId = $response->json('data.id');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/admin/promo-codes/' . $newCodeId, [
                'code' => 'ADMIN25',
                'name' => 'Admin 25%',
                'type' => 'percentage',
                'discount_value' => 25,
                'min_purchase_amount' => 2000,
                'start_date' => now()->addDay()->format('Y-m-d'),
                'end_date' => now()->addMonths(2)->format('Y-m-d'),
                'is_active' => true,
            ]);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message', 'data']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/admin/promo-codes/' . $newCodeId);
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'message']);
    }

    /**
     * Test admin upload images endpoint (no file -> 400)
     */
    public function test_admin_upload_images(): void
    {
        $loginResponse = $this->postJson('/api/admin/login', [
            'email' => 'admin@gloshop.com',
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('data.token');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/admin/products/upload-images', []);
        $response->assertStatus(400);
        $response->assertJson(['success' => false, 'message' => 'Aucune image fournie']);
    }
}

