<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('gateways', function (Blueprint $table) {
            $table->string('id', 32)->primary('id');
            $table->unsignedInteger('sys_uptime');
            $table->unsignedInteger('sys_memfree');
            $table->float('sys_load', 5, 2);
            $table->unsignedInteger('wifidog_uptime');
            $table->timestamps();
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('gateways');
    }
};
