<?php

require_once '../common/filter.php';
require_once '../common/inspectionInfo.php';
require_once '../common/navigation.php';
require_once '../common/newIndicator.php';
require_once '../common/permissions.php';
require_once '../common/roles.php';

//require 'viewInspection.php';

class ViewInspections
{
   private $filter;
   
   public function __construct()
   {
      if (isset($_SESSION["inspectionFilter"]))
      {
         $this->filter = $_SESSION["inspectionFilter"];
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
         $this->filter->addByName('jobNumber', new JobNumberFilterComponent("Job Number", JobInfo::getJobNumbers(false), "All"));
         $this->filter->addByName('date', new DateFilterComponent());
         $this->filter->add(new FilterButton());
         $this->filter->add(new FilterDivider());
         $this->filter->add(new TodayButton());
         $this->filter->add(new YesterdayButton());
         //$this->filter->add(new ThisWeekButton());
         $this->filter->add(new FilterDivider());
         $this->filter->add(new PrintButton("inspectionReport.php"));
      }
      
      $this->filter->update();
      
      $_SESSION["inspectionFilter"] = $this->filter;
   }
   

   public function getHtml()
   {
      $filterDiv = $this->filterDiv();
      
      $inspectionsDiv = $this->inspectionsDiv();
      
      $navBar = $this->navBar();
      
      $html = 
<<<HEREDOC
      <script src="inspection.js"></script>
   
      <div class="flex-vertical content">

         <div class="heading">Inspections</div>

         <div class="description">Part inspections allow Pittsburgh Precision quality assurance experts the chance to catch productions problems before they result in signficant waste or delay.</div>

         <div class="flex-vertical inner-content"> 

            $filterDiv
   
            $inspectionsDiv
            
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
      
      if (Authentication::checkPermissions(Permission::EDIT_INSPECTION))
      {
         $navBar->highlightNavButton("New Inspection", "onNewInspection()", true);
      }
      
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private function inspectionsDiv()
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
         
         $result = $database->getInspections(InspectionType::UNKNOWN,  // TODO: Use filter value.
                                             $this->filter->get('operator')->selectedEmployeeNumber, 
                                             $this->filter->get('jobNumber')->selectedJobNumber,
                                             $startDateString, 
                                             $endDateString);
        
         if ($result && ($database->countResults($result) > 0))
         {
            $html =
<<<HEREDOC
            <div class="table-container">
               <table class="line-inspections-table">
                  <tr>
                     <th>Date</th>
                     <th class="hide-on-tablet">Time</th>
                     <th class="hide-on-tablet">Type</th>
                     <th class="hide-on-tablet">Inspector</th>
                     <th class="hide-on-tablet">Operator</th>
                     <th>Job</th>
                     <th>Work<br/>Center</th>
                     <!--th class="hide-on-tablet">Thread #1</th>
                     <th class="hide-on-tablet">Thread #2</th>
                     <th class="hide-on-tablet">Thread #3</th>
                     <th class="hide-on-tablet">Visual</th>
                     <th class="hide-on-tablet">Undercut</th>
                     <th class="hide-on-tablet">Depth</th-->
                     <th></th>
                     <th></th>
                  </tr>
HEREDOC;
            
            while ($row = $result->fetch_assoc())
            {
               $inspectionInfo = InspectionInfo::load($row["inspectionId"]);
               
               if ($inspectionInfo)
               {
                  $dateTime = new DateTime($inspectionInfo->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                  $inspectionDate = $dateTime->format("m-d-Y");
                  $inspectionTime = $dateTime->format("h:i A");
                  
                  $newIndicator = new NewIndicator($dateTime, 60);
                  $new = $newIndicator->getHtml();
                  
                  $inspectorName = "unknown";
                  $user = UserInfo::load($inspectionInfo->inspector);
                  if ($user)
                  {
                     $inspectorName = $user->getFullName();
                  }
                  
                  $operatorName = "unknown";
                  $user = UserInfo::load($inspectionInfo->operator);
                  if ($user)
                  {
                     $operatorName = $user->getFullName();
                  }
                  
                  $jobNumber = "";
                  $wcNumber = "";
                  $jobInfo = JobInfo::load($inspectionInfo->jobId);
                  if ($jobInfo)
                  {
                     $jobNumber = $jobInfo->jobNumber;
                     $wcNumber = $jobInfo->wcNumber;
                  }
                  
                  $inspectionType = InspectionType::getLabel($inspectionInfo->inspectionType);
                  
                  $viewEditIcon = "";
                  $deleteIcon = "";
                  if (Authentication::checkPermissions(Permission::EDIT_INSPECTION))
                  {
                     $viewEditIcon =
                     "<i class=\"material-icons pan-ticket-function-button\" onclick=\"onEditInspection($inspectionInfo->inspectionId)\">mode_edit</i>";
                    
                     $deleteIcon =
                     "<i class=\"material-icons table-function-button\" onclick=\"onDeleteInspection($inspectionInfo->inspectionId)\">delete</i>";
                  }
                  else
                  {
                     $viewEditIcon =
                     "<i class=\"material-icons table-function-button\" onclick=\"onViewInspection($inspectionInfo->inspectionId)\">visibility</i>";
                  }
                  
                  $html .=
<<<HEREDOC
                     <tr>
                        <td>$inspectionDate $new</td>                        
                        <td class="hide-on-tablet">$inspectionTime</td>
                        <td>$inspectionType</td>
                        <td class="hide-on-tablet">$inspectorName</td>
                        <td class="hide-on-tablet">$operatorName</td>
                        <td>$jobNumber</td>
                        <td>$wcNumber</td>
HEREDOC;
                  /*
                  for ($i = 0; $i < InspectionInfo::NUM_INSPECTIONS; $i++)
                  {
                     $label = InspectionStatus::getLabel($inspectionInfo->inspections[$i]);
                     $class = InspectionStatus::getClass($inspectionInfo->inspections[$i]);
                     
                     $html .= "<td class=\"hide-on-mobile\"><div class=\"$class\">$label</div></td>";
                  }
                  */
                        
                  $html .=
<<<HEREDOC
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