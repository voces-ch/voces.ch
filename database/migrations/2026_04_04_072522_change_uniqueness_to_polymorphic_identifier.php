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
        Schema::table('campaign_fields', function (Blueprint $table) {
            $table->boolean('is_unique')->default(false)->after('is_required');
        });

        Schema::table('signatures', function (Blueprint $table) {
            $table->renameColumn('email', 'unique_identifier');

            $table->dropUnique('unique_campaign_email_org');
            $table->unique(
                ['campaign_id', 'unique_identifier', 'organization_id'],
                'unique_campaign_identifier_org'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaign_fields', function (Blueprint $table) {
            $table->dropColumn('is_unique');
        });

        Schema::table('signatures', function (Blueprint $table) {
            $table->renameColumn('unique_identifier', 'email');
            $table->dropUnique('unique_campaign_identifier_org');
            $table->unique(
                ['campaign_id', 'email', 'organization_id'],
                'unique_campaign_email_org'
            );

        });
    }
};
