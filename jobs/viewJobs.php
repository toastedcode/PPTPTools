<?php

require_once '../common/database.php';
require_once '../common/filter.php';
require_once '../common/jobInfo.php';
require_once '../common/navigation.php';
require_once '../common/newIndicator.php';
require_once '../common/permissions.php';

require_once '../common/userInfo.php';

// *****************************************************************************
//                              JobStatusFilterComponent

class JobStatusFilterComponent extends FilterComponent
{
   public $jobStatusChecked = array();
   
   public function __construct()
   {
      for ($jobStatus = JobStatus::FIRST; $jobStatus < JobStatus::LAST; $jobStatus++)
      {
         $this->jobStatusChecked[] = false;
      }
      
      $this->jobStatusChecked[JobStatus::PENDING] = true;
      $this->jobStatusChecked[JobStatus::ACTIVE] = true;
   }
   
   public function getHtml()
   {
      $html = "<div class=\"filter-component\">";
      
      for ($jobStatus = JobStatus::FIRST; $jobStatus < JobStatus::LAST; $jobStatus++)
      {
         if ($jobStatus != JobStatus::DELETED)
         {
            $checked = $this->jobStatusChecked[$jobStatus] ? "checked" : "";
            
            $label = JobStatus::getName($jobStatus);
            
            $name = strtolower($label);
            
            $id = "filter-" . $name . "-input";
            
            $html .=
<<<HEREDOC
               <input type="hidden" name="$name" value="0"/>
               <input id="$id" type="checkbox" name="$name" value="true" $checked/>
               <label for="$id">$label</label>
HEREDOC;
         }
      }
      
      $html .= "</div>";
      
      return ($html);
   }
   
   public function update()
   {
      for ($jobStatus = JobStatus::FIRST; $jobStatus < JobStatus::LAST; $jobStatus++)
      {
         $name = strtolower(JobStatus::getName($jobStatus));
         
         if (isset($_POST[$name]))
         {
            $this->jobStatusChecked[$jobStatus] = boolval($_POST[$name]);
         }
      }
   }
}

// *****************************************************************************
//                                   ViewJobs

class ViewJobs
{
   private $filter;
   
   public function __construct()
   {
      if (isset($_SESSION["jobFilter"]))
      {
         $this->filter = $_SESSION["jobFilter"];
      }
      else
      {
         $this->filter = new Filter();
      
         $this->filter->addByName('jobNumber', new JobNumberFilterComponent("Job Number", JobInfo::getJobNumbers(false), "All"));
         $this->filter->addByName('jobStatus', new JobStatusFilterComponent());
         $this->filter->add(new FilterButton());
         $this->filter->add(new FilterDivider());
         $this->filter->add(new PrintButton("jobsReport.php"));
      }
      
      $this->filter->update();
      $this->filter->get("jobNumber")->updateJobNumbers(JobInfo::getJobNumbers(false));
      
      $_SESSION["jobFilter"] = $this->filter;
   }

   public function getHtml()
   {
      $filterDiv = ViewJobs::filterDiv();
      
      $jobsDiv = ViewJobs::jobsDiv();
      
      $navBar = ViewJobs::navBar();
      
      $html = 
<<<HEREDOC
      <script src="jobs.js"></script>
   
      <div class="flex-vertical content">

         <div class="heading">Jobs</div>

         <div class="description">
            Tracking production all starts with the creation of Job.  Your active jobs are the ones available to your operators for creating Time Sheets
         </div>

         <div class="flex-vertical inner-content"> 
   
            $filterDiv
   
            $jobsDiv
         
         </div>

         $navBar

      </div>
HEREDOC;
      
      return ($html);
   }
   
   public function render()
   {
      echo (ViewJobs::getHtml());
   }
      
   private function filterDiv()
   {
      return ($this->filter->getHtml());
   }
   
