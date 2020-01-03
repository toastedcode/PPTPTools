<?php

require_once '../common/authentication.php';
require_once '../common/database.php';
require_once '../common/filter.php';
require_once '../common/header.php';
require_once '../common/inspection.php';
require_once '../common/inspectionTemplate.php';
require_once '../common/navigation.php';
require_once '../common/newIndicator.php';

// *****************************************************************************
//                            InspectionTypeFilterComponent

class InspectionTypeFilterComponent extends FilterComponent
{
   public $selectedInspectionType;
   
   function __construct($label)
   {
      $this->label = $label;
   }
   
   public function getHtml()
   {
      $all = InspectionType::UNKNOWN;
      
      $selected = "";
      
      $options = "<option $selected value=\"$all\">All</option>";
      
      for ($inspectionType = InspectionType::FIRST; 
           $inspectionType != InspectionType::LAST; 
           $inspectionType++)
      {
         $label = InspectionType::getLabel($inspectionType);
         $selected = ($inspectionType == $this->selectedInspectionType) ? "selected" : "";
         $options .= "<option $selected value=\"$inspectionType\">$label</option>";
      }
      
      $html =
<<<HEREDOC
      <div class="flex-horizontal filter-component hide-on-tablet">
         <div>$this->label:&nbsp</div>
         <div><select id="filter-inspection-type-input" name="filterInspectionType">$options</select></div>
      </div>
HEREDOC;
      
      return ($html);
   }
   
   public function update()
   {
      if (isset($_POST['filterInspectionType']))
      {
         $this->selectedInspectionType = $_POST['filterInspectionType'];
      }
   }
}

// *****************************************************************************

function getNavBar()
{
   $navBar = new Navigation();
   
   $navBar->start();
   $navBar->mainMenuButton();
   $navBar->highlightNavButton("New Inspection", "location.replace('selectInspection.php?view=new_inspection');", true);
   $navBar->end();
   
   return ($navBar->getHtml());
}

function getFilter()
{
   $filter = null;
   
   if (isset($_SESSION["inspectionFilter"]))
   {
      $filter = $_SESSION["inspectionFilter"];
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
      
      $filter = new Filter();
      
      $filter->addByName("inspectionType", new InspectionTypeFilterComponent("Inspection Type"));
      $filter->addByName('jobNumber', new JobNumberFilterComponent("Job Number", JobInfo::getJobNumbers(false), "All"));
      $filter->addByName("operator", new UserFilterComponent("Operator", $operators, $selectedOperator, $allowAll));
      $filter->addByName('date', new DateFilterComponent());
      $filter->add(new FilterButton());
      $filter->add(new FilterDivider());
      $filter->add(new TodayButton());
      $filter->add(new YesterdayButton());
      //$filter->add(new ThisWeekButton());
      $filter->add(new FilterDivider());
      $filter->add(new PrintButton("inspectionReport.php"));
      
      $_SESSION["inspectionFilter"] = $filter;
   }
   
   $filter->update();
   
   return ($filter);
}

