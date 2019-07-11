<?php

require_once '../common/database.php';
require_once '../common/filter.php';
require_once '../common/navigation.php';
require_once '../common/newIndicator.php';
require_once '../common/permissions.php';

require_once '../common/userInfo.php';

// *****************************************************************************
//                              OnlyActiveFilterComponent

class OnlyActiveFilterComponent extends FilterComponent
{
   public $onlyActive = false;
   
   public function getHtml()
   {
      $checked = $this->onlyActive ? "checked" : ""; 
      
      $html =
<<<HEREDOC
      <div class="filter-component">
         <input id="only-active-input" type="checkbox" name="onlyActive" value="true" $checked>
         <label for="only-active-input">Active jobs</label>
      </div>
HEREDOC;
      
      return ($html);
   }
   
   public function update()
   {
      if (isset($_POST['onlyActive']))
      {
         $this->onlyActive = boolval($_POST['onlyActive']);
      }
      else
      {
         $this->onlyActive = false;
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
      
         $this->filter->addByName('date', new DateFilterComponent());
         $this->filter->addByName('onlyActive', new OnlyActiveFilterComponent());
         $this->filter->add(new FilterButton());
         $this->filter->add(new FilterDivider());
         $this->filter->add(new ThisWeekButton());
         $this->filter->add(new FilterDivider());
         $this->filter->add(new PrintButton("jobsReport.php"));
      }
      
      $this->filter->update();
      
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

         $navBar;

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
      $html = "";
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         // Start date.
         $startDate = new DateTime($this->filter->get('date')->startDate, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
         $startDateString = $startDate->format("Y-m-d");
         
         // End date.
         // Increment the end date by a day to make it inclusive.
         $endDate = new DateTime($this->filter->get('date')->endDate, new DateTimeZone('America/New_York'));
         $endDate->modify('+1 day');
         $endDateString = $endDate->format("Y-m-d");
         
         $onlyActive = $this->filter->get("onlyActive")->onlyActive;
         
         $result = $database->getJobs($startDateString, $endDateString, $onlyActive);
         
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
                     <th>Work Center #</th>
                     <th class="hide-on-tablet">Cycle Time</th>
                     <th class="hide-on-tablet">Net Percentage</th>
                     <th>Status</th>
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
                  
                  $status = JobStatus::getName($jobInfo->status);
                  
                  $viewEditIcon = "";
                  $deleteIcon = "";
                  if (Authentication::checkPermissions(Permission::EDIT_JOB))
                  {
                     $viewEditIcon =
                        "<i class=\"material-icons pan-ticket-function-button\" onclick=\"onEditJob($jobInfo->jobId)\">mode_edit</i>";
                     
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
                        <td>$jobInfo->wcNumber</td>
                        <td class="hide-on-tablet">$jobInfo->cycleTime</td>
                        <td class="hide-on-tablet">$jobInfo->netPercentage</td>
                        <td>$status</td>
                        <td>$viewEditIcon</td>
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
            $html = "<div class=\"no-data\">No data is available for the selected range.  Use the filter controls above to select a new operator or date range.</div>";
         }
      }
      
      return ($html);
   }
}
?>