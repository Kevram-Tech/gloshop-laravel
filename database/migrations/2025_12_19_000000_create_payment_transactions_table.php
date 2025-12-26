<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('payment_method'); // paygate_flooz, paygate_tmoney, card_visa, etc.
            $table->decimal('amount', 10, 2);
            $table->string('tx_reference')->nullable(); // Référence PayGateGlobal
            $table->string('identifier')->nullable(); // Identifiant interne (order_number)
            $table->string('payment_reference')->nullable(); // Référence de paiement Flooz/TMoney
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->json('metadata')->nullable(); // Données supplémentaires
            $table->timestamps();

            $table->index('tx_reference');
            $table->index('identifier');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};

