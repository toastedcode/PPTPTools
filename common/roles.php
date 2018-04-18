<?php

require_once 'permissions.php';

class Role
{
   const UNKNOWN     = 0;
   const FIRST       = 1;
   const SUPER_USER  = Role::FIRST;
   const ADMIN       = 2;
   const OPERATOR    = 3;
   const LABORER     = 4;
   const PART_WASHER = 5;
   const LAST        = Role::PART_WASHER;
   
   public $roleId;
   
   public $roleName;
   
   public $permissions;
      
   public static function getRoles()
   {
      if (Role::$roles == null)
      {
         Role::$roles = 
            array(new Role(Role::SUPER_USER, "Super User", Permission::ALL_PERMISSIONS),
                  new Role(Role::ADMIN, "Admin", Permission::ALL_PERMISSIONS),
                  new Role(Role::OPERATOR, "Operator", Permission::getBits(Permission::VIEW_TIME_CARD, Permission::EDIT_TIME_CARD, Permission::VIEW_PART_INSPECTION)),
                  new Role(Role::LABORER, "Laborer", Permission::getBits(Permission::VIEW_PART_WEIGHT_LOG, Permission::EDIT_PART_WEIGHT_LOG)),
                  new Role(Role::PART_WASHER, "Part Washer", Permission::getBits(Permission::VIEW_PART_WASHER_LOG, Permission::EDIT_PART_WASHER_LOG)));
      }
      
      return (Role::$roles);
   }
   
   public static function getRole($roleId)
   {
      $role = new Role(Role::UNKNOWN, "", Permission::NO_PERMISSIONS);
      
      if (($roleId >= Role::FIRST) && ($roleId <= Role::LAST))
      {
         $role = Role::getRoles()[$roleId - Role::FIRST];
      }
      
      return ($role);
   }
   
   public function hasPermission($permissionId)
   {
      $permission = Permission::getPermission($permissionId);
      
      return ($permission->isSetIn($this->permissions));
   }
   
   private static $roles = null;
      
   private function __construct($roleId, $roleName, $permissions)
   {
      $this->roleId = $roleId;
      $this->roleName = $roleName;
      $this->permissions = $permissions;
   }
}

/*
$role = Role::getRole(Role::PART_WASHER);

foreach (Permission::getPermissions() as $permission)
{
   $isSet = $permission->isSetIn($role->permissions) ? "set" : "";
   echo "{$permission->permissionName}: $isSet<br/>";
}

$isEditPartWasherLogSet = $role->hasPermission(Permission::EDIT_PART_WASHER_LOG) ? "is set" : "is not set";
$isEditPartWeightLogSet = $role->hasPermission(Permission::EDIT_PART_WEIGHT_LOG) ? "is set" : "is not set";

echo ("EDIT_PART_WASHER_LOG = $isEditPartWasherLogSet<br/>");
echo ("EDIT_PART_WEIGHT_LOG = $isEditPartWeightLogSet<br/>");
*/