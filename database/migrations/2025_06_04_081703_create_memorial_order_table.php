<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('memorial_order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('memorial_id')->constrained()->onDelete('cascade');

            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2)->comment('price of memorial at order (per unit)');
            $table->decimal('discount', 10, 2)->nullable()->comment('discount of memorial at order (per unit)');
            $table->decimal('shipping_fee', 10, 2)->nullable();
            $table->decimal('tax', 10, 2)->nullable();
            $table->decimal('total', 10, 2);
            $table->enum('font', ['nunito', 'amatic', 'pacifico', 'dancing', 'satisfy'])->default('nunito');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->text('epitaph')->nullable();
            $table->timestamp('DOB')->nullable();
            $table->timestamp('DOD')->nullable();
            $table->text('instructions')->nullable();
            $table->string('image')->nullable();
            $table->enum('status', ['pending', 'shipped', 'delivered', 'returned'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memorial_order');
    }
};
