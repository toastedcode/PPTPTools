<?php

class Permission
{
   const UNKNOWN                  = 0;
   const FIRST                    = 1;
   const VIEW_JOB                 = Permission::FIRST;
   const EDIT_JOB                 = 2;
   const VIEW_TIME_CARD           = 3;
   const EDIT_TIME_CARD           = 4;
   const VIEW_PART_WEIGHT_LOG     = 5;
   const EDIT_PART_WEIGHT_LOG     = 6;
   const VIEW_PART_WASHER_LOG     = 7;
   const EDIT_PART_WASHER_LOG     = 8;
   const VIEW_PART_INSPECTION     = 9;
   const VIEW_MACHINE_STATUS      = 10;
   const VIEW_PRODUCTION_SUMMARY  = 11;
   const VIEW_USER                = 12;
   const EDIT_USER                = 13;
   const VIEW_OTHER_USERS         = 14;
   const APPROVE_TIME_CARDS       = 15;
   const VIEW_SIGN                = 16;
   const EDIT_SIGN                = 17;
   const VIEW_INSPECTION          = 18;
   const EDIT_INSPECTION          = 19;
   const QUICK_INSPECTION         = 20;
   const VIEW_INSPECTION_TEMPLATE = 21;
   const EDIT_INSPECTION_TEMPLATE = 22;  
   const VIEW_PRINT_MANAGER       = 23;   
   const VIEW_MAINTENANCE_LOG     = 24;
   const EDIT_MAINTENANCE_LOG     = 25;
   const LAST                     = Permission::EDIT_MAINTENANCE_LOG;
   
   const NO_PERMISSIONS = 0x00000000;
   const ALL_PERMISSIONS = 0xFFFFFFFF;
   
   public $permissionId;
   
   public $permissionName;
   
   public $bits;
   
   public static function getPermissions()
   {
      if (Permission::$permissions == null)
      {
         Permission::$permissions =
            array(new Permission(Permission::VIEW_JOB,                 "View job"),
                  new Permission(Permission::EDIT_JOB,                 "Edit job"),
                  new Permission(Permission::VIEW_TIME_CARD,           "View time card"),
                  new Permission(Permission::EDIT_TIME_CARD,           "Edit time card"),
                  new Permission(Permission::VIEW_PART_WEIGHT_LOG,     "View part weight log"),
                  new Permission(Permission::EDIT_PART_WEIGHT_LOG,     "Edit part weight log"),
                  new Permission(Permission::VIEW_PART_WASHER_LOG,     "View part washer log"),
                  new Permission(Permission::EDIT_PART_WASHER_LOG,     "Edit part washer log"),
                  new Permission(Permission::VIEW_PART_INSPECTION,     "View part inspection"),
                  new Permission(Permission::VIEW_MACHINE_STATUS,      "View machine status"),
                  new Permission(Permission::VIEW_PRODUCTION_SUMMARY,  "View production summary"),
                  new Permission(Permission::VIEW_USER,                "View user"),
                  new Permission(Permission::EDIT_USER,                "Edit user"),
                  new Permission(Permission::VIEW_OTHER_USERS,         "View other users"),
                  new Permission(Permission::APPROVE_TIME_CARDS,       "Approve time cards"),
                  new Permission(Permission::VIEW_SIGN,                "View digital signs"),
                  new Permission(Permission::EDIT_SIGN,                "Edit digital signs"),
                  new Permission(Permission::VIEW_INSPECTION,          "View inspection"), 
                  new Permission(Permission::EDIT_INSPECTION,          "Edit inspection"),
                  new Permission(Permission::QUICK_INSPECTION,         "Enable \"quick\" inspections"),
                  new Permission(Permission::VIEW_INSPECTION_TEMPLATE, "View inspection template"),
                  new Permission(Permission::EDIT_INSPECTION_TEMPLATE, "Edit inspection template"),               
                  new Permission(Permission::VIEW_PRINT_MANAGER,       "View print manager"),
                  new Permission(Permission::VIEW_MAINTENANCE_LOG,     "View maintenance log"),
                  new Permission(Permission::EDIT_MAINTENANCE_LOG,     "Edit maintenance log")
            );
      }
      
      return (Permission::$permissions);
   }
   
   public static function getPermission($permissionId)
   {
      $permission = new Permission(Permission::UNKNOWN, "");
      
      if (($permissionId>= Permission::FIRST) && ($permissionId <= Permission::LAST))
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
      
      if ($permissionId > Permission::UNKNOWN)
      {
         $this->bits = (1 << ($permissionId - Permission::FIRST));
      }
      else
      {
         $this->bits = 0;
      }
   }
}