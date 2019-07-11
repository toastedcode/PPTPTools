<?php
require_once 'database.php';

abstract class SelectJob
{
   public function __construct()
   {
   }
   
   public function getHtml()
   {
      $html = "";
      
      $description = $this->description();
      
      $jobsDiv = $this->jobsDiv();
            
      $navBar = $this->navBar();
      
      $html =
<<<HEREDOC
      <form id="input-form" action="#" method="POST"></form>

      <div class="flex-vertical content">

         <div class="heading">Select Your Job Number</div>

         $description

         <div class="flex-vertical inner-content">

            $jobsDiv
            
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
   
   abstract protected function description();
   
   abstract protected function navBar();
   
   abstract protected function getWorkCenterNumber();
   
   abstract protected function getJobId();
   
   private function jobsDiv()
   {
      $html = "";
      
      $wcNumber = $this->getWorkCenterNumber();
      
      $selectedJob = $this->getJobId();
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getActiveJobs($wcNumber);
         
         // output data of each row
         while ($result && ($row = $result->fetch_assoc()))
         {
            $jobId = $row["jobId"];
            $jobNumber = $row["jobNumber"];
            
            $isChecked = ($selectedJob == $jobId);
            
            $html .= SelectJob::jobDiv($jobId, $jobNumber, $isChecked);
         }
      }
      
      return ($html);
   }
   
   private static function jobDiv($jobId, $jobNumber, $isChecked)
   {
      $html = "";
      
      $checked = $isChecked ? "checked" : "";
      
      $id = "list-option-" . $jobId;
      
      $html =
<<<HEREDOC
         <input type="radio" form="input-form" id="$id" class="invisible-radio-button" name="jobId" value="$jobId" $checked/>
         <label for="$id">
            <div type="button" class="select-button job-select-button">
               <i class="material-icons button-icon">assignment</i>
               <div>$jobNumber</div>
            </div>
         </label>
HEREDOC;
      
      return ($html);
   }
}
?>