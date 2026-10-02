<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        Schema::table('links', function (Blueprint $table) {
            $table->boolean('disabled')->default(false)->after('expires_at');
            $table->string('disabled_reason')->nullable()->after('disabled');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });

        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn(['disabled', 'disabled_reason']);
        });
    }
};
