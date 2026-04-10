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
            Schema::table('campaigns', function (Blueprint $table) {
                $table->boolean('is_data_pooled')->default(false)->after('organization_id');
            });
        });

        Schema::table('signatures', function (Blueprint $table) {
            $table->foreignId('is_duplicate_of')->nullable()->constrained('signatures')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('is_data_pooled');
        });

        Schema::table('signatures', function (Blueprint $table) {
            $table->dropColumn('is_duplicate_of');
        });
    }
};
