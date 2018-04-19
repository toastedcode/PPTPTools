<?php

require_once '../common/database.php';
require_once '../common/filter.php';
require_once '../common/navigation.php';
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
         if ($user->permissions & (Permissions::ADMIN | Permissions::SUPER_USER))
         {
            // Allow selection from all operators.
            $operators = UserInfo::getUsers(Permissions::OPERATOR);
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
   
      <div class="flex-vertical card-div">
         <div class="card-header-div">View Part Inspections</div>
         <div class="flex-vertical content-div" style="justify-content: flex-start; height:400px;">
   
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
      $html = 
<<<HEREDOC
         <div class="part-inspections-div">
            <table class="part-inspection-table">
               <tr>
                  <th>Name</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Work Center #</th>
                  <th>Part #</th>
                  <th>Part Count</th>
                  <th>Failure Count</th>
                  <th>Efficiency</th>
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
         
         $result = $database->getPartInspections($this->filter->get('operator')->selectedEmployeeNumber, $startDateString, $endDateString);
         
         if ($result)
         {
            while ($row = $result->fetch_assoc())
            {
               $partInspectionInfo = PartInspectionInfo::load($row["partInspectionId"]);
               
               if ($partInspectionInfo)
               {
                  $operatorName = "unknown";
                  $operator = UserInfo::getUser($partInspectionInfo->employeeNumber);
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