<?php

require_once '../common/filter.php';
require_once '../common/jobInfo.php';
require_once '../common/navigation.php';
require_once '../common/newIndicator.php';
require_once '../common/partWasherEntry.php';
require_once '../common/permissions.php';
require_once '../common/roles.php';
require_once '../common/timeCardInfo.php';

class ViewPartWeightLog
{
   private $filter;
   
   public function __construct()
   {
      $this->filter = new Filter();
      
      if (isset($_SESSION["partWeightFilter"]))
      {
         $this->filter = $_SESSION["partWeightFilter"];
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
            $operators = UserInfo::getUsersByRole(Role::LABORER);
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
      
      $_SESSION["partWeightFilter"] = $this->filter;
   }
   

   public function getHtml()
   {
      $filterDiv = $this->filterDiv();
      
      $partWeightLogDiv = $this->partWeightLogDiv();
      
      $navBar = $this->navBar();
      
      $html = 
<<<HEREDOC
      <script src="partWeightLog.js"></script>

      <div class="flex-vertical content">

         <div class="heading">Part Weight Log</div>

         <div class="description">The Part Weight Log provides an up-to-the-minute view into the part weighing process.  Here you can track the weight of your manufactured parts prior to the washing process.</div>

         <div class="flex-vertical inner-content"> 

            $filterDiv

            $partWeightLogDiv
            
         </div>

         $navBar;

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
      $navBar->highlightNavButton("Weigh Parts", "onNewPartWeightEntry()", true);
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private function partWeightLogDiv()
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
         
         $result = $database->getPartWeightEntries($this->filter->get('operator')->selectedEmployeeNumber, $startDateString, $endDateString);
         
         if ($result && ($database->countResults($result) > 0))
         {
            $html = 
<<<HEREDOC
            <div class="table-container">
               <table class="part-weight-log-table">
                  <tr>
                     <th>Job #</th>
                     <th class="hide-on-tablet">WC #</th>
                     <th class="hide-on-tablet">Operator Name</th>
                     <th class="hide-on-tablet">Mfg. Date</th>
                     <th>Laborer Name</th>
                     <th>Weigh Date</th>
                     <th class="hide-on-tablet">Weigh Time</th>
                     <th class="hide-on-mobile">Basket Count</th>
                     <th>Weight</th>
                     <th></th>
                  </tr>
HEREDOC;
      
            while ($row = $result->fetch_assoc())
            {
               $partWeightEntry = PartWeightEntry::load($row["partWeightEntryId"]);
               
               if ($partWeightEntry)
               {
                  $jobId = $partWeightEntry->getJobId();
                  $operatorEmployeeNumber =  $partWeightEntry->getOperator();
                  $panCount = $partWeightEntry->getPanCount();
                  
                  // If we have a timeCardId, use that to fill in the job id, operator, and manufacture date.
                  $mfgDate = "unknown";
                  $timeCardInfo = TimeCardInfo::load($partWeightEntry->timeCardId);
                  if ($timeCardInfo)
                  {
                     $dateTime = new DateTime($timeCardInfo->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                     $mfgDate = $dateTime->format("m-d-Y");
                  }
                  
                  // Check for a mismatch between the part weight pan count and the part washer man count.
                  $mismatch = "";
                  $partWasherEntry = PartWasherEntry::getPartWasherEntryForJob($jobId);
                  if ($partWasherEntry)
                  {
                     $otherPanCount = $partWasherEntry->getPanCount();
                     
                     if ($panCount != $otherPanCount)
                     {
                        $mismatch = "<span class=\"mismatch-indicator\" tooltip=\"Part washer log count = $otherPanCount\" tooltip-position=\"top\">mismatch</span>";
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
                     $operatorName = $operator->getFullName();
                  }
                     
                  $laborerName = "unknown";
                  $operator = UserInfo::load($partWeightEntry->employeeNumber);
                  if ($operator)
                  {
                     $laborerName = $operator->getFullName();
                  }
                  
                  $dateTime = new DateTime($partWeightEntry->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                  $weighDate = $dateTime->format("m-d-Y");
                     
                  $dateTime = new DateTime($partWeightEntry->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                  $weighTime = $dateTime->format("h:i a");
                     
                  $newIndicator = new NewIndicator($dateTime, 60);
                  $new = $newIndicator->getHtml();
                     
                  $deleteIcon = "";
                  if (Authentication::checkPermissions(Permission::EDIT_PART_WEIGHT_LOG))
                  {
                     $deleteIcon =
                     "<i class=\"material-icons table-function-button\" onclick=\"onDeletePartWeightEntry($partWeightEntry->partWeightEntryId)\">delete</i>";
                  }
   
                  $html .=
<<<HEREDOC
                  <tr>
                     <td>$jobNumber</td>
                     <td class="hide-on-tablet">$wcNumber</td>
                     <td class="hide-on-tablet">$operatorName</td>
                     <td class="hide-on-tablet">$mfgDate</td>
                     <td>$laborerName</td>
                     <td>$weighDate $new</td>
                     <td class="hide-on-tablet">$weighTime</td>
                     <td class="hide-on-mobile">$panCount $mismatch</td>                           
                     <td>$partWeightEntry->weight</td>
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