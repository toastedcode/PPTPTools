<?php

require_once '../database.php';

class MachineStatus
{
   const DISCONNECTED = 0;
   const FAILED = 1;
   const IDLE = 2;
   const ACTIVE = 3;
   
   public static function toString($machineStatus)
   {
      $string = "";
      
      switch ($machineStatus)
      {
         case MachineStatus::DISCONNECTED:
         {
            $string = "Disconnected";
            break;
         }
         
         case MachineStatus::FAILED:
         {
            $string = "Failed";
            break;
         }
         
         case MachineStatus::IDLE:
         {
            $string = "Idle";
            break;
         }
         
         case MachineStatus::ACTIVE:
         {
            $string = "Active";
            break;
         }
         
         default:
         {
            break;
         }
      }
         
      return ($string);
   }
}

class MachineStatusIndicator
{
   public function __construct($wcNumber)
   {
      $this->wcNumber = $wcNumber;
      
      $this->database = new PPTPDatabase();
   }
   
   public function getHtml()
   {
      $html = "";
      
      $this->database->connect();
      
      if ($this->database->isConnected())
      {
         $status = strtolower(MachineStatus::toString($this->getStatus()));
         $statusTime = $this->getStatusTime();
         $partCount = $this->getPartCount();
         $hourlyGraph = $this->getHourlyGraph();
         $startTime = "6am";
         $endTime = "2pm";
         
         $html .=
<<<HEREDOC
         <div class="flex-vertical machine-status-div $status">
            <div class="flex-vertical machine-status-header-div">
               <div class="flex-horizontal wc-text">$this->wcNumber</div>
               <div id="status-time-$this->wcNumber class="flex-horizontal small-text">$statusTime</div>
            </div>
            <div class="flex-horizontal machine-status-body-div">
               <div class="flex-vertical machine-status-part-count-div">
                  <div id="part-count-$this->wcNumber" class="flex-horizontal part-count-text">$partCount</div>
                  <div class="flex-horizontal small-text">Part Count</div>
               </div>
            </div>
            $hourlyGraph
            <div class="flex-horizontal machine-status-footer-div small-text">
               <div>$startTime</div>
               <div>$endTime</div>
            </div>               
         </div>

         <script src="machineStatus.js"></script>
         <script>
            //var machineMonitor = new MachineMonitor($this->wcNumber);
            //machineMonitor.onTimeout();
           setInterval(function(){updateStatus($this->wcNumber);}, 1000);
         </script>
HEREDOC;
      }
      
      return ($html);
   }
   
   public function render()
   {
      echo ($this->getHtml());
   }
   
   private function getStatus()
   {
      $status = MachineStatus::IDLE;  // TODO
      
      return ($status);
   }
   
   private function getStatusTime()
   {
      $statusTime = "49m - Active";  // TODO
      
      return ($statusTime);
   }
   
   private function getPartCount()
   {
      $partCount = 131;  // TODO
      
      return ($partCount);
   }
   
   private function getHourlyPartCounts()
   {
      $partCounts = [0, 0, 45, 78, 78, 898, 456, 688, 345, 34, 0, 0];
      
      return ($partCounts);
   }
   
   private function getHourlyGraph()
   {
      $partCounts = $this->getHourlyPartCounts();
      
      $hourlyGraph = 
<<<HEREDOC
      <div class="flex-horizontal machine-status-graph-div">
         <div class="graph-bar" style="height:0%"></div>
         <div class="graph-bar" style="height:0%"></div>
         <div class="graph-bar" style="height:10%"></div>
         <div class="graph-bar" style="height:15%"></div>
         <div class="graph-bar" style="height:50%"></div>
         <div class="graph-bar" style="height:75%"></div>
         <div class="graph-bar" style="height:75%"></div>
         <div class="graph-bar" style="height:65%"></div>
         <div class="graph-bar" style="height:80%"></div>
         <div class="graph-bar" style="height:10%"></div>
         <div class="graph-bar" style="height:0%"></div>
         <div class="graph-bar" style="height:0%"></div>
      </div>
HEREDOC;
      
      return ($hourlyGraph);
   }
   
   private $wcNumber = 0;
   
   private $database;
}
?>

<html>

<link rel="stylesheet" type="text/css" href="../flex.css"/>
<link rel="stylesheet" type="text/css" href="machineStatus.css"/>

<body>
   <?php $indicator = new MachineStatusIndicator(813); echo ($indicator->render());?>
</body>
</html>