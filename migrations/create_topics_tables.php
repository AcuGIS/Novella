<?php

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->create('topics', function ($table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->foreignId('created_by')->constrained('users');
    $table->foreignId('updated_by')->constrained('users');
    $table->timestamps();
});

Capsule::schema()->create('dataset_topics', function ($table) {
    $table->id();
    $table->foreignId('dataset_id')->constrained('datasets')->onDelete('cascade');
    $table->foreignId('topic_id')->constrained('topics')->onDelete('cascade');
    $table->timestamps();

    $table->unique(['dataset_id', 'topic_id']);
}); 