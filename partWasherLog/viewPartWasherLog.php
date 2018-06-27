<?php

require_once '../common/filter.php';
require_once '../common/jobInfo.php';
require_once '../common/navigation.php';
require_once '../common/newIndicator.php';
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
   
      <div class="flex-vertical card-div">
         <div class="card-header-div">View Part Washer Log</div>
         <div class="flex-vertical content-div" style="justify-content: flex-start; height:400px;">
   
               $filterDiv
   
               $partWasherLogDiv
         
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
      $navBar->highlightNavButton("New Log Entry", "onNewPartWasherEntry()", true);
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private function partWasherLogDiv()
   {
      $html = 
<<<HEREDOC
         <div class="part-washer-log-div">
            <table class="part-washer-log-table">
               <tr>
                  <th>Job #</th>
                  <th>Washer Name</th>
                  <th>Wash Date</th>
                  <th>Mfg. Date</th>
                  <th>Basket Count</th>
                  <th>Part Count</th>
                  <th></th>
               </tr>
HEREDOC;
      
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
        
         if ($result)
         {
            while ($row = $result->fetch_assoc())
            {
               $partWasherEntry = PartWasherEntry::load($row["partWasherEntryId"]);
               
               if ($partWasherEntry)
               {
                  $timeCardInfo = TimeCardInfo::load($partWasherEntry->timeCardId);
                  
                  $jobInfo = JobInfo::load($timeCardInfo->jobNumber);
                  
                  if ($timeCardInfo && $jobInfo)
                  {
                     $partWasherName = "unknown";
                     $operator = UserInfo::load($partWasherEntry->employeeNumber);
                     if ($operator)
                     {
                        $partWasherName= $operator->getFullName();
                     }
                     
                     $dateTime = new DateTime($partWasherEntry->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                     $washDate = $dateTime->format("m-d-Y");
                     
                     $newIndicator = new NewIndicator($dateTime, 60);
                     $new = $newIndicator->getHtml();
                                          
                     $dateTime = new DateTime($timeCardInfo->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                     $mfgDate = $dateTime->format("m-d-Y");
                     
                     $deleteIcon = "";
                     if (Authentication::checkPermissions(Permission::EDIT_PART_WASHER_LOG))
                     {
                        $deleteIcon =
                        "<i class=\"material-icons table-function-button\" onclick=\"onDeletePartWasherEntry($partWasherEntry->partWasherEntryId)\">delete</i>";
                     }
   
                     $html .=
<<<HEREDOC
                        <tr>
                           <td>$jobInfo->jobNumber</td>
                           <td>$partWasherName</td>
                           <td>$washDate $new</td>
                           <td>$mfgDate</td>
                           <td>$partWasherEntry->panCount</td>
                           <td>$partWasherEntry->partCount</td>
                           <td>$deleteIcon</td>
                        </tr>
HEREDOC;
                  }
               }
            }
         }
      }
      
      $html .=
<<<HEREDOC
            </table>
         </div>
HEREDOC;
      
      return ($html);
   }
}
?>