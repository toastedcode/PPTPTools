<?php

require_once '../common/filter.php';
require_once '../common/lineInspectionInfo.php';
require_once '../common/navigation.php';
require_once '../common/newIndicator.php';
require_once '../common/permissions.php';
require_once '../common/roles.php';

require 'viewLineInspection.php';

class ViewLineInspections
{
   private $filter;
   
   public function __construct()
   {
      if (isset($_SESSION["lineInspectionFilter"]))
      {
         $this->filter = $_SESSION["lineInspectionFilter"];
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
         
         $this->filter->addByName("operator", new UserFilterComponent("Operator", $operators, $selectedOperator, $allowAll));
         $this->filter->addByName('date', new DateFilterComponent());
         $this->filter->add(new FilterButton());
         $this->filter->add(new FilterDivider());
         $this->filter->add(new TodayButton());
         $this->filter->add(new YesterdayButton());
         $this->filter->add(new ThisWeekButton());
      }
      
      $this->filter->update();
      
      $_SESSION["lineInspectionFilter"] = $this->filter;
   }
   

   public function getHtml()
   {
      $filterDiv = $this->filterDiv();
      
      $partWasherLogDiv = $this->lineInspectionsDiv();
      
      $navBar = $this->navBar();
      
      $html = 
<<<HEREDOC
      <script src="lineInspection.js"></script>
   
      <div class="flex-vertical card-div">
         <div class="card-header-div">View Line Inspections</div>
         <div class="flex-vertical content-div" style="justify-content: flex-start; height:400px;">
   
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
      $navBar->highlightNavButton("New Inspection", "onNewLineInspection()", true);
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private function lineInspectionsDiv()
   {
      $html = 
<<<HEREDOC
         <div class="line-inspections-div">
            <table class="line-inspections-table">
               <tr>
                  <th>Date</th>
                  <th>Inspector</th>
                  <th>Operator</th>
                  <th>Job</th>
                  <th>Work Center</th>
                  <th>Thread #1</th>
                  <th>Thread #2</th>
                  <th>Thread #3</th>
                  <th>Visual</th>
                  <th></th>
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
         
         $result = $database->getLineInspections($this->filter->get('operator')->selectedEmployeeNumber, $startDateString, $endDateString);
        
         if ($result)
         {
            while ($row = $result->fetch_assoc())
            {
               $lineInspectionInfo = LineInspectionInfo::load($row["entryId"]);
               
               if ($lineInspectionInfo)
               {
                  $dateTime = new DateTime($lineInspectionInfo->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                  $inspectionTime = $dateTime->format("m-d-Y");
                  
                  $newIndicator = new NewIndicator($dateTime, 60);
                  $new = $newIndicator->getHtml();
                  
                  $inspectorName = "unknown";
                  $user = UserInfo::load($lineInspectionInfo->inspector);
                  if ($user)
                  {
                     $inspectorName = $user->getFullName();
                  }
                  
                  $operatorName = "unknown";
                  $user = UserInfo::load($lineInspectionInfo->operator);
                  if ($user)
                  {
                     $operatorName = $user->getFullName();
                  }
                  
                  $viewEditIcon = "";
                  $deleteIcon = "";
                  if (Authentication::checkPermissions(Permission::EDIT_PART_WASHER_LOG))  // TODO
                  {
                     $viewEditIcon =
                     "<i class=\"material-icons pan-ticket-function-button\" onclick=\"onEditLineInspection($lineInspectionInfo->entryId)\">mode_edit</i>";
                    
                     $deleteIcon =
                     "<i class=\"material-icons table-function-button\" onclick=\"onDeleteLineInspection($lineInspectionInfo->entryId)\">delete</i>";
                  }
                  else
                  {
                     $viewEditIcon =
                     "<i class=\"material-icons table-function-button\" onclick=\"onViewLineInspection($lineInspectionInfo->entryId)\">visibility</i>";
                  }
                  
                  $html .=
<<<HEREDOC
                     <tr>
                        <td>$inspectionTime $new</td>
                        <td>$inspectorName</td>
                        <td>$operatorName</td>
                        <td>$lineInspectionInfo->jobNumber</td>
                        <td>$lineInspectionInfo->wcNumber</td>
HEREDOC;
                  for ($i = 0; $i < LineInspectionInfo::NUM_INSPECTIONS; $i++)
                  {
                     $label = InspectionStatus::getLabel($lineInspectionInfo->inspections[$i]);
                     $class = InspectionStatus::getClass($lineInspectionInfo->inspections[$i]);
                     
                     $html .= "<td><div class=\"$class\">$label</div></td>";
                  }
                        
                  $html .=
<<<HEREDOC
                        <td>$viewEditIcon</td>
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
   
   private static function getInspectionStatusLabel($inspectionStatus)
   {
      $strings = array("---", "PASS", "FAIL");
      
      return ($strings[$inspectionStatus]);
   }
}

?>