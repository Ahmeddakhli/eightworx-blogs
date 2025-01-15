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
        Schema::create('blogs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->enum('post_type', ['article', 'video', 'gallery'])->nullable();

            // Information
            $table->string('title_en')->nullable();
            $table->string('title_ar')->nullable();
            $table->string('sub_title_en')->nullable();
            $table->string('sub_title_ar')->nullable();
            $table->mediumText('short_description_en')->nullable();
            $table->mediumText('short_description_ar')->nullable();
            $table->mediumText('description_en')->nullable();
            $table->mediumText('description_ar')->nullable();
            $table->string('og_title_ar')->nullable();
            $table->string('og_title_en')->nullable();
            $table->text('og_description_ar')->nullable();
            $table->text('og_description_en')->nullable();
            $table->string('meta_keywords_en')->nullable();
            $table->string('meta_keywords_ar')->nullable();
            $table->integer('order')->default(1);
            $table->integer('views')->nullable();
            $table->boolean('is_featured')->default(true)->unsigned()->index();
            $table->boolean('is_published')->default(true)->unsigned()->index();
            $table->date('published_at')->nullable();
            $table->enum('media_type', ['url', 'iframe', 'video'])->nullable();
            $table->longText('media_data')->nullable();

            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('lookups')->onDelete('cascade');

            // SEO
            $table->string('slug')->nullable()->index();
            $table->mediumText('meta_title_en')->nullable();
            $table->mediumText('meta_title_ar')->nullable();
            $table->mediumText('meta_description_en')->nullable();
            $table->mediumText('meta_description_ar')->nullable();
            $table->boolean('is_index')->default(true)->unsigned()->index();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('created_at');
            $table->unsignedBigInteger('updated_by')->nullable()->after('updated_at');
            $table->unsignedBigInteger('deleted_by')->nullable()->after('deleted_at');

            // Foreign key relations for userstamps
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
