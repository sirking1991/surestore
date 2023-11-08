<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductOption;
use App\Models\Store;
use App\Models\StoreFront;
use App\Models\User;
use Database\Factories\OrderItemFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Users
        User::factory(50)->create();

        User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
        ]);

        // Stores
        Store::factory(20)->create();        

        // for each store
        foreach (Store::all() as $store) {

            StoreFront::factory(1)->create(['store_id' => $store->id]);

            // create product category
            ProductCategory::factory(rand(5, 10))->create(['store_id' => $store->id]);
            
            // for each product category, create product
            foreach (ProductCategory::where('store_id',$store->id)->get() as $productCategory) {
                Product::factory(rand(0,50))
                    ->create([
                        'store_id' => $store->id,
                        'category_id' => $productCategory->id,
                    ]);
            }

            // for each product, create product options
            foreach (Product::where('store_id',$store->id)->get() as $product) {
                if(rand(0,1)) {
                    // randomly generate options
                    ProductOption::factory(1)->create([
                        'product_id' => $product->id
                    ]);
                }
            }

            // create order
            Order::factory(rand(3, 50))->create(['store_id' => $store->id]);
            // foreach order,  create order items 
            foreach (Order::where('store_id', $store->id)->get() as $order) {
                for ($i=0; $i < rand(0,10) ; $i++) { 
                    $product = Product::where('store_id', $store->id)
                        ->inRandomOrder()
                        ->first();
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'base_price' => $product->price
                    ]);
                }
                
            }

        }

        // setup mystore
        $store = Store::inRandomOrder()->first();
        $categories = ProductCategory::where('store_id', $store->id)->inRandomOrder()->limit(3)->get()->pluck('id')->toArray();
        $store->update(['slug'=>'mystore']);
        $storeFront = StoreFront::whereStoreId($store->id)->whereStatus('active')->first();
        $storeFront->update([
            'month_category' => $categories
        ]);
    }
}
