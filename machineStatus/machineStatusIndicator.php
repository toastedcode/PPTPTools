<?php

require_once '../time.php';

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
         $this->getStatus($status, $uptime);
         
         $wcDescription = $this->wcNumber ? $this->wcNumber : "Unassigned";
         $statusClass = strtolower(MachineStatus::toString($status));
         $statusDescription = $this->getStatusDescription($status, $uptime);
         $partCount = $this->getPartCount();
         $hourlyGraph = $this->getHourlyGraph();
         $startTime = "6am";
         $endTime = "2pm";
         
         $html .=
         <<<HEREDOC
         <div class="flex-vertical machine-status-div $statusClass">
            <div class="flex-vertical machine-status-header-div">
               <div class="flex-horizontal wc-text">$wcDescription</div>
               <div id="status-time-$this->wcNumber" class="flex-horizontal small-text">$statusDescription</div>
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
   
   private function getStatus(&$status, &$uptime)
   {
      $status = MachineStatus::UNASSIGNED;
      $uptime = 0;  // minutes
      
      if ($this->wcNumber != 0)
      {
         $status = MachineStatus::DISCONNECTED;
         
         $result = $this->database->getSensor($this->sensorId);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $lastContact = new DateTime(Time::fromMySqlDate($row["lastContact"], "Y-m-d H:i:s"));
            $lastCount = new DateTime(Time::fromMySqlDate($row["lastCount"], "Y-m-d H:i:s"));
            $now = new DateTime(Time::now("Y-m-d H:i:s"));
            
            $countInterval = $lastCount->diff($now);
            $contactInterval = $lastContact->diff($now);
            
            if (($countInterval->d == 0) &&
                ($countInterval->h == 0) &&
                ($countInterval->i == 0) &&
                ($countInterval->s < 10))  // TODO: constant, better way
            {
               $status = MachineStatus::ACTIVE;
               $uptime = $countInterval;
            }
            else if (($contactInterval->d == 0) &&
                     ($contactInterval->h == 0) &&
                     ($contactInterval->i == 0) &&
                     ($contactInterval->s < 10))  // // TODO: constant, better way
            {
               $status = MachineStatus::IDLE;
               $uptime = $countInterval;
            }
            else
            {
               $status = MachineStatus::DISCONNECTED;
               $uptime = $contactInterval;
            }
         }
      }
      
      return ($status);
   }
   
   private function getStatusDescription($status, $uptime)
   {
      $statusDescription = "";
      
      if ($status == MachineStatus::UNASSIGNED)
      {
         $statusDescription = MachineStatus::toString($status);
      }
      else
      {
         if ($uptime->d > 0)
         {
            $statusDescription = $uptime->d . "d " . $uptime->h . "h";
         }
         else if ($uptime->h > 0)
         {
            $statusDescription = $uptime->h . "h " . $uptime->i . "m";
         }
         else
         {
            $statusDescription = $uptime->i . "m ";
         }
            
         $statusDescription .= " - " . MachineStatus::toString($status);
      }
      
      return ($statusDescription);
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