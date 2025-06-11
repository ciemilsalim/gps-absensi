<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIzinsTable extends Migration
{
    public function up()
    {
        Schema::create('izins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('jenis', ['izin', 'cuti']);
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason');
            $table->string('document_path')->nullable(); // untuk bukti dokumen
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('izins');
    }
}
