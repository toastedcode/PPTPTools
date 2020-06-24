<?php
require_once 'database.php';
require_once 'inspectionTemplate.php';

abstract class JobStatus
{
   const FIRST = 0;
   const PENDING = JobStatus::FIRST;
   const ACTIVE = 1;
   const COMPLETE = 2;
   const CLOSED = 3;
   const DELETED = 4;
   const LAST = 5;
   const COUNT = JobStatus::LAST - JobStatus::FIRST;
   
   private static $names = array("Pending", "Active", "Complete", "Closed", "Deleted");
   
   public static function getName($status)
   {
      return (JobStatus::$names[$status]);
   }
}

class JobInfo
{
   const UNKNOWN_JOB_ID = 0;
   
   const UNKNOWN_JOB_NUMBER = "";
   
   const UNKNOWN_WC_NUMBER = 0;
   
   const SECONDS_PER_MINUTE = 60;
   
   const SECONDS_PER_HOUR = 3600;
   
   const UNKNOWN_SAMPLE_WEIGHT = 0.0;
      
   public $jobId = JobInfo::UNKNOWN_JOB_ID;
   public $jobNumber = JobInfo::UNKNOWN_JOB_NUMBER;
   public $creator;
   public $dateTime;
   public $partNumber;
   public $sampleWeight = JobInfo::UNKNOWN_SAMPLE_WEIGHT;
   public $wcNumber;
   public $cycleTime;
   public $netPercentage;
   public $status = JobStatus::PENDING;
   public $inProcessTemplateId = InspectionTemplate::UNKNOWN_TEMPLATE_ID;
   public $lineTemplateId = InspectionTemplate::UNKNOWN_TEMPLATE_ID;
   public $qcpTemplateId = InspectionTemplate::UNKNOWN_TEMPLATE_ID;
   public $customerPrint;
   
   public function isActive()
   {
      return ($this->status = JobStatus::ACTIVE);
   }
   
   public static function load($jobId)
   {
      $jobInfo = null;
      
      $database = PPTPDatabase::getInstance();
      
      if ($database && $database->isConnected())
      {
         $result = $database->getJob($jobId);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $jobInfo = new JobInfo();
            
            $jobInfo->jobId =         intval($row['jobId']);
            $jobInfo->jobNumber =     $row['jobNumber'];
            $jobInfo->creator =       $row['creator'];
            $jobInfo->dateTime =      Time::fromMySqlDate($row['dateTime'], "Y-m-d H:i:s");
            $jobInfo->partNumber =    $row['partNumber'];
            $jobInfo->sampleWeight =  doubleval($row['sampleWeight']);
            $jobInfo->wcNumber =      $row['wcNumber'];
            $jobInfo->cycleTime =     doubleval($row['cycleTime']);
            $jobInfo->netPercentage = doubleval($row['netPercentage']);
            $jobInfo->status =        $row['status'];
            $jobInfo->inProcessTemplateId = intval($row['inProcessTemplateId']);
            $jobInfo->lineTemplateId = intval($row['lineTemplateId']);
            $jobInfo->qcpTemplateId = intval($row['qcpTemplateId']);
            $jobInfo->customerPrint = $row['customerPrint'];
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
   
   public function getGrossPartsPerHour()
   {
      $grossPartsPerHour = 0;
      
      if (($this->cycleTime > 0) &&
          ($this->cycleTime <= JobInfo::SECONDS_PER_MINUTE))
      {
         $grossPartsPerHour = (JobInfo::SECONDS_PER_HOUR / $this->cycleTime);   
      }
      
      return ($grossPartsPerHour);
   }
   
   public function getNetPartsPerHour()
   {
      $grossPartsPerHour = $this->getGrossPartsPerHour();
      
      $netPartsPerHour = ($grossPartsPerHour * ($this->netPercentage / 100.0));
      
      return ($netPartsPerHour);
   }
   
   public static function getJobNumbers($onlyActive)
   {
      $jobNumbers = array();
      
      $database = PPTPDatabase::getInstance();
      
      if ($database && $database->isConnected())
      {
         $result = $database->getJobNumbers($onlyActive);
         
         if ($result)
         {
            while ($result && ($row = $result->fetch_assoc()))
            {
               $jobNumbers[] = $row["jobNumber"];
            }
         }
      }
      
      return ($jobNumbers);
   }
   
   public static function getJobIdByComponents($jobNumber, $wcNumber)
   {
      $jobId = JobInfo::UNKNOWN_JOB_ID;
      
      $database = PPTPDatabase::getInstance();
      
      $result = $database->getJobByComponents($jobNumber, $wcNumber);

      if ($result && ($row = $result->fetch_assoc()))
      {
         $jobId = intval($row["jobId"]);
      }
      
      return ($jobId);
   }
}

/*
if (isset($_GET["$jobId"]))
{
   $jobId = $_GET["jobId"];
   $jobInfo = JobInfo::load($jobId);
   
   if ($jobInfo)
   {
      echo "jobId: " .               $jobInfo->jobId .               "<br/>";
      echo "jobNumber: " .           $jobInfo->jobNumber .           "<br/>";
      echo "creator: " .             $jobInfo->creator .             "<br/>";
      echo "dateTime: " .            $jobInfo->dateTime .            "<br/>";
      echo "partNumber: " .          $jobInfo->partNumber .          "<br/>";
      echo "sampleWeight: " .        $jobInfo->sampleWeight .        "<br/>";
      echo "wcNumber: " .            $jobInfo->wcNumber .            "<br/>";
      echo "cycleTime: " .           $jobInfo->cycleTime .           "<br/>";
      echo "netPercentage: " .       $jobInfo->netPercentage .       "<br/>";
      echo "inProcessTemplateId: " . $jobInfo->inProcessTemplateId . "<br/>";
      echo "lineTemplateId: " .      $jobInfo->lineTemplateId .      "<br/>";
      echo "qcpTemplateId: " .       $jobInfo->qcpTemplateId .       "<br/>";
      echo "customerPrint: " .       $jobInfo->customerPrint .       "<br/>";
      
      echo "status: " . JobStatus::getName($jobInfo->status) . "<br/>";
   }
   else
   {
      echo "No job found.";
   }
}
*/

?>