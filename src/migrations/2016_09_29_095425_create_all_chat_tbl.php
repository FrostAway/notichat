<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllChatTbl extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('rooms', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->unique();
            $table->integer('owner_id')->unsigned();
            $table->string('type', 20)->default('room');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('users_rooms', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('user_id')->unsigned();
            $table->integer('room_id')->unsigned();
            $table->integer('role_id')->default(1);
            $table->primary(['user_id', 'room_id']);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->text('message');
            $table->integer('from_user_id')->unsigned();
            $table->string('from_user_name');
            $table->integer('to_user_id')->unsigned()->nullable();
            $table->string('to_user_name')->nullable();
            $table->integer('room_id')->unsigned()->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->foreign('from_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('users_rooms');
        Schema::dropIfExists('messages');
    }

}
