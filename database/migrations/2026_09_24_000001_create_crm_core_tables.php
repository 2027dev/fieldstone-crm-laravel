<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('label')->nullable();
            $table->string('address')->nullable();
            $table->string('website')->nullable();
            $table->string('industry')->nullable();
            $table->unsignedInteger('employee_count')->nullable();
            $table->boolean('is_sample')->default(false);
            $table->timestamps();

            $table->index('name');
        });

        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('job_title')->nullable();
            $table->string('email')->nullable();
            $table->string('email_label')->default('Work');
            $table->string('phone')->nullable();
            $table->string('phone_label')->default('Work');
            $table->string('label')->nullable();
            $table->boolean('is_sample')->default(false);
            $table->timestamps();

            $table->index('name');
        });

        Schema::create('pipelines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pipeline_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('position')->default(0);
            $table->unsignedTinyInteger('probability')->default(100);
            $table->timestamps();
        });

        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pipeline_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stage_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->nullable()->constrained('people')->nullOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->decimal('value', 14, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->string('status')->default('open'); // open, won, lost
            $table->string('label')->nullable();
            $table->date('expected_close_date')->nullable();
            $table->timestamp('won_at')->nullable();
            $table->timestamp('lost_at')->nullable();
            $table->string('lost_reason')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_sample')->default(false);
            $table->timestamps();

            $table->index(['stage_id', 'status']);
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('person_id')->nullable()->constrained('people')->nullOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('converted_deal_id')->nullable()->constrained('deals')->nullOnDelete();
            $table->string('title');
            $table->decimal('value', 14, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('label')->nullable();
            $table->string('source')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->boolean('is_sample')->default(false);
            $table->timestamps();
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deal_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('person_id')->nullable()->constrained('people')->nullOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject');
            $table->string('type')->default('call'); // call, meeting, task, deadline, email, lunch
            $table->date('due_date')->nullable();
            $table->time('due_time')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->string('location')->nullable();
            $table->string('priority')->nullable(); // low, medium, high
            $table->string('outcome')->nullable();
            $table->text('note')->nullable();
            $table->boolean('done')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->boolean('is_sample')->default(false);
            $table->timestamps();

            $table->index(['done', 'due_date']);
        });

        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('deal_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->nullable()->constrained('people')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });

        Schema::create('email_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('person_id')->nullable()->constrained('people')->nullOnDelete();
            $table->foreignId('deal_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject');
            $table->string('folder')->default('inbox'); // inbox, sent, drafts, archived
            $table->boolean('is_read')->default(false);
            $table->boolean('is_starred')->default(false);
            $table->timestamp('last_message_at')->nullable();
            $table->boolean('is_sample')->default(false);
            $table->timestamps();
        });

        Schema::create('email_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_thread_id')->constrained()->cascadeOnDelete();
            $table->string('direction')->default('incoming'); // incoming, outgoing
            $table->string('from_name')->nullable();
            $table->string('from_email');
            $table->string('to_email');
            $table->text('body');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('setup_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('task_key');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'task_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setup_tasks');
        Schema::dropIfExists('email_messages');
        Schema::dropIfExists('email_threads');
        Schema::dropIfExists('notes');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('deals');
        Schema::dropIfExists('stages');
        Schema::dropIfExists('pipelines');
        Schema::dropIfExists('people');
        Schema::dropIfExists('organizations');
    }
};
