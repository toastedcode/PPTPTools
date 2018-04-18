<?php

abstract class Permissions
{
   const VIEW_JOB                = 0b00000000001;
   const EDIT_JOB                = 0b00000000010;
   const VIEW_TIME_CARD          = 0b00000000100;
   const EDIT_TIME_CARD          = 0b00000001000;
   const WEIGH_PARTS             = 0b00000010000;
   const COUNT_PARTS             = 0b00000100000;
   const VIEW_PART_INSPECTION    = 0b00001000000;
   const EDIT_PART_INSPECTION    = 0b00010000000;
   const VIEW_MACHINE_STATUS     = 0b00100000000;
   const VIEW_PRODUCTION_SUMMARY = 0b01000000000;
   const VIEW_OTHERS             = 0b10000000000;
   
   static function check($permission, $mask)
   {
      return (($permission & $mask) > 0);
   }
   
   static function createMask(...$permissions)
   {
      $mask = 0;
      
      foreach ($permissions as $permission)
      {
         $mask |= $permission;
      }
      
      return ($mask);
   }
}