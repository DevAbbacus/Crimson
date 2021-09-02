<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSyncStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_statuses', function (Blueprint $table) {
            $table->id();

            // * Attributes
            $table->unsignedInteger('status')->default(0);
            $table->timestamp('last_sync')->useCurrent();

            // * Relationships
            $table->unsignedInteger('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sync_statuses');
    }
}
