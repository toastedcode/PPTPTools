<?php

require_once '../common/database.php';
require_once 'printDefs.php';

class PrintJob
{
   const UNKNOWN_PRINT_JOB_ID = 0;
   
   const UNKNOWN_OWNER_ID = 0;
   
   const UNKNOWN_PRINTER_ID = 0;
   
   const MIN_COPIES = 1;
   
   public $printJobId;
   public $owner;
   public $dateTime;
   public $description;
   public $printerId;
   public $copies;
   public $status;
   public $xml;
   
   public function __construct()
   {
      $this->printJobId = PrintJob::UNKNOWN_PRINT_JOB_ID;
      $this->owner = PrintJob::UNKNOWN_OWNER_ID;
      $this->dateTime = null;
      $this->description = "";
      $this->printerId = PrintJob::UNKNOWN_PRINTER_ID;
      $this->copies = PrintJob::MIN_COPIES;
      $this->status = PrintJobStatus::UNKNOWN;
      $this->xml = "";
   }
   
   public static function load($printJobId)
   {
      $printJob = null;
      
      $database = PPTPDatabase::getInstance();
      
      if ($database && $database->isConnected())
      {
         $result = $database->getPrintJob($printJobId);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $printJob = new PrintJob();
            
            $printJob->printJobId = intval($row['printJobId']);
            $printJob->owner = intval($row['owner']);
            $printJob->dateTime = Time::fromMySqlDate($row['dateTime'], "Y-m-d H:i:s");
            $printJob->description = $row['description'];
            $printJob->printerId = intval($row['printerId']);
            $printJob->copies = intval($row['copies']);
            $printJob->status = intval($row['status']);
            $printJob->xml = $row['xml'];
         }
      }
      
      return ($printJob);
   }
}

/*
if (isset($_GET["printJobId"]))
{
   $printJobId = $_GET["printJobId"];
    
   $printJob = PrintJob::load($printJobId);
 
   if ($printJob)
   {
      echo "printJobId: " .  $printJob->printJobId .                       "<br/>";
      echo "owner: " .       $printJob->owner .                            "<br/>";
      echo "dateTime: " .    $printJob->dateTime .                         "<br/>";
      echo "description: " . $printJob->description .                      "<br/>";
      echo "printerId: " .   $printJob->printerId .                        "<br/>"; 
      echo "copies: " .      $printJob->copies .                           "<br/>";     
      echo "status: " .      PrintJobStatus::getLabel($printJob->status) . "<br/>";
      echo "xml: " .         htmlspecialchars($printJob->xml) .            "<br/>";
   }
   else
   {
     echo "No print job found.";
   }
}
*/

?>