<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->decimal('cleanliness',    3, 1)->nullable()->after('rating');
            $table->decimal('service_score',  3, 1)->nullable()->after('cleanliness');
            $table->decimal('location_score', 3, 1)->nullable()->after('service_score');
            $table->decimal('value_score',    3, 1)->nullable()->after('location_score');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['cleanliness', 'service_score', 'location_score', 'value_score']);
        });
    }
};
