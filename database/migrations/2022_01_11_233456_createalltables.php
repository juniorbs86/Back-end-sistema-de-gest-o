<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Createalltables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { //criando o campo usuarios no banco, name,email,cpf,password
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique(); //unique significa que só pode ter 1 usuario com aquele email
            $table->string('cpf')->unique();
            $table->string('password');
        });
        //criando campo unidades no banco, name, id_owner(usuario da unidade)
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name'); //referencia ex: ap 101, lote 33 ...
            $table->integer('id_owner');
        });
        //criando campo peoples no banco, id_unit, name, brithdate
        Schema::create('unitpeoples', function (Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('name');
            $table->date('birthdate');
        });
        //criando campo vehicle no banco, id_unit, title, color, plate
        Schema::create('unitvehicles', function (Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('title');
            $table->string('color');
            $table->string('plate');
        });
        //criando campo pets no banco, id_unit, name, race
        Schema::create('unitpets', function (Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('name');
            $table->string('race');
        });
        //criando campo walls(mural de avisos)no banco, title, body, datecreated
        Schema::create('walls', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('body');
            $table->datetime('datecreated');
        });
        //criando campo walllikes no banco, id_wall, id_user (consegue identificar quantos likes teve e quantos usuarios deram likes)
        Schema::create('walllikes', function (Blueprint $table) {
            $table->id();
            $table->integer('id_wall');
            $table->integer('id_user');
        });
        //criando campo docs no banco, title, fileurl
        Schema::create('docs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('fileurl');
        });
        //criando campo billets no banco, id_unit, title, fileurl(identifica em qual unidade vai billets especificamente)
        Schema::create('billets', function (Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('title');
            $table->string('fileurl');
        });
        //criando campo warnings (avisos) no banco, id_unit,title,status,datecreated,photos
        Schema::create('warnings', function (Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->string('title');
            $table->string('status')->default('IN_REVIEW'); //IN_REVIEW(em analise) RESOLVED(resolvido)
            $table->date('datecreated');
            $table->text('photos'); //foto1.jpg, foto2.jpg...
        });
        // criando campo foundandlost(achados e perdidos) no banco, status,photo,description,where,datecreated
        Schema::create('foundandlost', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('LOST'); //LOST, RECOVERED
            $table->string('photo');
            $table->string('description');
            $table->string('where');
            $table->date('datecreated');
        });
        //criando campo areas no banco (criar reservas nas areas, se está disponível ou não)allowed,title,cover,days,start_time,end_time
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->integer('allowed')->default('1');
            $table->string('title');
            $table->string('cover');
            $table->string('days'); //dias disponíveis 0,1,2,3,4,5,6 (domingo a sabado)
            $table->time('start_time');
            $table->time('end_time');
        });
        //criando campo areadisabled no banco(desabilitar determinados dias, exemplo natal) id_area, day
        Schema::create('areadisableddays', function (Blueprint $table) {
            $table->id();
            $table->integer('id_area');
            $table->date('day');
        });
        //criando campo reservations no banco(reservas unidade vai reservar a area) id_unit, id_area, reservation_date
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->integer('id_unit');
            $table->integer('id_area');
            $table->dateTime('reservation_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('units');
        Schema::dropIfExists('unitpeoples');
        Schema::dropIfExists('unitvehicles');
        Schema::dropIfExists('unitpets');
        Schema::dropIfExists('walls');
        Schema::dropIfExists('walllikes');
        Schema::dropIfExists('docs');
        Schema::dropIfExists('billets');
        Schema::dropIfExists('warnings');
        Schema::dropIfExists('foundandlost');
        Schema::dropIfExists('areas');
        Schema::dropIfExists('areadisabledays');
        Schema::dropIfExists('reservations');
    }
}
