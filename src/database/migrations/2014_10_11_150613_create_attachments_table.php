<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->nullableMorphs('attachable');
            $table->string('file_attachment')->index();
            $table->string('name');
            $table->string('path');
            $table->integer('size');
            $table->string('type');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropMorphs('attachable');
            $table->dropIndex(['file_attachment']);
        });

        Schema::dropIfExists('attachments');
    }
}
