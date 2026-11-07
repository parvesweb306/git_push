<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {

            $table->string('manufacturer')
                ->nullable()
                ->after('device_name');

            $table->string('model')
                ->nullable()
                ->after('manufacturer');

            $table->string('android_version')
                ->nullable()
                ->after('model');

            $table->unsignedTinyInteger('battery_level')
                ->nullable()
                ->after('android_version');

            $table->decimal('latitude', 10, 7)
                ->nullable()
                ->after('battery_level');

            $table->decimal('longitude', 10, 7)
                ->nullable()
                ->after('latitude');

            $table->decimal('location_accuracy', 10, 2)
                ->nullable()
                ->after('longitude');

            $table->timestamp('location_updated_at')
                ->nullable()
                ->after('location_accuracy');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {

            $table->dropColumn([
                'manufacturer',
                'model',
                'android_version',
                'battery_level',
                'latitude',
                'longitude',
                'location_accuracy',
                'location_updated_at',
            ]);
        });
    }
};