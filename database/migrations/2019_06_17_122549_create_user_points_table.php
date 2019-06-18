<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_point_votes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('channel_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('voter_id')->unsigned();
            $table->boolean('is_upvote');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['channel_id', 'user_id']);
            $table->index(['channel_id', 'voter_id']);

            $table->foreign('channel_id')->references('id')->on('channels');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('voter_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_point_votes');
    }
}
