<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // What a seller offers, e.g. {"Color": ["Black", "White"], "Size": ["S", "M", "L"]}.
        // Null / empty = the product has no options to choose from.
        Schema::table('products', function (Blueprint $table) {
            $table->json('variations')->nullable()->after('image_path');
        });

        // What the buyer picked, stored as readable text, e.g. "Color: Black, Size: M".
        Schema::table('cart_items', function (Blueprint $table) {
            $table->string('variation')->nullable()->after('quantity');
        });

        // Copied from the cart when the order is placed, so the seller knows what to pack.
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('variation')->nullable()->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('variations');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('variation');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('variation');
        });
    }
};
