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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->json('title')->change();
            $table->json('slug')->change();
            $table->json('description')->nullable()->change();
            $table->json('languages');
        });

        Schema::table('campaign_fields', function (Blueprint $table) {
            $table->json('label')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('title')->change();
            $table->string('slug')->change();
            $table->text('description')->nullable()->change();
        });

        Schema::table('campaign_fields', function (Blueprint $table) {
            $table->string('name')->change();
            $table->string('label')->change();
        });
    }
};
