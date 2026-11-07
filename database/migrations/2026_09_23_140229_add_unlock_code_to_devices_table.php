<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (
            Blueprint $table
        ) {
            $table
                ->string(
                    'unlock_code_hash'
                )
                ->nullable()
                ->after('unlock_pin');

            $table
                ->timestamp(
                    'unlock_code_expires_at'
                )
                ->nullable()
                ->after(
                    'unlock_code_hash'
                );
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (
            Blueprint $table
        ) {
            $table->dropColumn([
                'unlock_code_hash',
                'unlock_code_expires_at',
            ]);
        });
    }
};