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
      $statusDiv = ViewJob::statusDiv($jobInfo, $editable);
      
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
               <div class="flex-horizontal" style="align-items: flex-start;">
                  $statusDiv
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
      $html = "";
      
      /*
      $disabled = ($editable) ? "" : "disabled";
      
      $html =
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">
         <div class="section-header-div"><h2>Job</h2></div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Job #</h3></div>
            <input type="number" class="medium-text-input" style="width:150px;" oninput="jobValidator.validate()" value="$panTicketInfo->jobNumber" disabled />
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Work center #</h3></div>
            <input type="text" class="medium-text-input" style="width:150px;" value="$panTicketInfo->wcNumber" disabled />
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Part #</h3></div>
            <input id="partNumber-input" type="text" class="medium-text-input" form="panTicketForm" style="width:150px;" name="partNumber" value="$panTicketInfo->partNumber" $disabled />
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Heat #</h3></div>
            <input id="materialNumber-input" type="text" class="medium-text-input" form="panTicketForm" style="width:150px;" name="materialNumber" value="$panTicketInfo->materialNumber" $disabled />
         </div>
      </div>
HEREDOC;
*/
      
      return ($html);
   }
   
   protected static function partDiv($jobInfo)
   {
      $html = "";
      
      /*
       $html =
       <<<HEREDOC
       <div class="flex-vertical time-card-table-col">
       <div class="section-header-div"><h2>Operator</h2></div>
       <div class="flex-horizontal time-card-table-row">
       <div class="label-div"><h3>Name</h3></div>
       <input type="text" class="medium-text-input" style="width:200px;" value="$name" disabled>
       </div>
       <div class="flex-horizontal time-card-table-row">
       <div class="label-div"><h3>Employee #</h3></div>
       <input type="text" class="medium-text-input" style="width:100px;" value="$panTicketInfo->employeeNumber" disabled>
       </div>
       </div>
       HEREDOC;
       */
      
      return ($html);
   }
   
   protected static function statusDiv($jobInfo, $editable)
   {
      $html = "";
      
      /*
      $disabled = ($editable) ? "" : "disabled";
      
      $weight = $panTicketInfo->weight;
      if (isset($_POST["weight"]))
      {
         $weight = $_POST["weight"];
      }
      
      $html =
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">
         <div class="section-header-div"><h2>Weight</h2></div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Weight</h3></div>
            <input id="weight-input" type="number" class="medium-text-input" form="panTicketForm" style="width:150px;" name="weight" oninput="weightValidator.validate()" value="$weight" $disabled/>
         </div>
      </div>
HEREDOC;
*/
      
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
}
?>