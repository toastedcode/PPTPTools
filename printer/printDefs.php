<?php

abstract class PrintJobStatus
{
   const FIRST = 0;
   const UNKNOWN = PrintJobStatus::FIRST;
   const QUEUED = 1;
   const PENDING = 2;
   const PRINTING = 3;
   const COMPLETE = 4;
   const DELETED = 5;
   const LAST = 6;
   const COUNT = PrintJobStatus::LAST - PrintJobStatus::FIRST;
   
   public static function getLabel($status)
   {
      $labels = array("", "QUEUED", "PENDING", "PRINTING", "COMPLETE", "DELETED");
      
      return ($labels[$status]);
   }
}

?>