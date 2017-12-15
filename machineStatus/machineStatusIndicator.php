<?php

class MachineStatus
{
   const UNASSIGNED = 0;
   const DISCONNECTED = 1;
   const FAILED = 2;
   const IDLE = 3;
   const ACTIVE = 4;
   
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
   public function __construct($sensorId, $wcNumber)
   {
      $this->sensorId = $sensorId;
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
      $status = MachineStatus::UNASSIGNED;
      
      if ($this->wcNumber != 0)
      {
         $status = MachineStatus::DISCONNECTED;
         
         $result = $this->database->getSensor($this->sensorId);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $lastContact = new DateTime($row["lastContact"], new DateTimeZone('America/New_York'));
            $lastCount = new DateTime($row["lastCount"], new DateTimeZone('America/New_York'));
            $now = new DateTime(null, new DateTimeZone('America/New_York'));
            
            $countInterval = $now->diff($lastCount);
            $contactInterval = $now->diff($lastContact);
            
            if (($countInterval->d == 0) &&
                ($countInterval->h == 0) &&
                ($countInterval->i == 0))
            {
               if (($countInterval->d == 0) &&
                   ($countInterval->h == 0) &&
                   ($countInterval->i == 0) &&
                   ($countInterval->s < 10))  // TODO: constant, better way
               {
                  $status = MachineStatus::ACTIVE; 
               }
               else if (($contactInterval->d == 0) &&
                        ($contactInterval->h == 0) &&
                        ($contactInterval->i == 0) &&
                        ($contactInterval->s < 10))  // // TODO: constant, better way
               {
                  $status = MachineStatus::IDLE;
               }
            }
         }
      }
      
      return ($status);
   }
   
   private function getStatusTime()
   {
      $statusTime = "49m - Active";  // TODO
      
      return ($statusTime);
   }
   
   private function getPartCount()
   {
      $result = $this->database->getSensor($this->sensorId);
      
      if ($result && ($row = $result->fetch_assoc()))
      {
         $partCount = $row["partCount"];
      }
      
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
   
   private $sensorId = 0;
   
   private $wcNumber = 0;
   
   private $database;
}

?>