<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('signatures', function (Blueprint $table) {
            $table->string('email')->after('organization_id')->nullable();
            $table->unique(
                ['campaign_id', 'email', 'organization_id'],
                'unique_campaign_email_org'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signatures', function (Blueprint $table) {
            $table->dropUnique('unique_campaign_email_org');
            $table->dropColumn('email');
        });
    }
};
