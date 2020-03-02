<?php

require_once '../common/database.php';
require_once '../common/filter.php';
require_once '../common/navigation.php';
require_once '../common/permissions.php';
require_once '../common/roles.php';
require_once '../common/userInfo.php';

class ViewPartInspections
{
   private $filter;
   
   public function __construct()
   {
      if (isset($_SESSION["partInspectionFilter"]))
      {
         $this->filter = $_SESSION["partInspectionFilter"];
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
            $operators = UserInfo::getUsersByRole(Role::OPERATOR);
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
         
         $this->filter->addByName("inspector", new UserFilterComponent("Inspector", $operators, $selectedOperator, $allowAll));
         $this->filter->addByName('date', new DateFilterComponent());
         $this->filter->add(new FilterButton());
         $this->filter->add(new FilterDivider());
         $this->filter->add(new TodayButton());
         $this->filter->add(new YesterdayButton());
         $this->filter->add(new ThisWeekButton());
         $this->filter->add(new FilterDivider());
         $this->filter->add(new PrintButton());
      }
      
      $this->filter->update();
      
      $_SESSION["partInspectionFilter"] = $this->filter;
   }
   
   public function getHtml()
   {
      $filterDiv = ViewPartInspections::filterDiv();
      
      $partInspectionsDiv = ViewPartInspections::partInspectionsDiv();
      
      $navBar = ViewPartInspections::navBar();
      
      $html = 
<<<HEREDOC
      <script src="partInspection.js"></script>
   
      <div class="flex-vertical content">

         <div class="heading">Oasis Part Inspections</div>

         <div class="description">
            Part inspections performed at any of the Oasis inspection stations automatically show up in this log.  Real-time inspection data lets you keep close tabs on the qualitiy control process.<br/>
            <br/>
            If you feel there are missing inspections from this log, verify that the <b>Folder Watcher</b> application is running at your Oasis inspection station.
         </div>

         <div class="flex-vertical inner-content"> 

            $filterDiv
   
            $partInspectionsDiv
            
         </div>

         $navBar;

      </div>
HEREDOC;
      
      return ($html);
   }
   
   public function render()
   {
      echo (ViewPartInspections::getHtml());
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
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private function partInspectionsDiv()
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
         
         $result = $database->getPartInspections($this->filter->get('inspector')->selectedEmployeeNumber, $startDateString, $endDateString);
         
         if ($result && ($database->countResults($result) > 0))
         {    
            $html = 
<<<HEREDOC
            <div class="table-container">
               <table class="part-inspection-table">
                  <tr>
                     <th>Inspector</th>
                     <th>Date</th>
                     <th>Time</th>
                     <th>Work Center #</th>
                     <th>Part #</th>
                     <th>Part Count</th>
                     <th>Failure Count</th>
                     <th>Efficiency</th>
                  </tr>
HEREDOC;

            while ($row = $result->fetch_assoc())
            {
               $partInspectionInfo = PartInspectionInfo::load($row["partInspectionId"]);
               
               if ($partInspectionInfo)
               {
                  $operatorName = "unknown";
                  $operator = UserInfo::load($partInspectionInfo->employeeNumber);
                  if ($operator)
                  {
                     $operatorName = $operator->getFullName();
                  }
                  
                  $dateTime = new DateTime($partInspectionInfo->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                  $date = $dateTime->format("m-d-Y");
                  $time = $dateTime->format("h:i a");
                  
                  $inspectionClass = ($partInspectionInfo->failures > 0) ? "failed-inspection" : "good-inspection"; 
                  
                  $workCenter = ($partInspectionInfo->wcNumber == 0) ? "unknown" : $partInspectionInfo->wcNumber;

                  $html .=
<<<HEREDOC
                     <tr class="$inspectionClass">
                        <td>$operatorName</td>
                        <td>$date</td>
                        <td>$time</td>
                        <td>$workCenter</td>
                        <td>$partInspectionInfo->partNumber</td>
                        <td>$partInspectionInfo->partCount</td>
                        <td>$partInspectionInfo->failures</td>
                        <td>$partInspectionInfo->efficiency</td>
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