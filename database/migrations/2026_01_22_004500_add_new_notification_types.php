<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Change enum to string type to allow any notification type
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('tipe', 50)->change();
        });
    }

    public function down(): void
    {
        // Revert to original enum values (note: this may lose new type data)
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('tipe', 50)->change();
        });
    }
};
