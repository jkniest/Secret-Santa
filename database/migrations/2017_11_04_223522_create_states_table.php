<?php

use App\Models\State;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('states', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('value')->nullable();
            $table->timestamps();
        });

        State::create([
            'name'  => 'bot',
            'value' => State::STOPPED
        ]);
        State::create([
            'name'  => 'announcement_id',
            'value' => null
        ]);
        State::create([
            'name'  => 'announcement_channel',
            'value' => null
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('states');
    }
}
