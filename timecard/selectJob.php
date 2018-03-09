<?php
require_once '../database.php';

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
      
      $selectedJob = SelectJob::getJobNumber();
      
      $wcNumber = SelectJob::getWorkCenter();
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getActiveJobs($wcNumber);
         
         // output data of each row
         while ($result && ($row = $result->fetch_assoc()))
         {
            $jobNumber = $row["jobNumber"];
            
            $isChecked = ($selectedJob == $jobNumber);
            
            $html .= SelectJob::jobDiv($jobNumber, $isChecked);
         }
      }
      
      return ($html);
   }
   
   private static function jobDiv($jobNumber, $isChecked)
   {
      $html = "";
      
      $checked = $isChecked ? "checked" : "";
      
      $id = "list-option-" + $jobNumber;
      
      $html =
<<<HEREDOC
         <input type="radio" form="input-form" id="$id" class="operator-input" name="jobNumber" value="$jobNumber" $checked/>
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
      $navBar->nextButton("if (validateJob()){submitForm('input-form', 'timeCard.php', 'enter_time', 'update_time_card_info');};");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function getJobNumber()
   {
      $jobNumber = null;
      
      if (isset($_SESSION['timeCardInfo']))
      {
         $jobNumber = $_SESSION['timeCardInfo']->jobNumber;
      }
      
      return ($jobNumber);
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
         $jobInfo = JobInfo::load($_SESSION['timeCardInfo']->jobNumber);
         
         if ($jobInfo)
         {
            $wcNumber = $jobInfo->wcNumber;
         }
      }
      
      return ($wcNumber);
   }
}
?>