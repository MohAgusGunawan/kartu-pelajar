<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiswasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id(); // Ini adalah id PK, dengan AI (Auto Increment)
            $table->integer('nis');
            $table->string('nama', 100);
            $table->string('ttl', 100);
            $table->enum('gender', ['Laki-laki', 'Perempuan']);
            $table->string('alamat', 50);
            $table->string('wa', 20);
            $table->string('kelas', 10);
            $table->string('foto', 255);
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
        Schema::dropIfExists('siswa');
    }
}
