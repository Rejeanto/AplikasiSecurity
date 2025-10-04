<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessment_responses', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('assessment_id');
            $table->integer('question_id');
            $table->integer('answer_text')->nullable();
            $table->foreignId('step_id')->nullable()->constrained('question_options')->onDelete('cascade')->onUpdate('cascade')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assessment_responses');
    }
};
