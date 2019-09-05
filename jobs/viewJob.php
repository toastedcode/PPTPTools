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
      
      $headingDiv = ViewJob::headingDiv($view);
      $descriptionDiv = ViewJob::descriptionDiv($view);
      $creationDiv = ViewJob::creationDiv($jobInfo);
      $jobDiv = ViewJob::jobDiv($jobInfo, $view);
      $partDiv = ViewJob::partDiv($jobInfo, $editable);
      
      $navBar = ViewJob::navBar($jobInfo, $view);
      
      $html =
<<<HEREDOC
      <form id="input-form" action="#" method="POST" enctype="multipart/form-data">
         <input id="job-number-input" type="hidden" name="jobNumber" value="$jobInfo->jobNumber"/>
         <input id="part-number-input" type="hidden" name="partNumber" value="$jobInfo->partNumber"/>
      </form>

      <div class="flex-vertical content">

         $headingDiv

         $descriptionDiv

         <div class="flex-horizontal inner-content" style="justify-content: flex-start; flex-wrap: wrap;"> 
            $creationDiv
            $jobDiv
         </div>
         
         $navBar
               
      </div>
               
      <script>
         var jobNumberPrefixValidator = new PartNumberPrefixValidator("job-number-prefix-input", 5, 1, 9999, false);
         var jobNumberSuffixValidator = new PartNumberSuffixValidator("job-number-suffix-input", 3, 1, 99, false);
         var cycleTimeValidator = new DecimalValidator("cycle-time-input", 5, 1, 60, 2, false);
         var netPercentageValidator = new DecimalValidator("net-percentage-input", 5, 0, 100, 2, false);

         jobNumberPrefixValidator.init();
         jobNumberSuffixValidator.init();
         cycleTimeValidator.init();
         netPercentageValidator.init();

         autoFillPartNumber();
         autoFillPartStats();
      </script>
