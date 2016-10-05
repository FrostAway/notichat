<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotifyTbl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification', function (Blueprint $table) {
           $table->engine = 'InnoDB';
           $table->increments('id');
           $table->unsignedInteger('user_id');
           $table->string('title')->nullable();
           $table->text('content');
           $table->unsignedInteger('object_id')->nullable();
           $table->string('notify_type', 20);
           $table->boolean('is_read')->default(0);
           $table->timestamp('read_at')->nullable();
           $table->timestamps();
           $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification');
    }
}