function getTable($filter)
{
   $html = "";
   
   global $ROOT;
   
   $filter = getFilter();
   
   // Start date.
   $startDate = new DateTime($filter->get('date')->startDate, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
   $startDateString = $startDate->format("Y-m-d");
   
   // End date.
   // Increment the end date by a day to make it inclusive.
   $endDate = new DateTime($filter->get('date')->endDate, new DateTimeZone('America/New_York'));
   $endDate->modify('+1 day');
   $endDateString = $endDate->format("Y-m-d");
   
   $result = PPTPDatabase::getInstance()->getInspections(
      $filter->get('inspectionType')->selectedInspectionType,
      $filter->get('jobNumber')->selectedJobNumber,
      $filter->get('operator')->selectedEmployeeNumber,
      $startDateString,
      $endDateString);
   
   if ($result && (MySqlDatabase::countResults($result) > 0))
   {
      $html =
<<<HEREDOC
      <div class="table-container">
         <table class="part-weight-log-table">
            <tr>
               <th>Inspection<br/>Type</th>               
               <th>Name</th>
               <th>Date</th>
               <th>Time</th>
               <th>Inspector</th>
               <th>Operator</th>
               <th>Job</th>
               <th>Work<br/>Center</th>
               <th>Success Rate</th>
               <th>PASS/FAIL</th>
               <th></th>
               <th></th>
            </tr>
HEREDOC;
      
      while ($row = $result->fetch_assoc())
      {
         $inspection = Inspection::load($row["inspectionId"]);
         
         $inspectionTemplate = InspectionTemplate::load($row["templateId"]);
         
         if ($inspection && $inspectionTemplate)
         {
            $inspectionTypeLabel = InspectionType::getLabel($inspectionTemplate->inspectionType);
            
            $dateTime = new DateTime($inspection->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
            $inspectionDate = $dateTime->format("m-d-Y");
            $inspectionTime = $dateTime->format("h:i A");
            
            $newIndicator = new NewIndicator($dateTime, 60);
            $new = $newIndicator->getHtml();
            
            $inspectorName = "unknown";
            $user = UserInfo::load($inspection->inspector);
            if ($user)
            {
               $inspectorName = $user->getFullName();
            }
            
            $jobNumber = $inspection->jobNumber;
            $wcNumber = ($inspection->wcNumber != JobInfo::UNKNOWN_WC_NUMBER) ? $inspection->wcNumber : "";
            
            $operatorName = "";
            $user = UserInfo::load($inspection->operator);
            if ($user)
            {
               $operatorName = $user->getFullName();
            }
            
            $inspectionStatus = $inspection->getInspectionStatus();
            $class = InspectionStatus::getClass($inspectionStatus);
            $label = InspectionStatus::getLabel($inspectionStatus);
            $passFail =
<<<HEREDOC
            <div class="inspection-status $class">$label</div>
HEREDOC;

            $viewEditIcon = "";
            $deleteIcon = "";
            if (Authentication::checkPermissions(Permission::EDIT_INSPECTION))
            {
               $viewEditIcon =
                  "<a href=\"$ROOT/inspection/viewInspection.php?inspectionId=$inspection->inspectionId&view=edit_inspection\"><i class=\"material-icons table-function-button\">mode_edit</i></a>";
               $deleteIcon =
                  "<i class=\"material-icons table-function-button\" onclick=\"onDeleteInspection($inspection->inspectionId)\">delete</i>";
            }
            else
            {
               $viewEditIcon =
                  "<a href=\"$ROOT/inspection/viewInspection.php?inspectionId=$inspection->inspectionId&view=view_inspection\"><i class=\"material-icons table-function-button\">visibility</i></a>";
            }
            
            $passCount = $inspection->getCountByStatus(InspectionStatus::PASS);
            $count = ($inspection->getCount() - $inspection->getCountByStatus(InspectionStatus::NON_APPLICABLE));
            
            $html .=
<<<HEREDOC
            <tr>
               <td>$inspectionTypeLabel</td>
               <td>$inspectionTemplate->name</td>
               <td>$inspectionDate $new</td>                        
               <td class="hide-on-tablet">$inspectionTime</td>
               <td class="hide-on-tablet">$inspectorName</td>
               <td class="hide-on-tablet">$operatorName</td>
               <td>$jobNumber</td>
               <td>$wcNumber</td>
               <td>$passCount/$count</td>
               <td>$passFail</td>
               <td>$viewEditIcon</td>
               <td>$deleteIcon</td>
            </tr>
HEREDOC;
         }  // end if ($inspection && $inspectionTemplate)
      }  // end while ($row = $result->fetch_assoc())
      
      $html .=
<<<HEREDOC
         </table>
      </div>
HEREDOC;
   }
   else
   {
      $html = "<div class=\"no-data\">No data is available for the selected range.  Use the filter controls above to select a new operator or date range.</div>";
   }  // end if ($result && (Database::countResults($result) > 0))
   
   return ($html);
}

?>

<!-- ********************************** BEGIN ********************************************* -->

<?php 
Time::init();

session_start();

if (!Authentication::isAuthenticated())
{
   header('Location: ../home.php');
   exit;
}

$filter = getFilter();
?>

<!DOCTYPE html>
<html>

<head>

   <meta name="viewport" content="width=device-width, initial-scale=1">
   
   <link rel="stylesheet" type="text/css" href="../common/flex.css"/>
   <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
   <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css"/>
   <link rel="stylesheet" type="text/css" href="../common/common.css"/>
   <link rel="stylesheet" type="text/css" href="../common/tooltip.css"/>
   <link rel="stylesheet" type="text/css" href="inspection.css"/>
   
   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
   <script src="../common/common.js"></script>
   <script src="../common/validate.js"></script>
   <script src="inspection.js"></script>

</head>

<body>

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="flex-horizontal main">
     
     <!-- div class="flex-horizontal sidebar hide-on-tablet"></div--> 
   
     <div class="flex-vertical content">

        <div class="heading">Inspections</div>

        <div class="description">Part inspections allow Pittsburgh Precision quality assurance experts the chance to catch productions problems before they result in signficant waste or delay.</div>

        <div class="flex-vertical inner-content">
        
           <?php echo $filter->getHtml(); ?>
           
           <?php echo getTable($filter); ?>
      
        </div>
         
        <?php echo getNavBar(); ?>
         
     </div>
     
   </div>
   
   <script>
      preserveSession();
   </script>

</body>

</html>