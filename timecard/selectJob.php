<?php
require_once '../common/database.php';

class SelectJob
{
   public static function getHtml()
   {
      $html = "";
      
      $jobsDiv = SelectJob::jobsDiv();
            
      $navBar = SelectJob::navBar();
      
      $html =
<<<HEREDOC
      <form id="input-form" action="#" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Select Job</div>
         <div class="flex-horizontal content-div" style="flex-wrap: wrap; align-items: flex-start;">
            $jobsDiv
         </div>
         
         $navBar
         
      </div>
HEREDOC;
      
      return ($html);
   }
   
   public static function render()
   {
      echo (SelectJob::getHtml());
   }
   
   private static function jobsDiv()
   {
      $html = "";
      
      $selectedJob = SelectJob::getJobId();
      
      $wcNumber = SelectJob::getWorkCenter();
      
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
      
      $id = "list-option-" + $jobId;
      
      $html =
<<<HEREDOC
         <input type="radio" form="input-form" id="$id" class="operator-input" name="jobId" value="$jobId" $checked/>
         <label for="$id">
            <div type="button" class="select-button job-select-button">
               <i class="material-icons button-icon">assignment</i>
               <div>$jobNumber</div>
            </div>
         </label>
HEREDOC;
      
      return ($html);
   }
   
   private static function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('input-form', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
      $navBar->backButton("submitForm('input-form', 'timeCard.php', 'select_work_center', 'update_time_card_info');");
      $navBar->nextButton("if (validateJob()){submitForm('input-form', 'timeCard.php', 'enter_material_number', 'update_time_card_info');};");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function getJobId()
   {
      $jobId = null;
      
      if (isset($_SESSION['timeCardInfo']))
      {
         $jobId= $_SESSION['timeCardInfo']->jobId;
      }
      
      return ($jobId);
   }
   
   private static function getWorkCenter()
   {
      $wcNumber = null;
      
      if (isset($_POST['wcNumber']))
      {
         $wcNumber = $_POST['wcNumber'];
      }
      else if (isset($_SESSION['timeCardInfo']))
      {
         $jobInfo = JobInfo::load($_SESSION['timeCardInfo']->jobId);
         
         if ($jobInfo)
         {
            $wcNumber = $jobInfo->wcNumber;
         }
      }
      
      return ($wcNumber);
   }
}
?>