<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
               User::firstOrCreate(
            ['email' => 'admin@lubosmart.com'],
            [
                'name' => 'Super Admin',
                'password' => 'admin123',
                'role' => 'superadmin',
                'status' => 'approved',
            ]
        );

        User::firstOrCreate(
            ['email' => 'logistics@lubosmart.com'],
            [
                'name' => 'SPX Express',
                'password' => 'logistics123',
                'role' => 'logistics',
                'status' => 'approved',
            ]
        );

        User::firstOrCreate(
            ['email' => 'rider@lubosmart.com'],
            [
                'name' => 'Mark Villareal',
                'password' => 'rider123',
                'role' => 'rider',
                'status' => 'matched', // skips pending-approval + choose-company screens
            ]
        );

        User::firstOrCreate(
            ['email' => 'buyer@lubosmart.com'],
            [
                'name' => 'Maria Santos',
                'password' => 'buyer123',
                'role' => 'buyer',
                'status' => 'approved',
            ]
        );

        User::firstOrCreate(
            ['email' => 'seller@lubosmart.com'],
            [
                'name' => "Anna's Boutique",
                'password' => 'seller123',
                'role' => 'seller',
                'status' => 'approved',
            ]
        );
        // 'Product name' => [image file, options the buyer can choose from (or null)]
        $categories = [
            'Fashion' => [
                'Denim Jacket' => ['denim-jacket.jpg', ['Color' => ['Light Wash', 'Dark Wash'], 'Size' => ['S', 'M', 'L', 'XL']]],
                'Graphic Tee' => ['graphic-tee.jpg', ['Color' => ['Black', 'White', 'Navy'], 'Size' => ['S', 'M', 'L', 'XL']]],
            ],
            'Electronics' => [
                'Wireless Earbuds' => ['wireless-headphones.jpg', ['Color' => ['Black', 'White']]],
                'Power Bank' => ['portable-power-bank.jpg', ['Capacity' => ['10,000 mAh', '20,000 mAh']]],
            ],
            'Beauty' => [
                'Vitamin C Serum' => ['vitamin-c-serum.jpg', null],
            ],
            'Home & Living' => [
                'Ceramic Mug Set' => ['ceramic-mug-set.jpg', ['Set' => ['2 pieces', '4 pieces']]],
            ],
            'Accessories' => [
                'Leather Wallet' => ['minimal-leather-bag.jpg', ['Color' => ['Brown', 'Black']]],
                'Canvas Tote Bag' => ['everyday-tote-bag.jpg', ['Color' => ['Natural', 'Black']]],
            ],
        ];

        foreach ($categories as $catName => $items) {
            $category = Category::firstOrCreate(['name' => $catName]);

            foreach ($items as $itemName => [$image, $variations]) {
                $product = Product::firstOrCreate(
                    ['category_id' => $category->id, 'name' => $itemName],
                    [
                        'description' => "A great {$itemName} from the {$catName} category.",
                        'price' => rand(150, 2500),
                        'stock' => rand(10, 100),
                    ]
                );

                // Only fills in image_path — won't overwrite price/stock you've
                // already edited, and won't wipe an image_path set by hand later.
                if ($image && ! $product->image_path) {
                    $product->update(['image_path' => "products/{$image}"]);
                }

                // Same idea for options: only fills them in when the product has none yet.
                if ($variations && ! $product->variations) {
                    $product->update(['variations' => $variations]);
                }
            }
        }
    }
}