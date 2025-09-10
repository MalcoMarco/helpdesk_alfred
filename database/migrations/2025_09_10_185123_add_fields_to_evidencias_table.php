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
        Schema::table('evidencias', function (Blueprint $table) {
            $table->string('original_name')->nullable()->after('name');
            $table->string('mime_type')->nullable()->after('path');
            $table->bigInteger('size')->nullable()->after('mime_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evidencias', function (Blueprint $table) {
            $table->dropColumn(['original_name', 'mime_type', 'size']);
        });
    }
};