HEREDOC;
      
      return ($html);
   }
   
   public function render($view)
   {
      echo (ViewJob::getHtml($view));
   }
   
   protected static function headingDiv($view)
   {
      $heading = "";
      if ($view == "new_job")
      {
         $heading = "Create a New Job";
      }
      if ($view == "edit_job")
      {
         $heading = "Edit an Existing Job";
      }
      else if ($view == "view_job")
      {
         $heading = "View a Job";
      }
      
      $html =
<<<HEREDOC
      <div class="heading">$heading</div>
HEREDOC;
      
      return ($html);
   }
   
   protected static function descriptionDiv($view)
   {
      $description = "";
      if ($view == "new_job")
      {
         $description = "Start with a job number and work center.  Cyle time and net percentage can be found in the JobBOSS database for your part.<br/><br/>Once you're satisfied, click Save below to add this time card to the system.";
      }
      else if ($view == "edit_job")
      {
         $description = "You may revise any of the fields for this job and then select save when you're satisfied with the changes.";
      }
      else if ($view == "view_job")
      {
         $description = "View a previously saved job in detail.";
      }
      
      $html =
<<<HEREDOC
      <div class="description">$description</div>
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
      <div class="flex-vertical" style="align-items: flex-start; margin-right: 50px;">
         <div class="form-item">
            <div class="form-label">Creator</div>
            <input type="text" class="form-input-medium" name="date" style="width:180px;" value="$creatorName" disabled />
         </div>
         <div class="form-item">
            <div class="form-label">Date</div>
            <input type="date" class="form-input-medium" name="date" style="width:180px;" value="$date" disabled />
         </div>
      </div>
HEREDOC;

      return ($html);
   }
   
   protected static function jobDiv($jobInfo, $view)
   {
      global $ROOT;
      
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
      for ($status = JobStatus::PENDING; $status <= JobStatus::CLOSED; $status++)
      {
         $statusName = JobStatus::getName($status);
         $selected = ($jobInfo->status == $status) ? "selected" : "";
         $statusOptions .= "<option $selected value=\"" . $status . "\">" . $statusName . "</option>";
      }
      
      $prefix = JobInfo::getJobPrefix($jobInfo->jobNumber);
      $prefix = ($prefix != "") ? $prefix : "M";
      $suffix = JobInfo::getJobSuffix($jobInfo->jobNumber);
      
      $customerPrint = "";
      if ($jobInfo->customerPrint != "")
      {
         $customerPrint =
<<<HEREDOC
         <div class="flex-vertical" style="align-items: flex-start;">
            <a href="$ROOT/uploads/$jobInfo->customerPrint" class="medium-text-input" style="margin-bottom: 10px;" target="_blank">$jobInfo->customerPrint</a> 
            <input type="file" class="medium-text-input" name="customerPrint" form="input-form" $disabled>
         </div>
HEREDOC;
      }
      else
      {
         $customerPrint =
<<<HEREDOC
         <input type="file" class="medium-text-input" name="customerPrint" form="input-form" $disabled>
HEREDOC;
      }
      
      $html =
<<<HEREDOC
      <div class="flex-vertical" style="align-items: flex-start; margin-right: 50px;">

         <div class="form-item">
            <div class="form-label-long">Job #</div>
            <div style="display:flex; flex-direction:row; justify-content:flex-start;">
               <input id="job-number-prefix-input" type="text" class="form-input-medium" name="jobNumberPrefix" form="input-form" style="width:150px;" value="$prefix" oninput="{this.validator.validate(); autoFillPartNumber();}" autocomplete="off" $jobDisabled/>
               <div>&nbsp-&nbsp</div>
               <input id="job-number-suffix-input" type="text" class="form-input-medium" name="jobNumberSuffix" form="input-form" style="width:150px;" value="$suffix" oninput="{this.validator.validate(); autoFillJobNumber();}" autocomplete="off" $jobDisabled/>
            </div>
         </div>
   
         <div class="form-item">
            <div class="form-label-long">Part #</div>
            <input id="part-number-display-input" type="text" class="form-input-medium" style="width:150px;" value="$jobInfo->partNumber" disabled />
         </div>
   
         <div class="form-item">
            <div class="form-label-long">Work center #</div>
            <div><select id="work-center-input" class="form-input-medium" name="wcNumber" form="input-form" $disabled>$wcOptions</select></div>
         </div>
   
         <div class="form-item">
            <div class="form-label-long">Cycle Time</div>
            <input id="cycle-time-input" type="number" class="medium-text-input" name="cycleTime" form="input-form" style="width:150px;" value="$jobInfo->cycleTime" oninput="this.validator.validate(); autoFillPartStats();" $disabled />
         </div>
   
         <div class="form-item">
            <div class="form-label-long">Gross Pieces/Hour</div>
            <input id="gross-parts-per-hour-input" type="number" class="medium-text-input" style="width:150px;" disabled />
         </div>
   
         <div class="form-item">
            <div class="form-label-long">Net Percentage</div>
            <input id="net-percentage-input" type="number" class="medium-text-input" name="netPercentage" form="input-form" style="width:150px;" value="$jobInfo->netPercentage" oninput="this.validator.validate(); autoFillPartStats();" $disabled"/>
            <div class="form-label">&nbsp%</div>
         </div>
   
         <div class="form-item">
            <div class="form-label-long">Net Pieces/Hour</div>
            <input id="net-parts-per-hour-input" type="number" class="medium-text-input" style="width:150px;" disabled />
         </div>
   
         <div class="form-item">
            <div class="form-label-long">Job status</div>
            <div><select id="status-input" class="medium-text-input" name="status" form="input-form" $disabled>$statusOptions</select></div>
         </div>

         <div class="form-item">
            <div class="form-label-long">Customer print</div>
             $customerPrint
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
      
      if (isset($_GET['jobId']))
      {
         $jobInfo = JobInfo::load($_GET['jobId']);
      }
      else if (isset($_POST['jobId']))
      {
         $jobInfo = JobInfo::load($_POST['jobId']);
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