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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
}

