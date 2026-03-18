<?php
namespace App\Services;
use App\Models\TenantUser;
class UserRoleService{
 public function role(){ return TenantUser::where('tenant_id',session('tenant_id'))->where('user_id',auth()->id())->value('role'); }
 public function isAdmin(){ return $this->role()==='admin' || auth()->user()?->is_super_admin; }
 public function isFarmer(){ return $this->role()==='farmer'; }
 public function isTrucker(){ return $this->role()==='trucker'; }
 public function isBuyer(){ return $this->role()==='buyer'; }
 public function isAuditor(){ return $this->role()==='auditor'; }
}