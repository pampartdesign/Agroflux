<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration{
public function up():void{
Schema::create('orders',function(Blueprint $t){
$t->id();
$t->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
$t->string('customer_name');
$t->string('customer_email');
$t->string('status')->default('pending');
$t->decimal('total',10,2)->default(0);
$t->timestamps();
$t->index(['tenant_id','status']);
});}
public function down():void{Schema::dropIfExists('orders');}};
