<?php
require_once '../database.php';

class JobInfo
{
   const UNKNOWN_JOB_NUMBER = 0;
   
   public $jobNumber = JobInfo::UNKNOWN_JOB_NUMBER;
   public $creator;
   public $dateTime;
   public $partNumber;
   public $wcNumber;
   public $isActive;
   
   public static function load($jobNumber)
   {
      $jobInfo = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getJob($jobNumber);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $jobInfo = new JobInfo();
            
            $jobInfo->jobNumber =  $row['jobNumber'];
            $jobInfo->creator =    $row['creator'];
            $jobInfo->dateTime =   Time::fromMySqlDate($row['dateTime'], "Y-m-d H:i:s");
            $jobInfo->partNumber = $row['partNumber'];
            $jobInfo->wcNumber =   $row['wcNumber'];
            $jobInfo->isActive =   $row['isActive'];
         }
      }
      
      return ($jobInfo);
   }
}

/*
if (isset($_GET["jobNumber"]))
{
   $jobNumber = $_GET["jobNumber"];
   $jobInfo = JobInfo::load($jobNumber);
   
   if ($jobInfo)
   {
      echo "jobNumber: " .  $jobInfo->jobNumber  . "<br/>";
      echo "creator: " .    $jobInfo->creator    . "<br/>";
      echo "dateTime: " .   $jobInfo->dateTime   . "<br/>";
      echo "partNumber: " . $jobInfo->partNumber . "<br/>";
      echo "wcNumber: " .   $jobInfo->wcNumber   . "<br/>";
      echo "isActive: " .   $jobInfo->isActive   . "<br/>";
   }
   else
   {
      echo "No job found.";
   }
}
*/
?>