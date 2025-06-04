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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('memorial_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->enum('font', ['nunito', 'amatic', 'pacifico', 'dancing', 'satisfy'])->default('nunito');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->text('epitaph')->nullable();
            $table->timestamp('DOB')->nullable();
            $table->timestamp('DOD')->nullable();
            $table->text('instructions')->nullable();
            $table->string('image')->nullable();
            $table->boolean('terms')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
