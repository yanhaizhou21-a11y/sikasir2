<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_cash_transaction_creates_orders_and_receipt(): void
    {
        $this->seed();

        $user = User::factory()->create();
        $user->assignRole('kasir');

        $foodCategory = Category::factory()->create(['name' => 'Makanan']);
        $drinkCategory = Category::factory()->create(['name' => 'Minuman']);

        $food = Product::factory()->create(['category_id' => $foodCategory->id, 'harga_modal' => 1000, 'harga_jual' => 10000]);
        $drink = Product::factory()->create(['category_id' => $drinkCategory->id, 'harga_modal' => 1000, 'harga_jual' => 8000]);

        $payload = [
            'products' => [
                ['product_id' => $food->id, 'qty' => 1],
                ['product_id' => $drink->id, 'qty' => 2],
            ],
            'payment_method' => 'cash',
        ];

        $response = $this->actingAs($user)->post(route('kasir.transaksi.store'), $payload);
        $response->assertStatus(302);

        $this->assertDatabaseHas('transactions', ['payment_method' => 'cash', 'status' => 'paid']);

        $transaction = Transaction::latest('id')->first();
        $this->assertNotNull($transaction);

        // Orders created for kitchen and bar
        $this->assertDatabaseHas('orders', ['transaction_id' => $transaction->id, 'destination' => 'kitchen']);
        $this->assertDatabaseHas('orders', ['transaction_id' => $transaction->id, 'destination' => 'bar']);

        // Receipt generated
        $this->assertNotNull($transaction->fresh()->receipt);
    }
}


