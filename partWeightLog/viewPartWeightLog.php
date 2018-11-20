<?php

require_once '../common/filter.php';
require_once '../common/jobInfo.php';
require_once '../common/navigation.php';
require_once '../common/newIndicator.php';
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
   
      <div class="flex-vertical card-div">
         <div class="card-header-div">View Part Weight Log</div>
         <div class="flex-vertical content-div" style="justify-content: flex-start; height:400px;">
   
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
      $html = 
<<<HEREDOC
         <div class="part-weight-log-div">
            <table class="part-weight-log-table">
               <tr>
                  <th>Job #</th>
                  <th>WC #</th>
                  <th>Operator Name</th>
                  <th>Laborer Name</th>
                  <th>Weigh Date</th>
                  <th>Weigh Time</th>
                  <th>Mfg. Date</th>
                  <th>Basket Count</th>
                  <th>Weight</th>
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
         
         $result = $database->getPartWeightEntries($this->filter->get('operator')->selectedEmployeeNumber, $startDateString, $endDateString);
        
         if ($result)
         {
            while ($row = $result->fetch_assoc())
            {
               $partWeightEntry = PartWeightEntry::load($row["partWeightEntryId"]);
               
               if ($partWeightEntry)
               {
                  // Start with the manually entered values.
                  $jobId = $partWeightEntry->jobId;
                  $operatorEmployeeNumber =  $partWeightEntry->operator;
                  $panCount = 0;  // TODO
                  
                  // If we have a timeCardId, use that to fill in the job id, operator, and manufacture.
                  $mfgDate = "unknown";
                  $timeCardInfo = TimeCardInfo::load($partWeightEntry->timeCardId);
                  if ($timeCardInfo)
                  {
                     $jobId = $timeCardInfo->jobId;
                     
                     $dateTime = new DateTime($timeCardInfo->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                     $mfgDate = $dateTime->format("m-d-Y");
                     
                     $operatorEmployeeNumber = $timeCardInfo->employeeNumber;
                     
                     $panCount = $timeCardInfo->panCount;
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
                        <td>$wcNumber</td>
                        <td>$operatorName</td>
                        <td>$laborerName</td>
                        <td>$weighDate $new</td>
                        <td>$weighTime</td>
                        <td>$mfgDate</td>
                        <td>$panCount</td>                           
                        <td>$partWeightEntry->weight</td>
                        <td>$deleteIcon</td>
                     </tr>
HEREDOC;
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