<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login()
    {
        $response = $this->get('/products');
        $response->assertRedirect('/login');
    }

    public function test_kasir_cannot_access_users_management()
    {
        $kasir = User::factory()->create(['role' => 'kasir', 'is_active' => true]);

        $response = $this->actingAs($kasir)->get('/users');
        $response->assertStatus(403);
    }

    public function test_admin_can_add_category()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $response = $this->actingAs($admin)->post('/categories', [
            'name' => 'Minuman Segar',
            'description' => 'Kategori minuman',
        ]);

        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', ['name' => 'Minuman Segar']);
    }

    public function test_sale_transaction_reduces_stock_correctly()
    {
        $kasir = User::factory()->create(['role' => 'kasir', 'is_active' => true]);
        $category = Category::create(['name' => 'Snack']);
        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'SNK-01',
            'name' => 'Chiki',
            'unit' => 'pcs',
            'purchase_price' => 1000,
            'selling_price' => 2000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->actingAs($kasir)->post('/sales', [
            'payment_method' => 'tunai',
            'paid_amount' => 5000,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ]
            ]
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8
        ]);
    }
}