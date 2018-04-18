<?php

class Permission
{
   const UNKNOWN                 = 0;
   const FIRST                   = 1;
   const VIEW_JOB                = Permission::FIRST;
   const EDIT_JOB                = 2;
   const VIEW_TIME_CARD          = 3;
   const EDIT_TIME_CARD          = 4;
   const VIEW_PART_WEIGHT_LOG    = 5;
   const EDIT_PART_WEIGHT_LOG    = 6;
   const VIEW_PART_WASHER_LOG    = 7;
   const EDIT_PART_WASHER_LOG    = 8;
   const VIEW_PART_INSPECTION    = 9;
   const VIEW_MACHINE_STATUS     = 10;
   const VIEW_PRODUCTION_SUMMARY = 11;
   const VIEW_ALL_USER_DATA      = 12;
   const LAST                    = Permission::VIEW_ALL_USER_DATA;
   
   const NO_PERMISSIONS = 0x0000;
   const ALL_PERMISSIONS = 0xFFFF;
   
   public $permissionId;
   
   public $permissionName;
   
   public $bits;
   
   public static function getPermissions()
   {
      if (Permission::$permissions == null)
      {
         Permission::$permissions =
            array(new Permission(Permission::VIEW_JOB,                "View job"),
                  new Permission(Permission::EDIT_JOB,                "Edit job"),
                  new Permission(Permission::VIEW_TIME_CARD,          "View time card"),
                  new Permission(Permission::EDIT_TIME_CARD,          "Edit time card"),
                  new Permission(Permission::VIEW_PART_WEIGHT_LOG,    "View part weight log"),
                  new Permission(Permission::EDIT_PART_WEIGHT_LOG,    "Edit part weight log"),
                  new Permission(Permission::VIEW_PART_WASHER_LOG,    "View part washer log"),
                  new Permission(Permission::EDIT_PART_WASHER_LOG,    "Edit part washer log"),
                  new Permission(Permission::VIEW_PART_INSPECTION,   "View part inspection"),
                  new Permission(Permission::VIEW_MACHINE_STATUS,     "View machine status"),
                  new Permission(Permission::VIEW_PRODUCTION_SUMMARY, "View production summary"));
      }
      
      return (Permission::$permissions);
   }
   
   public static function getPermission($permissionId)
   {
      $permission = new Permission(Permission::UNKNOWN, "");
      
      if (($permissionId>= Permission::FIRST) && ($permissionId<= Permission::LAST))
      {
         $permission = Permission::getPermissions()[$permissionId - Permission::FIRST];
      }
      
      return ($permission);
   }
   
   public function isSetIn($mask)
   {
      return (($this->bits & $mask) > 0);
   }
   
   public static function getBits(...$permissionIds)
   {
      $bits = Permission::NO_PERMISSIONS;
      
      foreach ($permissionIds as $permissionId)
      {
         $bits |=  Permission::getPermission($permissionId)->bits;
      }
      
      return ($bits);
   }
   
   private static $permissions = null;
   
   private function __construct($permissionId, $permissionName)
   {
      $this->permissionId = $permissionId;
      $this->permissionName = $permissionName;
      $this->bits = (1 << $permissionId);
   }
}