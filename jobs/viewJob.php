<?php

require_once '../navigation.php';
require_once '../user.php';
require_once '../common/jobInfo.php';

class ViewJob
{
   public function getHtml($view)
   {
      $html = "";
      
      $jobInfo = ViewJob::getJobInfo();
      
      $newJob = ($jobInfo->jobNumber == JobInfo::UNKNOWN_JOB_NUMBER);
      
      $editable = (($view == "new_job") || ($view == "edit_job"));
      
      $titleDiv = ViewJob::titleDiv();
      $creationDiv = ViewJob::creationDiv($jobInfo);
      $jobDiv = ViewJob::jobDiv($jobInfo, $editable);
      $partDiv = ViewJob::partDiv($jobInfo, $editable);
      
      $navBar = ViewJob::navBar($jobInfo, $view);
      
      $title = "";
      if ($view == "new_job")
      {
         $title = "New Job";
      }
      else if ($view == "edit_job")
      {
         $title = "New Job";
      }
      else if ($view == "view_job")
      {
         $title = "View Job";
      }
      
      $html =
<<<HEREDOC
      <form id="input-form" action="#" method="POST">
         <input type="hidden" name="jobNumber" value="$jobInfo->jobNumber"/>
      </form>

      <div class="flex-vertical card-div">
         <div class="card-header-div">$title</div>
         
         <div class="flex-vertical content-div">
            <div class="flex-vertical time-card-div">
               <div class="flex-horizontal">
                  $titleDiv
                  $creationDiv
               </div>
               <div class="flex-horizontal" style="align-items: flex-start;">
                  $jobDiv
                  $partDiv
               </div>
            </div>
         </div>
         
         $navBar
               
      </div>
               
      <script>
      </script>
HEREDOC;
      
      return ($html);
   }
   
   public function render($view)
   {
      echo (ViewJob::getHtml($view));
   }
   
   protected static function titleDiv()
   {
      $html =
<<<HEREDOC
      <div class="flex-horizontal time-card-table-col">
         <h1>Job</h1>
      </div>
HEREDOC;
      
      return ($html);
   }
   
   protected function creationDiv($jobInfo)
   {
      $html = "";
      
      $creatorName = "";
      if ($user = User::getUser($jobInfo->creator))
      {
         $creatorName = $user->getFullName();
      }
       
      $date = date_format(new DateTime($jobInfo->dateTime), "Y-m-d");
      
      $html =
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Creator</h3></div>
            <input type="text" class="medium-text-input" form="panTicketForm" name="date" style="width:180px;" value="$creatorName" disabled />
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Date</h3></div>
            <input type="date" class="medium-text-input" form="panTicketForm" name="date" style="width:180px;" value="$date" disabled />
         </div>
      </div>
HEREDOC;

      return ($html);
   }
   
   protected static function jobDiv($jobInfo, $editable)
   {
      $disabled = ($editable) ? "" : "disabled";
      
      $workcenters = ViewJob::getWorkcenters();
      
      $wcOptions = "";
      foreach ($workcenters as $workcenter)
      {
         $selected = ($jobInfo->wcNumber == $workcenter) ? "selected" : "";
         $wcOptions .= "<option $selected value=\"" . $workcenter . "\">" . $workcenter . "</option>";
      }
      
      $statusOptions = "";
      for ($status = JobStatus::PENDING; $status <= JobStatus::COMPLETE; $status++)
      {
         $statusName = JobStatus::getName($status);
         $selected = ($jobInfo->status == $status) ? "selected" : "";
         $statusOptions .= "<option $selected value=\"" . $status . "\">" . $statusName . "</option>";
      }
      
      $prefix = JobInfo::getJobPrefix($jobInfo->jobNumber);
      $suffix = JobInfo::getJobSuffix($jobInfo->jobNumber);
      
      $html =
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">

         <div class="section-header-div"><h2>Job</h2></div>

         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Job #</h3></div>
            <input id="job-number-prefix-input" type="text" class="medium-text-input" style="width:150px;" value="$prefix" $disabled />
            <div><h3>&nbsp-&nbsp</h3></div>
            <input id="job-number-suffix-input" type="text" class="medium-text-input" style="width:150px;" value="$suffix" $disabled />
         </div>

         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Work center #</h3></div>
            <div><select id="work-center-input" class="medium-text-input" name="wcNumber" $disabled>$wcOptions</select></div>
         </div>

         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Job status</h3></div>
            <div><select id="status-input" class="medium-text-input" name="status" $disabled>$statusOptions</select></div>
         </div>
      </div>
HEREDOC;
      
      return ($html);
   }
   
   protected static function partDiv($jobInfo)
   {
       $html =
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">

         <div class="section-header-div"><h2>Part</h2></div>

         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Part #</h3></div>
            <input id="part-number-prefix-input" type="text" class="medium-text-input" style="width:150px;" value="$jobInfo->partNumber" disabled />
         </div>

      </div>
HEREDOC;
      
      return ($html);
   }
   
   protected static function navBar($jobInfo, $view)
   {
      $navBar = new Navigation();
      
      $navBar->start();
      
      if (($view == "new_job") &&
          ($jobInfo->jobNumber == JobInfo::UNKNOWN_JOB_NUMBER))
      {
         // Case 1
         // Creating a new job.
         
         /*
         $navBar->cancelButton("submitForm('panTicketForm', 'panTicket.php', 'view_pan_tickets', 'cancel_pan_ticket')");
         $navBar->backButton("submitForm('panTicketForm', 'panTicket.php', 'enter_material_number', 'update_pan_ticket_info');");
         $navBar->highlightNavButton("Save", "if (validatePanTicket()){submitForm('panTicketForm', 'panTicket.php', 'view_pan_tickets', 'save_pan_ticket');};", false);
         */
      }
      else if ($view == "edit_job")
      {
         // Case 2
         // Editing an existing job.
         
         /*
         $navBar->cancelButton("submitForm('panTicketForm', 'panTicket.php', 'view_pan_tickets', 'cancel_pan_ticket')");
         $navBar->highlightNavButton("Save", "if (validatePanTicket()){submitForm('panTicketForm', 'panTicket.php', 'view_pan_tickets', 'save_pan_ticket');};", false);
         */
      }
      else if ($view == "view_job")
      {
         // Case 3
         // Viewing an existing job.
         
         /*
         $navBar->printButton("onPrintPanTicket($panTicketInfo->panTicketId)");
         $navBar->highlightNavButton("Ok", "submitForm('panTicketForm', 'panTicket.php', 'view_pan_tickets', 'no_action')", false);
         */
      }
      
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   protected static function getJobInfo()
   {
      $jobInfo = new JobInfo();
      
      if (isset($_GET['jobNumber']))
      {
         $jobInfo = JobInfo::load($_GET['jobNumber']);
      }
      else if (isset($_POST['jobNumber']))
      {
         $jobInfo = JobInfo::load($_POST['jobNumber']);
      }
      else if (isset($_SESSION['jobInfo']))
      {
         $jobInfo = $_SESSION['jobInfo'];
      }
      
      return ($jobInfo);
   }
   
   protected static function getWorkcenters()
   {
      $workcenters = array();
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getWorkCenters();
         
         if ($result)
         {
            while ($row = $result->fetch_assoc())
            {
               $workcenters[] = $row["WCNumber"];
            }
         }
      }
      
      return ($workcenters);
   }
}
?>