   private function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->mainMenuButton();
      $navBar->highlightNavButton("New Job", "onNewJob()", true);
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private function jobsDiv()
   {
      global $ROOT;
      
      $html = "";
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getJobs($this->filter->get("jobNumber")->selectedJobNumber,
                                      $this->filter->get("jobStatus")->jobStatusChecked);
         
         if ($result && ($database->countResults($result) > 0))
         {
            $html = 
<<<HEREDOC
            <div class="table-container">
               <table class="job-table">
                  <tr>
                     <th>Job Number</th>
                     <th>Author</th>                  
                     <th>Date</th>
                     <th class="hide-on-tablet">Part #</th>
                     <th>Sample Weight</th>
                     <th>Work Center #</th>
                     <th class="hide-on-tablet">Cycle Time</th>
                     <th class="hide-on-tablet">Net Percentage</th>
                     <th class="hide-on-tablet">Customer Print</th>
                     <th>Status</th>
                     <th/>
                     <th/>
                     <th/>
                  </tr>
HEREDOC;

            while ($row = $result->fetch_assoc())
            {
               $jobInfo = JobInfo::load($row["jobId"]);
               
               if ($jobInfo)
               {
                  $creatorName = "unknown";
                  $user = UserInfo::load($jobInfo->creator);
                  if ($user)
                  {
                     $creatorName= $user->getFullName();
                  }
                  
                  $dateTime = new DateTime($jobInfo->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                  $date = $dateTime->format("m-d-Y");
                  
                  $newIndicator = new NewIndicator($dateTime, 60);
                  $new = $newIndicator->getHtml();
                  
                  $customerPrint = "";
                  if ($jobInfo->customerPrint != "")
                  {
                     $truncatedFilename = strlen($jobInfo->customerPrint) > 15 ? substr($jobInfo->customerPrint, 0, 15) . "..." : $jobInfo->customerPrint; 
                     $customerPrint = "<a href=\"$ROOT/uploads/$jobInfo->customerPrint\" target=\"_blank\">$truncatedFilename</a>";
                  }
                  
                  $status = JobStatus::getName($jobInfo->status);
                  
                  $viewEditIcon = "";
                  $copyIcon = "";
                  $deleteIcon = "";
                  if (Authentication::checkPermissions(Permission::EDIT_JOB))
                  {
                     $viewEditIcon =
                        "<i class=\"material-icons pan-ticket-function-button\" onclick=\"onEditJob($jobInfo->jobId)\">mode_edit</i>";
                     
                     $copyIcon =
                     "<i class=\"material-icons pan-ticket-function-button\" onclick=\"onCopyJob($jobInfo->jobId)\">file_copy</i>";
                     
                     
                     if ($jobInfo->status != JobStatus::DELETED)
                     {
                        $deleteIcon =
                           "<i class=\"material-icons pan-ticket-function-button\" onclick=\"onDeleteJob($jobInfo->jobId)\">delete</i>";
                     }
                  }
                  else
                  {
                     $viewEditIcon =
                        "<i class=\"material-icons table-function-button\" onclick=\"onViewJob($jobInfo->jobId)\">visibility</i>";
                  }
                  
                  $html .=
<<<HEREDOC
                     <tr>
                        <td>$jobInfo->jobNumber</td>
                        <td>$creatorName</td>
                        <td>$date $new</td>
                        <td class="hide-on-tablet">$jobInfo->partNumber</td>
                        <td class="hide-on-tablet">$jobInfo->sampleWeight</td>
                        <td>$jobInfo->wcNumber</td>
                        <td class="hide-on-tablet">$jobInfo->cycleTime</td>
                        <td class="hide-on-tablet">$jobInfo->netPercentage</td>
                        <td class="hide-on-tablet">$customerPrint</td>
                        <td>$status</td>
                        <td>$viewEditIcon</td>
                        <td>$copyIcon</td>
                        <td>$deleteIcon</td>
                     </tr>
HEREDOC;
               }
            }
            
            $html .=
<<<HEREDOC
               </table>
            </div>
HEREDOC;
         }
         else
         {
            $html = "<div class=\"no-data\">No data is available for the selected range.  Use the filter controls above to select a different job or status.</div>";
         }
      }
      
      return ($html);
   }
}
?>