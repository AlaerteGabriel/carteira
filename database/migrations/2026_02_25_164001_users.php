<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up(): void
    {
        Schema::create('fi_users', function (Blueprint $table) {
            //$table->id();
            $table->increments('us_id');
            $table->string('us_nome', length: 80);
            $table->date('us_dt_nascimento')->nullable();
            $table->bigInteger('us_cpf', false, true)->nullable()->index();
            $table->string('us_email', length: 80)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->char('us_password', 60);
            $table->enum('us_status', ['1','2'])->index()->default(1);
            $table->enum('us_acc_encerrada', ['1','2'])->index()->default(2);
            $table->string('us_remember_token', 100)->nullable();
            $table->decimal('us_balanco',15, 2)->default(0)->nullable();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', length:80)->primary();
            $table->string('token')->index();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        DB::statement("ALTER TABLE fi_users CHANGE us_cpf us_cpf bigint(11) UNSIGNED ZEROFILL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fi_users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');

    }
};
