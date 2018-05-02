<?php

require_once '../common/jobInfo.php';
require_once '../common/navigation.php';
require_once '../common/userInfo.php';

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
      $jobDiv = ViewJob::jobDiv($jobInfo, $view);
      $partDiv = ViewJob::partDiv($jobInfo, $editable);
      
      $navBar = ViewJob::navBar($jobInfo, $view);
      
      $title = "";
      if ($view == "new_job")
      {
         $title = "New Job";
      }
      else if ($view == "edit_job")
      {
         $title = "Edit Job";
      }
      else if ($view == "view_job")
      {
         $title = "View Job";
      }
      
      $html =
<<<HEREDOC
      <form id="input-form" action="#" method="POST">
         <input id="job-number-input" type="hidden" name="jobNumber" value="$jobInfo->jobNumber"/>
         <input id="part-number-input" type="hidden" name="partNumber" value="$jobInfo->partNumber"/>
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
               </div>
            </div>
         </div>
         
         $navBar
               
      </div>
               
      <script>
         var jobNumberPrefixValidator = new PartNumberValidator("job-number-prefix-input", 5, 1, 9999, false);
         var jobNumberSuffixValidator = new IntValidator("job-number-suffix-input", 4, 1, 9999, false);
         var cycleTimeValidator = new IntValidator("cycle-time-input", 2, 1, 60, false);
         var netPartsPerHourValidator = new IntValidator("net-parts-per-hour-input", 5, 1, 10000, false);

         jobNumberPrefixValidator.init();
         jobNumberSuffixValidator.init();
         cycleTimeValidator.init();
         netPartsPerHourValidator.init();
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
      if ($userInfo = UserInfo::load($jobInfo->creator))
      {
         $creatorName = $userInfo->getFullName();
      }
       
      $date = date_format(new DateTime($jobInfo->dateTime), "Y-m-d");
      
      $html =
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Creator</h3></div>
            <input type="text" class="medium-text-input" name="date" style="width:180px;" value="$creatorName" disabled />
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Date</h3></div>
            <input type="date" class="medium-text-input" name="date" style="width:180px;" value="$date" disabled />
         </div>
      </div>
HEREDOC;

      return ($html);
   }
   
   protected static function jobDiv($jobInfo, $view)
   {
      $editable = (($view == "new_job") || ($view == "edit_job"));
      $jobEditable = ($view == "new_job");
      
      $disabled = ($editable) ? "" : "disabled";
      $jobDisabled = ($jobEditable) ? "" : "disabled";
      
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

         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Job #</h3></div>
            <input id="job-number-prefix-input" type="text" class="medium-text-input" name="jobNumberPrefix" form="input-form" style="width:150px;" value="$prefix" oninput="{this.validator.validate(); autoFillPartNumber();}" $jobDisabled/>
            <div><h3>&nbsp-&nbsp</h3></div>
            <input id="job-number-suffix-input" type="text" class="medium-text-input" name="jobNumberSuffix" form="input-form" style="width:150px;" value="$suffix" oninput="{this.validator.validate(); autoFillJobNumber();}" $jobDisabled/>
         </div>

         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Part #</h3></div>
            <input id="part-number-display-input" type="text" class="medium-text-input" style="width:150px;" value="$jobInfo->partNumber" disabled />
         </div>

         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Work center #</h3></div>
            <div><select id="work-center-input" class="medium-text-input" name="wcNumber" form="input-form" $disabled>$wcOptions</select></div>
         </div>

         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Cycle Time</h3></div>
            <input id="cycle-time-input" type="number" class="medium-text-input" name="cycleTime" form="input-form" style="width:150px;" value="$jobInfo->cycleTime" oninput="this.validator.validate()" $disabled />
         </div>

         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Net Pieces/Hour</h3></div>
            <input id="net-parts-per-hour-input" type="number" class="medium-text-input" name="netPartsPerHour" form="input-form" style="width:150px;" value="$jobInfo->netPartsPerHour" oninput="this.validator.validate()" $disabled />
         </div>

         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Job status</h3></div>
            <div><select id="status-input" class="medium-text-input" name="status" form="input-form" $disabled>$statusOptions</select></div>
         </div>
      </div>
HEREDOC;
      
      return ($html);
   }
   
   protected static function partDiv($jobInfo, $editable)
   {
      $disabled = ($editable) ? "" : "disabled";
      
       $html =
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">

         <div class="section-header-div"><h2>Part</h2></div>

         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Part #</h3></div>
            <input id="part-number-input" type="text" class="medium-text-input" name="partNumber" form="input-form" style="width:150px;" value="$jobInfo->partNumber" disabled />
         </div>

      </div>
HEREDOC;
      
      return ($html);
   }
   
   protected static function navBar($jobInfo, $view)
   {
      $navBar = new Navigation();
      
      $navBar->start();
      
      if (($view == "new_job") ||
          ($view == "edit_job"))
      {
         // Case 1
         // Creating a new job.
         // Editing an existing job.
         
         $navBar->cancelButton("submitForm('input-form', 'jobs.php', 'view_jobs', 'cancel_job')");
         $navBar->highlightNavButton("Save", "if (validateJob()){submitForm('input-form', 'jobs.php', 'view_jobs', 'save_job');};", false);
      }
      else if ($view == "view_job")
      {
         // Case 2
         // Viewing an existing job.
         
         $navBar->highlightNavButton("Ok", "submitForm('input-form', 'jobs.php', 'view_jobs', 'no_action')", false);
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
               $workcenters[] = $row["wcNumber"];
            }
         }
      }
      
      return ($workcenters);
   }
}
?>