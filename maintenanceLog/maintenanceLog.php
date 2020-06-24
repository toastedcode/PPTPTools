<?php

require_once '../common/authentication.php';
require_once '../common/database.php';
require_once '../common/filter.php';
require_once '../common/header.php';
require_once '../common/navigation.php';
require_once '../common/newIndicator.php';
require_once '../common/maintenanceEntry.php';

function getNavBar()
{
   $navBar = new Navigation();
   
   $navBar->start();
   $navBar->mainMenuButton();
   $navBar->highlightNavButton("New Log Entry", "location.href = 'maintenanceLogEntry.php';", true);
   $navBar->end();
   
   return ($navBar->getHtml());
}

function getFilter()
{
   $filter = null;
   
   if (isset($_SESSION["maintenancerFilter"]))
   {
      $filter = $_SESSION["maintenancerFilter"];
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
         $operators = UserInfo::getUsersByRoles(array(Role::LABORER, Role::PART_WASHER));
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
      
      $filter->addByName("employee", new UserFilterComponent("Employee", $operators, $selectedOperator, $allowAll));
      $filter->addByName('date', new DateFilterComponent());
      $filter->add(new FilterButton());
      $filter->add(new FilterDivider());
      $filter->add(new TodayButton());
      $filter->add(new YesterdayButton());
      $filter->add(new ThisWeekButton());
      $filter->add(new FilterDivider());
      $filter->add(new PrintButton());
      
      $_SESSION["maintenancerFilter"] = $filter;
   }
   
   $filter->update();
   
   return ($filter);
}

function getTable($filter)
{
   $html = "";
   
   global $ROOT;

   // Start date.
   $startDate = new DateTime($filter->get('date')->startDate, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
   $startDateString = $startDate->format("Y-m-d");
   
   // End date.
   // Increment the end date by a day to make it inclusive.
   $endDate = new DateTime($filter->get('date')->endDate, new DateTimeZone('America/New_York'));
   $endDate->modify('+1 day');
   $endDateString = $endDate->format("Y-m-d");
   
   $result = PPTPDatabase::getInstance()->getMaintenanceEntries($filter->get('employee')->selectedEmployeeNumber, $startDateString, $endDateString, false);
   
   if ($result && (MySqlDatabase::countResults($result) > 0))
   {
      $html =
<<<HEREDOC
         <div class="table-container">
            <table class="maintenance-log-table">
               <tr>
                  <th>Date</th>
                  <th>Employee</th>
                  <th>Category</th>
                  <th>Machine #</th>
                  <th>Operator</th>
                  <th>Maintenance time</th>
                  <th class="hide-on-print"/>
                  <th class="hide-on-print"/>
               </tr>
HEREDOC;
         
      while ($row = $result->fetch_assoc())
      {
         $maintenanceEntry = MaintenanceEntry::load($row["maintenanceEntryId"]);
         
         if ($maintenanceEntry)
         {
            $dateTime = new DateTime($maintenanceEntry->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
            $dateTimeString = $dateTime->format("m-d-Y");
            
            $employeeName = "unknown";
            $userInfo = UserInfo::load($maintenanceEntry->employeeNumber);
            if ($userInfo)
            {
               $employeeName = $userInfo->getFullName();
            }
            
            $category = MaintenanceCategory::getLabel($maintenanceEntry->category);
            
            $operatorName = "unknown";
            $userInfo = UserInfo::load($maintenanceEntry->operator);
            if ($userInfo)
            {
               $operatorName = $userInfo->getFullName();
            }
           
            $maintenanceTime = $maintenanceEntry->formatMaintenanceTime();
            
            $newIndicator = new NewIndicator($dateTime, 60);
            $new = $newIndicator->getHtml();
            
            $approval = "";
            if ($maintenanceEntry->isApproved())
            {
               $tooltip = "tooltip=\"Approved\"";
               
               $user = UserInfo::load($maintenanceEntry->approvedBy);
               if ($user)
               {
                  $tooltip = "tooltip=\"Approved by " . $user->getFullName() . "\"";
               }
               
               $approval = "<div class=\"approval approved\" $tooltip>Approved</div>";
            }
            else
            {
               $approval = "<div class=\"approval unapproved\">Unapproved</div>";
            }
            
            $viewEditIcon = "";
            $deleteIcon = "";
            if (Authentication::checkPermissions(Permission::EDIT_PART_WASHER_LOG))
            {
               $viewEditIcon =
               "<a href=\"$ROOT/maintenanceLog/maintenanceLogEntry.php?entryId=$maintenanceEntry->maintenanceEntryId\"><i class=\"material-icons table-function-button\">mode_edit</i></a>";
               $deleteIcon =
               "<i class=\"material-icons table-function-button\" onclick=\"onDeleteMaintenanceEntry($maintenanceEntry->maintenanceEntryId)\">delete</i>";
            }
            else
            {
               $viewEditIcon =
               "<a href=\"$ROOT/maintenanceLog/maintenanceLogEntry.php?entryId=$maintenanceEntry->maintenanceEntryId\"><i class=\"material-icons table-function-button\">visibility</i></a>";
            }
            
            $html .=
<<<HEREDOC
               <tr>
                  <td>$dateTimeString $new</td>
                  <td>$employeeName</td>
                  <td>$category</td>
                  <td>$maintenanceEntry->wcNumber</td>
                  <td>$operatorName</td>
                  <td>$maintenanceTime $approval</td>
                  <td class="hide-on-print">$viewEditIcon</td>
                  <td class="hide-on-print">$deleteIcon</td>
               </tr>
HEREDOC;
         }  // end if ($maintenanceEntry)
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

// ********************************** BEGIN ************************************

Time::init();

session_start();

if (!Authentication::isAuthenticated())
{
   header('Location: ../home.php');
   exit;
}

$filter = getFilter();

// Post/Redirect/Get idiom.
// getFilter() stores all $_POST data in the $_SESSION variable.
// header() redirects to this page, but with a GET request.
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
   // Redirect to this page.
   header("Location: " . $_SERVER['REQUEST_URI']);
   exit();
}
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
   <link rel="stylesheet" type="text/css" href="maintenanceLog.css"/>
   
   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
   <script src="../common/common.js"></script>
   <script src="../common/validate.js"></script>
   <script src="maintenanceLog.js"></script>

</head>

<body>

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="flex-horizontal main">
     
     <div class="flex-horizontal sidebar hide-on-tablet"></div> 
   
     <div class="flex-vertical content">

        <div class="heading">Maintenance Log</div>

        <div class="description">The Maintenance Log ...</div>

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