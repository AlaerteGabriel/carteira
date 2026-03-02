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
        //
        Schema::create('fi_transacoes', function (Blueprint $table) {
            $table->increments('tr_id');

            // Relacionamento com fi_users (Quem iniciou/é dono da transação)
            $table->unsignedInteger('tr_us_id')->index();

            // Para transferências entre usuários, guardamos o ID do destinatário
            $table->unsignedInteger('tr_pagador_id')->nullable()->index();
            $table->unsignedInteger('tr_beneficiario_id')->nullable()->index();

            $table->unsignedInteger('tr_estorno_id')->nullable()->index();

            $table->decimal('tr_valor', 15, 2)->default(0.00);
            $table->enum('tr_tipo', ['deposito', 'transferencia', 'cancelamento'])->index();
            $table->string('tr_descricao')->nullable();
            // Status para controle visual
            $table->boolean('tr_estornado')->default(false);
            $table->timestamps();

            $table->foreign('tr_us_id')->references('us_id')->on('fi_users')->onDelete('cascade');
            $table->foreign('tr_pagador_id')->references('us_id')->on('fi_users');
            $table->foreign('tr_beneficiario_id')->references('us_id')->on('fi_users');
            $table->foreign('tr_estorno_id')->references('tr_id')->on('fi_transacoes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('fi_transacoes');
    }
};
