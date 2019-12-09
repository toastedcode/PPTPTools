<?php

require_once 'printJob.php';

class PrintQueue
{
   public $queue;
   
   public function __construct()
   {
      $this->queue = array();
   }
   
   public static function load($printerId)
   {
      $printQueue = new PrintQueue();
      
      $database = PPTPDatabase::getInstance();
      
      if ($database && $database->isConnected())
      {
         $result = $database->getPrintJobIds($printerId);
         
         while ($result && ($row = $result->fetch_assoc()))
         {
            $printJob = PrintJob::load($row["printJobId"]);
            
            if ($printJob)
            {
               $printQueue->queue[] = $printJob;
            }
         }
      }
      
      return ($printQueue);
   }
   
   public function size()
   {
      return (count($queue));
   }
}

/*
if (isset($_GET["printerId"]))
{
   $printerId = $_GET["printerId"];
   
   $printQueue = PrintQueue::load($printerId);
   
   if ($printQueue)
   {
      echo "printQueue: ";
      
      foreach ($printQueue->queue as $printJob)
      {
         echo "$printJob->printJobId ->" .  PrintJobStatus::getLabel($printJob->status) . ", ";
      }
   }
   else
   {
      echo "No print jobs found for printer [$printerId].";
   }
}
*/
?>