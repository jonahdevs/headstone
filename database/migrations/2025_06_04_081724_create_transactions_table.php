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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('cascade');

            $table->string('transaction_id')->unique()->nullable();
            $table->string('reference_id')->unique();
            $table->decimal('amount', 10, 2);
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('description')->nullable();
            $table->string('currency')->nullable();
            $table->enum('status', ['abandoned', 'failed', 'ongoing', 'pending', 'processing', 'queued', 'reversed', 'success'])->default('pending');

            $table->string('payment_receipt')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
