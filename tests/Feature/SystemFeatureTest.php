<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Installment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_products()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.products.store'), [
                'name' => 'Test product',
                'price' => 500,
                'stock' => 10,
                'description' => 'Product description',
            ])
            ->assertRedirect(route('admin.products.index'));

        $product = Product::first();

        $this->assertNotNull($product);

        $this->actingAs($admin)
            ->put(route('admin.products.update', $product), [
                'name' => 'Updated product',
                'price' => 600,
                'stock' => 5,
                'description' => 'Updated description',
            ])
            ->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertEquals('Updated product', $product->name);

        $this->actingAs($admin)
            ->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->assertCount(0, Product::all());
    }

    public function test_user_can_manage_own_customers_and_cannot_delete_others()
    {
        $user1 = User::factory()->create(['role' => 'user']);
        $user2 = User::factory()->create(['role' => 'user']);

        $this->actingAs($user1)
            ->post(route('customers.store'), [
                'name' => 'Customer A',
                'phone' => '0123456789',
                'address' => 'Phnom Penh',
            ])
            ->assertRedirect(route('customers.index'));

        $customer = Customer::first();
        $this->assertEquals($user1->id, $customer->created_by);

        $this->actingAs($user1)
            ->put(route('customers.update', $customer), [
                'name' => 'Customer A Updated',
                'phone' => '0987654321',
                'address' => 'Siem Reap',
            ])
            ->assertRedirect(route('customers.index'));

        $customer->refresh();
        $this->assertEquals('Customer A Updated', $customer->name);

        $this->actingAs($user2)
            ->delete(route('customers.destroy', $customer))
            ->assertStatus(403);

        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
    }

    public function test_installment_ownership_and_cancellation()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = Customer::create(['name' => 'Customer Test', 'created_by' => $admin->id]);
        $product = Product::create(['name' => 'Product Test', 'price' => 100, 'stock' => 10, 'description' => 'desc']);

        $this->actingAs($admin)
            ->post(route('installments.store'), [
                'customer_id' => $customer->id,
                'product_id' => $product->id,
                'total_price' => 2000,
                'down_payment' => 200,
                'interest_rate' => 5,
                'duration_months' => 12,
            ])
            ->assertRedirect(route('installments.index'));

        $installment = Installment::first();
        $this->assertEquals('active', $installment->status);

        $this->actingAs($admin)
            ->delete(route('installments.destroy', $installment))
            ->assertRedirect(route('installments.index'));

        $installment->refresh();
        $this->assertEquals('cancelled', $installment->status);
    }

    public function test_telegram_webhook_updates_customer_telegram_id()
    {
        $customer = Customer::create(['name' => 'Telegram User', 'created_by' => User::factory()->create()->id]);

        $response = $this->post('/api/telegram/webhook', [
            'message' => [
                'chat' => ['id' => 12345],
                'text' => '/start',
            ],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'telegram_id' => 12345]);
    }
}
