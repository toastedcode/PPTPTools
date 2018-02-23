<?php
require_once '../database.php';

abstract class JobStatus
{
   const PENDING = 0;
   const ACTIVE = 1;
   const COMPLETE = 2;
   const DELETED = 2;
   
   private static $names = array("Pending", "Active", "Complete", "Deleted");
   
   public static function getName($status)
   {
      return (JobStatus::$names[$status]);
   }
}

class JobInfo
{
   const UNKNOWN_JOB_NUMBER = "";
   
   public $jobNumber = JobInfo::UNKNOWN_JOB_NUMBER;
   public $creator;
   public $dateTime;
   public $partNumber;
   public $wcNumber;
   public $status = JobStatus::PENDING;
   
   public function isActive()
   {
      return ($this->status = JobStatus::ACTIVE);
   }
   
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
            $jobInfo->status =   $row['status'];
         }
      }
      
      return ($jobInfo);
   }
   
   public static function getJobPrefix($jobNumber)
   {
      $dashpos = strpos($jobNumber, "-");
      
      $prefix = $jobNumber;
      if ($dashpos)
      {
         $prefix = substr($jobNumber, 0, $dashpos);
      }
      
      return ($prefix);
   }
   
   public static function getJobSuffix($jobNumber)
   {
      $dashpos = strpos($jobNumber, "-");
      
      $suffix = "";
      if ($dashpos)
      {
         $suffix = substr($jobNumber, ($dashpos + 1));
      }

      return ($suffix);
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