<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('watchers', function (Blueprint $table) {
            $table->id();
            $table->string('practitioner_name');
            $table->integer('agenda_id');
            $table->string('motive_name');
            $table->integer('motive_id');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->json('notified_slots')->nullable();
            $table->timestamps();
        });
    }
};
