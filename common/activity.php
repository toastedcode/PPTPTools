<?php
class Activity
{
   const UNKNOWN = 0;
   const FIRST = 1;
   const JOBS = Activity::FIRST;
   const TIME_CARD = 2;
   const PART_WEIGHT = 3;
   const PART_WASH = 4;
   const PART_INSPECTION = 5;
   const INSPECTION_TEMPLATE = 6;
   const LINE_INSPECTION = 7;
   const MACHINE_STATUS = 8;
   const PRODUCTION_SUMMARY = 9;
   const USER = 10;
   const SIGNAGE = 11;
   const PRINT_MANAGER = 12;
   const LAST = 13;
   
   public function getPermissionMask($activity)
   {
      
   }
   
   public function getIcon($activity)
   {
      
   }
   
   public function getTitle($activity)
   {
     
   }
   
   public static function isAllowed($activity, $permissions)
   {
      return (($permissions & Activity::getPermissionMask($activity)) > 0);
   }
   
   private static $permissionMasks = null;
   
   private static function getPermissionMasks()
   {
      if (Activity::$permissionMasks == null)
      {
         Activity::$permissionMasks = array(
            Permission::getPermission(Permission::VIEW_JOB)->bits,                  // JOBS
            Permission::getPermission(Permission::VIEW_TIME_CARD)->bits,            // TIME_CARD
            Permission::getPermission(Permission::VIEW_PART_WEIGHT_LOG)->bits,      // PART_WEIGHT
            Permission::getPermission(Permission::VIEW_PART_WASHER_LOG)->bits,      // PART_WASH
            Permission::getPermission(Permission::VIEW_PART_INSPECTION)->bits,      // PART_INSPECTION
            Permission::getPermission(Permission::VIEW_INSPECTION_TEMPLATE)->bits,  // INSPECTION_TEMPLATE
            Permission::getPermission(Permission::VIEW_INSPECTION)->bits,           // LINE_INSPECTION
            Permission::getPermission(Permission::VIEW_MACHINE_STATUS)->bits,       // MACHINE_STATUS
            Permission::getPermission(Permission::VIEW_PRODUCTION_SUMMARY)->bits,   // PRODUCTION_SUMMARY
            Permission::getPermission(Permission::VIEW_USER)->bits,                 // USER
            Permission::getPermission(Permission::VIEW_SIGN)->bits,                 // SIGNAGE
            Permission::getPermission(Permission::VIEW_PRINT_MANAGER)->bits         // PRINT_MANAGER
         );
      }
      
      return (Activity::$permissionMasks);
   }
   
   public static function getPermissionMask($activity)
   {
      $permissionMask = 0;
      
      if (($activity >= Activity::FIRST) && ($activity <= Activity::LAST))
      {
         $permissionMask = Activity::getPermissionMasks()[$activity - Activity::FIRST];
      }
      
      return ($permissionMask);
   }
   
   public static function isAllowed($activity, $permissions)
   {
      return (($permissions & Activity::getPermissionMask($activity)) > 0);
   }
}
?>