<?php
require_once '../common/database.php';

abstract class SelectWorkCenter
{
   public function getHtml()
   {
      $html = "";
      
      $workCenters = $this->workCenters();
      
      $navBar = $this->navBar();
      
      $html =
<<<HEREDOC
      <form id="input-form" action="#" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Select Work Center</div>
         <div class="flex-horizontal content-div" style="flex-wrap: wrap; align-items: flex-start;">
            $workCenters
         </div>
         
         $navBar
         
      </div>
HEREDOC;
      
      return ($html);
   }
   
   public function render()
   {
      echo ($this->getHtml());
   }
   
   private function workCenters()
   {
      $html = "";
      
      $selectedWorkCenter = $this->getWorkCenter();
      
      $jobId = $this->getJobId();
      
      $jobInfo = JobInfo::load($jobId);
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getWorkCentersForJob($jobInfo->jobNumber);
         
         // output data of each row
         while ($result && ($row = $result->fetch_assoc()))
         {
            $wcNumber = $row["wcNumber"];
            
            $isChecked = ($selectedWorkCenter == $wcNumber);
            
            $html .= SelectWorkCenter::workCenter($wcNumber, $isChecked);
         }
      }
      
      return ($html);
   }
   
   private static function workCenter($wcNumber, $isChecked)
   {
      $html = "";
      
      $checked = $isChecked ? "checked" : "";
      
      $id = "list-option-" + $wcNumber;
      
      $html =
<<<HEREDOC
         <input type="radio" form="input-form" id="$id" class="operator-input" name="wcNumber" value="$wcNumber" $checked/>
         <label for="$id">
            <div type="button" class="select-button wc-select-button">
               <i class="material-icons button-icon">build</i>
               <div>$wcNumber</div>
            </div>
         </label>
HEREDOC;
      
      return ($html);
   }
   
   abstract protected function navBar();
   
   abstract protected function getWorkCenter();
   
   abstract protected function getJobId();
}
?>