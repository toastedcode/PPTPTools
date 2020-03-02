<?php

require_once '../common/authentication.php';
require_once '../common/database.php';
require_once '../common/filter.php';
require_once '../common/header.php';
require_once '../common/jobInfo.php';
require_once '../common/navigation.php';
require_once '../common/newIndicator.php';
require_once '../common/permissions.php';
require_once '../common/roles.php';
require_once '../common/timeCardInfo.php';
require_once '../common/userInfo.php';

function getNavBar()
{  
   $navBar = new Navigation();
   
   $navBar->start();
   $navBar->mainMenuButton();
   $navBar->highlightNavButton("New Time Card", "location.href = 'viewTimeCard.php';", true);
   $navBar->end();
   
   return ($navBar->getHtml());
}

function getFilter()
{
   $filter = null;
   
   if (isset($_SESSION["timeCardFilter"]))
   {
      $filter = $_SESSION["timeCardFilter"];
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
         // Limit to own time cards.
         $operators = array($user);
         $selectedOperator = $user->employeeNumber;
         $allowAll = false;
      }
      
      $filter = new Filter();
      
      $filter->addByName("operator", new UserFilterComponent("Operator", $operators, $selectedOperator, $allowAll));
      $filter->addByName('date', new DateFilterComponent());
      $filter->add(new FilterButton());
      $filter->add(new FilterDivider());
      $filter->add(new TodayButton());
      $filter->add(new YesterdayButton());
      $filter->add(new ThisWeekButton());
      $filter->add(new FilterDivider());
      $filter->add(new PrintButton());
      
      $_SESSION["timeCardFilter"] = $filter;
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
   
   $result = PPTPDatabase::getInstance()->getTimeCards($filter->get('operator')->selectedEmployeeNumber, $startDateString, $endDateString);
   
   if ($result && (MySqlDatabase::countResults($result) > 0))
   {
      $html =
<<<HEREDOC
      <div class="table-container">
         <table class="time-card-table">
            <tr>
               <th>Date</th>
               <th>Operator</th>
               <th>Job #</th>
               <th class="hide-on-mobile">Machine #</th>
               <th class="hide-on-tablet">Heat #</th>
               <th class="hide-on-tablet">Run Time</th>
               <th class="hide-on-tablet">Setup Time</th>
               <th class="hide-on-tablet">Basket Count</th>
               <th>Part Count</th>
               <th class="hide-on-tablet">Scrap Count</th>
               <th class="hide-on-tablet">Efficiency</th>
               <th class="hide-on-print"/>
               <th class="hide-on-print"/>
               <th class="hide-on-print"/>
            </tr>
HEREDOC;
      
      while ($row = $result->fetch_assoc())
      {
         $timeCardInfo = TimeCardInfo::load($row["timeCardId"]);
         
         if ($timeCardInfo)
         {
            $operatorName = "unknown";
            $user = UserInfo::load($timeCardInfo->employeeNumber);
            if ($user)
            {
               $operatorName= $user->getFullName();
            }
            
            $dateTime = new DateTime($timeCardInfo->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
            $date = $dateTime->format("n-j-Y");
            
            $newIndicator = new NewIndicator($dateTime, 60);
            $new = $newIndicator->getHtml();
            
            $wcNumber = "unknown";
            $jobInfo = JobInfo::load($timeCardInfo->jobId);
            if ($jobInfo)
            {
               $wcNumber = $jobInfo->wcNumber;
            }
            
            $approval = "no-approval-required";
            $tooltip = "";
            if ($timeCardInfo->requiresApproval())
            {
               if ($timeCardInfo->isApproved())
               {
                  $approval = "approved";
                  
                  $user = UserInfo::load($timeCardInfo->approvedBy);
                  if ($user)
                  {
                     $tooltip = "tooltip=\"Approved by " . $user->getFullName() . "\"";
                  }
                  else
                  {
                     $tooltip = "tooltip=\"Approved\"";
                  }
               }
               else
               {
                  $approval = "unapproved";
               }
            }
            
            $incompleteTime = "";
            $incompletePanCount = "";
            $incompletePartCount = "";
            if ($timeCardInfo->incompleteTime())
            {
               $incompleteTime = "<span class=\"incomplete-indicator\">incomplete</span>";
            }
            else if ($timeCardInfo->incompletePanCount())
            {
               $incompletePanCount = "<span class=\"incomplete-indicator\">incomplete</span>";
            }
            else if ($timeCardInfo->incompletePartCount())
            {
               $incompletePartCount = "<span class=\"incomplete-indicator\">incomplete</span>";
            }
            
            $efficiency = number_format($timeCardInfo->getEfficiency(), 2);
            
            $viewEditIcon = "";
            $deleteIcon = "";
            if (Authentication::checkPermissions(Permission::EDIT_TIME_CARD))
            {
               $viewEditIcon =
               //"<i class=\"material-icons table-function-button\" onclick=\"onEditTimeCard('$timeCardInfo->timeCardId')\">mode_edit</i>";
               "<a href=\"$ROOT/timecard/viewTimeCard.php?timeCardId=$timeCardInfo->timeCardId\"><i class=\"material-icons table-function-button\">mode_edit</i></a>";
               
               $deleteIcon =
               "<i class=\"material-icons table-function-button\" onclick=\"onDeleteTimeCard('$timeCardInfo->timeCardId')\">delete</i>";
            }
            else
            {
               $viewEditIcon =
               //"<i class=\"material-icons table-function-button\" onclick=\"onViewTimeCard('$timeCardInfo->timeCardId')\">visibility</i>";
               "<a href=\"$ROOT/timecard/viewTimeCard.php?timeCardId=$timeCardInfo->timeCardId\"><i class=\"material-icons table-function-button\">visibility</i></a>";
            }
            
            $panTicketIcon =
            "<a href=\"$ROOT/panTicket/viewPanTicket.php?panTicketId=$timeCardInfo->timeCardId\"><i class=\"material-icons table-function-button\">receipt</i></a>";
            
            
            $html .=
<<<HEREDOC
            <tr>
               <td>$date $new</td>
               <td>$operatorName</td>
               <td>$jobInfo->jobNumber</td>
               <td class="hide-on-mobile">$wcNumber</td>
               <td class="hide-on-tablet">$timeCardInfo->materialNumber</td>
               <td class="hide-on-tablet">{$timeCardInfo->formatRunTime()} $incompleteTime</td>
               <td class="$approval hide-on-tablet">
                  {$timeCardInfo->formatSetupTime()}
                  <div class="approval $approval" $tooltip>Approved</div>
                  <div class="unapproval $approval">Unapproved</div>
               </td>
               <td class="hide-on-tablet">$timeCardInfo->panCount $incompletePanCount</td>
               <td>$timeCardInfo->partCount $incompletePartCount</td>
               <td class="hide-on-tablet">$timeCardInfo->scrapCount</td>
               <td class="hide-on-tablet">$efficiency%</td>
               <td class="hide-on-print">$viewEditIcon</td>
               <td class="hide-on-print">$panTicketIcon</td>
               <td class="hide-on-print">$deleteIcon</td>
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
   <link rel="stylesheet" type="text/css" href="timeCard.css"/>
   
   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
   <script src="../common/common.js"></script>
   <script src="../common/validate.js"></script>
   <script src="timeCard.js"></script>

</head>

<body>

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="flex-horizontal main">
     
     <div class="flex-horizontal sidebar hide-on-tablet"></div> 
   
     <div class="flex-vertical content">

        <div class="heading">Time Cards</div>

        <div class="description">Time cards record the time a machine operator spends working on a job, as well as a part count for that run.</div>

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