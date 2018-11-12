<?php
require_once 'database.php';
require_once 'time.php';

class LineInspectionInfo
{
   const INVALID_ENTRY_ID = 0;
   
   const NUM_THREAD_INSPECTIONS = 3;
   
   public $entryId;
   public $dateTime;
   public $inspector;
   public $operator;
   public $jobNumber;
   public $wcNumber;
   public $threadInspections;
   public $visualInspection;
   public $comments;
   
   public function __construct()
   {
      $entryId = LineInspectionInfo::INVALID_ENTRY_ID;
      $threadInspections = array(false, false, false);
   }
   
   public static function load($entryId)
   {
      $lineInspectionInfo = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getLineInspection($entryId);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $lineInspectionInfo = new LineInspectionInfo();
            
            $lineInspectionInfo->entryId= intval($row['entryId']);
            $lineInspectionInfo->dateTime= Time::fromMySqlDate($row['dateTime'], "Y-m-d H:i:s");
            $lineInspectionInfo->inspector= intval($row['inspector']);
            $lineInspectionInfo->operator= intval($row['operator']);
            $lineInspectionInfo->jobNumber= $row['jobNumber'];
            $lineInspectionInfo->wcNumber= intval($row['wcNumber']);
            $lineInspectionInfo->threadInspections[0] = boolval($row['thread1']);
            $lineInspectionInfo->threadInspections[1] = boolval($row['thread2']);
            $lineInspectionInfo->threadInspections[2] = boolval($row['thread3']);
            $lineInspectionInfo->visualInspection = boolval($row['visual']);
            $lineInspectionInfo->comments = $row['comments'];
         }
      }
      
      return ($lineInspectionInfo);
   }
}

/*
if (isset($_GET["entryId"]))
{
   $entryId = $_GET["entryId"];
   $lineInspectionInfo = LineInspectionInfo::load($entryId);
 
   if ($lineInspectionInfo)
   {
      echo "entryId: " .   $lineInspectionInfo->entryId.           "<br/>";
      echo "dateTime: " .  $lineInspectionInfo->dateTime .         "<br/>";
      echo "inspector: " . $lineInspectionInfo->inspector.         "<br/>";
      echo "operator: " .  $lineInspectionInfo->operator.          "<br/>";
      echo "jobNumber: " . $lineInspectionInfo->jobNumber.         "<br/>";
      for ($i = 0; $i < LineInspectionInfo::NUM_THREAD_INSPECTIONS; $i++)
      {
         echo "thread[" . $i . "]: " . $lineInspectionInfo->threadInspections[$i] . "<br/>";
      }
      echo "visual: " .    $lineInspectionInfo->visualInspection . "<br/>";
      echo "comments: " .  $lineInspectionInfo->comments .         "<br/>";
   }
   else
   {
        echo "No line inspection found.";
   }
}
*/
?>