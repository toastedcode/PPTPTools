<?php

require_once '../common/root.php';

class Upload
{
   const UPLOADED      = 0;
   const BAD_FILE_TYPE = 1;
   const BAD_FILE_SIZE = 2;
   const FILE_ERROR    = 3;
   
   static function uploadCustomerPrint($file)
   {
      global $UPLOADS;
      
      $returnStatus = Upload::UPLOADED;
      
      $target = $UPLOADS . basename($file["name"]);
      
      if (!Upload::validateFileFormat($file, array("pdf")))
      {
         $returnStatus = Upload::BAD_FILE_TYPE;
      }
      else if (!Upload::validateFileSize($file, 500000))  // 500Kb
      {
         $returnStatus = Upload::BAD_FILE_SIZE;
      }
      else if (!move_uploaded_file($file["tmp_name"], $target))
      {
         $returnStatus = Upload::FILE_ERROR;
      }
      
      return ($returnStatus);
   }
   
   static function validateFileSize($file, $maxSize)
   {
      return ($file["size"] > $maxSize);
   }
   
   static function validateFileFormat($file, $extensions)
   {
      $extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
      
      return (in_array($extension, $extensions));
   }
}