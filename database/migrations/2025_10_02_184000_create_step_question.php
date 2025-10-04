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
        Schema::create('step_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_step_id')->constrained('steps')->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(1);
            $table->timestamps();

            $table->unique(['assessment_step_id', 'question_id']);
            $table->index(['assessment_step_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('step_questions');
    }
};
