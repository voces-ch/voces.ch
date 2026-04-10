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
        Schema::table('signatures', function (Blueprint $table) {
            $table->dropColumn('is_verified');
            $table->timestamp('verified_at')->nullable();
            $table->string('verification_token')->nullable()->unique();
            $table->dateTime('token_expiration')->nullable();
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->boolean('is_email_verification_enabled')->default(false);
            $table->string('email_verification_field')->nullable();
            $table->string('verification_success_action')->nullable();
            $table->json('verification_success_message')->nullable();
            $table->json('verification_success_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signatures', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false);
            $table->dropColumn(['verified_at', 'verification_token', 'token_expiration']);
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['is_email_verification_enabled', 'email_verification_field', 'verification_success_action', 'verification_success_message', 'verification_success_url']);
        });
    }
};

