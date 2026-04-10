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
            $table->dropColumn('signature_goal');
            $table->boolean('has_goal')->default(false)->after('submit_label');
            $table->integer('goal')->nullable()->after('has_goal');
            $table->string('goal_type')->nullable()->after('goal');
            $table->string('goal_field')->nullable()->after('goal_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['goal', 'goal_type', 'goal_field']);
            $table->integer('signature_goal')->nullable()->after('campaign_description');
        });
    }
};
