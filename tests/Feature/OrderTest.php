<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['is_admin' => true]);
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_view_orders_list()
    {
        $response = $this->get('/admin/orders');
        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_create_an_order()
    {
        $customer = Customer::factory()->create();
        
        $orderData = [
            'order_number' => 'SO-' . date('Ymd') . '-0001',
            'customer_id' => $customer->id,
            'order_date' => now()->format('Y-m-d'),
            'delivery_date' => now()->addDays(7)->format('Y-m-d'),
            'status' => 'pending',
            'notes' => 'Test order notes',
        ];

        $response = $this->post('/admin/orders', $orderData);
        
        $this->assertDatabaseHas('orders', [
            'order_number' => $orderData['order_number'],
            'customer_id' => $customer->id,
        ]);
    }

    /** @test */
    public function it_can_show_an_order()
    {
        $order = Order::factory()->create();
        
        $response = $this->get('/admin/orders/' . $order->id);
        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_update_an_order()
    {
        $order = Order::factory()->create();
        $customer = Customer::factory()->create();
        
        $updatedData = [
            'customer_id' => $customer->id,
            'order_date' => now()->format('Y-m-d'),
            'delivery_date' => now()->addDays(14)->format('Y-m-d'),
            'status' => 'processing',
            'notes' => 'Updated order notes',
        ];

        $response = $this->put('/admin/orders/' . $order->id, $updatedData);
        
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'customer_id' => $customer->id,
            'status' => 'processing',
        ]);
    }

    /** @test */
    public function it_can_delete_an_order()
    {
        $order = Order::factory()->create();
        
        $response = $this->delete('/admin/orders/' . $order->id);
        
        $this->assertSoftDeleted('orders', [
            'id' => $order->id,
        ]);
    }
}
