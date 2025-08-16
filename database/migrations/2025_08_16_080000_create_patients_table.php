<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (Schema::hasTable('patients')) {
            return;
        }
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('nic', 20);
            $table->date('dob');
            $table->string('gender');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->string('emergency_contact', 20);
            $table->string('address', 500);
            $table->string('district');
            $table->string('hospital');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('patients');
    }
};