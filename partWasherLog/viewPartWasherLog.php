<?php

require_once '../common/filter.php';
require_once '../common/jobInfo.php';
require_once '../common/navigation.php';
require_once '../common/newIndicator.php';
require_once '../common/partWeightEntry.php';
require_once '../common/permissions.php';
require_once '../common/roles.php';
require_once '../common/timeCardInfo.php';

class ViewPartWasherLog
{
   private $filter;
   
   public function __construct()
   {
      if (isset($_SESSION["partWasherFilter"]))
      {
         $this->filter = $_SESSION["partWasherFilter"];
      }
      else
      {
         $user = Authentication::getAuthenticatedUser();
         
         $operators = null;
         $selectedOperator = null;
         $allowAll = false;
         if (Authentication::checkPermissions(Permission::VIEW_OTHER_USERS))
         {
            // Allow selection from all operators.
            $operators = UserInfo::getUsersByRole(Role::PART_WASHER);
            $selectedOperator = "All";
            $allowAll = true;
         }
         else
         {
            // Limit to own logs.
            $operators = array($user);
            $selectedOperator = $user->employeeNumber;
            $allowAll = false;
         }
         
         $this->filter = new Filter();
         
         $this->filter->addByName("operator", new UserFilterComponent("Operator", $operators, $selectedOperator, $allowAll));
         $this->filter->addByName('date', new DateFilterComponent());
         $this->filter->add(new FilterButton());
         $this->filter->add(new FilterDivider());
         $this->filter->add(new TodayButton());
         $this->filter->add(new YesterdayButton());
         $this->filter->add(new ThisWeekButton());
      }
      
      $this->filter->update();
      
      $_SESSION["partWasherFilter"] = $this->filter;
   }
   

   public function getHtml()
   {
      $filterDiv = $this->filterDiv();
      
      $partWasherLogDiv = $this->partWasherLogDiv();
      
      $navBar = $this->navBar();
      
      $html = 
<<<HEREDOC
      <script src="partWasherLog.js"></script>

      <div class="flex-vertical content">

         <div class="heading">Part Washer Log</div>

         <div class="description">The Part Washer Log provides an up-to-the-minute view into the part washing process.  Here you can track when your parts come through the wash line, and in what volume.</div>

         <div class="flex-vertical inner-content"> 

            $filterDiv

            $partWasherLogDiv
            
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
      
   private function filterDiv()
   {
      return ($this->filter->getHtml());
   }
   
   private function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->mainMenuButton();
      $navBar->highlightNavButton("New Log Entry", "onNewPartWasherEntry()", true);
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private function partWasherLogDiv()
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
         
         $result = $database->getPartWasherEntries($this->filter->get('operator')->selectedEmployeeNumber, $startDateString, $endDateString);
         
         if ($result && ($database->countResults($result) > 0))
         {            
            $html = 
<<<HEREDOC
            <div class="table-container">
               <table class="part-washer-log-table">
                  <tr>
                     <th>Job #</th>
                     <th class="hide-on-tablet">WC #</th>
                     <th class="hide-on-tablet">Operator Name</th>
                     <th class="hide-on-tablet">Mfg. Date</th>
                     <th>Washer Name</th>
                     <th>Wash Date</th>
                     <th class="hide-on-tablet">Wash Time</th>
                     <th class="hide-on-mobile">Basket Count</th>
                     <th>Part Count</th>
                     <th></th>
                  </tr>
HEREDOC;

            while ($row = $result->fetch_assoc())
            {
               $partWasherEntry = PartWasherEntry::load($row["partWasherEntryId"]);
               
               if ($partWasherEntry)
               {
                  $jobId = $partWasherEntry->getJobId();
                  $operatorEmployeeNumber =  $partWasherEntry->getOperator();
                  $mismatch = "";
                  
                  // If we have a timeCardId, use that to fill in the job id, operator, and manufacture.
                  $mfgDate = "unknown";
                  $timeCardInfo = TimeCardInfo::load($partWasherEntry->timeCardId);
                  if ($timeCardInfo)
                  {
                     $jobId = $timeCardInfo->jobId;
                     
                     $dateTime = new DateTime($timeCardInfo->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                     $mfgDate = $dateTime->format("m-d-Y");
                     
                     $operatorEmployeeNumber = $timeCardInfo->employeeNumber;
                     
                     if ($partWasherEntry->panCount != $timeCardInfo->panCount)
                     {
                        $mismatch = "<span class=\"mismatch-indicator\" tooltip=\"Time card count = $otherPanCount\" tooltip-position=\"top\">mismatch</span>";
                     }
                  }
                  
                  // Check for a mismatch between the part weight pan count and the part washer man count.
                  $partWeightEntry = PartWeightEntry::getPartWeightEntryForJob($jobId);
                  if ($partWeightEntry)
                  {
                     $otherPanCount = $partWeightEntry->getPanCount();
                     
                     if ($partWasherEntry->panCount != $otherPanCount)
                     {
                        $mismatch = "<span class=\"mismatch-indicator\" tooltip=\"Part weight log count = $otherPanCount\" tooltip-position=\"top\">mismatch</span>";
                     }
                  }
                  
                  // Use the job id to fill in the job number and work center number.
                  $jobNumber = "unknown";
                  $wcNumber = "unknown";
                  $jobInfo = JobInfo::load($jobId);
                  if ($jobInfo)
                  {
                     $jobNumber = $jobInfo->jobNumber;
                     $wcNumber = $jobInfo->wcNumber;
                  }
                  
                  $operatorName = "unknown";
                  $operator = UserInfo::load($operatorEmployeeNumber);
                  if ($operator)
                  {
                     $operatorName= $operator->getFullName();
                  }
                  
                  $partWasherName = "unknown";
                  $washer = UserInfo::load($partWasherEntry->employeeNumber);
                  if ($washer)
                  {
                     $partWasherName= $washer->getFullName();
                  }
                  
                  $dateTime = new DateTime($partWasherEntry->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                  $washDate = $dateTime->format("m-d-Y");
                  
                  $dateTime = new DateTime($partWasherEntry->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                  $washTime = $dateTime->format("h:i a");
                  
                  $newIndicator = new NewIndicator($dateTime, 60);
                  $new = $newIndicator->getHtml();
                                       
                  $deleteIcon = "";
                  if (Authentication::checkPermissions(Permission::EDIT_PART_WASHER_LOG))
                  {
                     $deleteIcon =
                     "<i class=\"material-icons table-function-button\" onclick=\"onDeletePartWasherEntry($partWasherEntry->partWasherEntryId)\">delete</i>";
                  }
   
                  $html .=
<<<HEREDOC
                  <tr>
                     <td>$jobNumber</td>
                     <td class="hide-on-tablet">$wcNumber</td>
                     <td class="hide-on-tablet">$operatorName</td>
                     <td class="hide-on-tablet">$mfgDate</td>
                     <td>$partWasherName</td>
                     <td>$washDate $new</td>
                     <td class="hide-on-tablet">$washTime</td>
                     <td class="hide-on-mobile">$partWasherEntry->panCount $mismatch</td>
                     <td>$partWasherEntry->partCount</td>
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