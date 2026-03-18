<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration{
public function up():void{
Schema::create('order_items',function(Blueprint $t){
$t->id();
$t->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
$t->foreignId('listing_id')->constrained('listings')->cascadeOnDelete();
$t->decimal('price',10,2);
$t->decimal('qty',10,2);
$t->timestamps();
});}
public function down():void{Schema::dropIfExists('order_items');}};
