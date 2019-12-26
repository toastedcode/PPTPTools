<?php

require_once '../common/authentication.php';
require_once '../common/database.php';
require_once '../common/filter.php';
require_once '../common/header.php';
require_once '../common/navigation.php';
require_once '../common/newIndicator.php';
require_once '../common/partWasherEntry.php';
require_once '../common/partWeightEntry.php';
require_once '../common/timeCardInfo.php';

function getNavBar()
{
   $navBar = new Navigation();
   
   $navBar->start();
   $navBar->mainMenuButton();
   $navBar->highlightNavButton("New Log Entry", "location.href = 'partWeightLogEntry.php';", true);
   $navBar->end();
   
   return ($navBar->getHtml());
}

function getFilter()
{
   $filter = null;
   
   if (isset($_SESSION["partWeightFilter"]))
   {
      $filter = $_SESSION["partWeightFilter"];
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
      
      $filter = new Filter();
      
      $filter->addByName("laborer", new UserFilterComponent("Laborer", $operators, $selectedOperator, $allowAll));
      $filter->addByName('date', new DateFilterComponent());
      $filter->add(new FilterButton());
      $filter->add(new FilterDivider());
      $filter->add(new TodayButton());
      $filter->add(new YesterdayButton());
      $filter->add(new ThisWeekButton());
      $filter->add(new FilterDivider());
      $filter->add(new PrintButton("partWeightReport.php"));
      
      $_SESSION["partWeightFilter"] = $filter;
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
   
   $result = PPTPDatabase::getInstance()->getPartWeightEntries(JobInfo::UNKNOWN_JOB_ID, $filter->get('laborer')->selectedEmployeeNumber, $startDateString, $endDateString, false);
   
   if ($result && (MySqlDatabase::countResults($result) > 0))
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
               <th class="hide-on-mobile">Estimated<br>Part Count</th>
               <th></th>
               <th></th>
            </tr>
HEREDOC;
         
      while ($row = $result->fetch_assoc())
      {
         $partWeightEntry = PartWeightEntry::load($row["partWeightEntryId"]);
         
         if ($partWeightEntry)
         {
            $jobId = JobInfo::UNKNOWN_JOB_ID;
            $operatorEmployeeNumber =  UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
            $panCount = 0;
            $mismatch = "";
            
            // If we have a timeCardId, use that to fill in the job id, operator, and manufacture.
            $mfgDate = null;
            $timeCardInfo = TimeCardInfo::load($partWeightEntry->timeCardId);
            if ($timeCardInfo)
            {
               $jobId = $timeCardInfo->jobId;
               
               $mfgDate = $timeCardInfo->dateTime;
               
               $operatorEmployeeNumber = $timeCardInfo->employeeNumber;
               
               $panCount = $timeCardInfo->panCount;
            }
            else
            {
               $jobId = $partWeightEntry->getJobId();
               $operatorEmployeeNumber =  $partWeightEntry->getOperator();
               $panCount = $partWeightEntry->panCount;
               
               if ($partWeightEntry->manufactureDate)
               {
                  $mfgDate = $partWeightEntry->manufactureDate;
               }
            }
            
            //
            // Check for a mismatch between the Part Weight Log pan count and the Part Washer Log pan count.
            //
            
            if ($mfgDate)
            {
               $partWeightLogPanCount = PartWeightEntry::getPanCountForJob($jobId, Time::startOfDay($mfgDate), Time::endOfDay($mfgDate));
               $partWasherLogPanCount = PartWasherEntry::getPanCountForJob($jobId, Time::startOfDay($mfgDate), Time::endOfDay($mfgDate));
               
               // Check for a mismatch.
               if ($partWeightLogPanCount != $partWasherLogPanCount)
               {
                  $mismatch = "<span class=\"mismatch-indicator\" tooltip=\"weight log = $partWeightLogPanCount; wash log = $partWasherLogPanCount\" tooltip-position=\"top\">mismatch</span>";
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
               $operatorName= $operator->getFullName();
            }
            
            $laborerName = "unknown";
            $laborer = UserInfo::load($partWeightEntry->employeeNumber);
            if ($laborer)
            {
               $laborerName= $laborer->getFullName();
            }
            
            if ($mfgDate)
            {
               $dateTime = new DateTime($mfgDate, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
               $mfgDate = $dateTime->format("m-d-Y");
            }
            else
            {
               $mfgDate = "---";
            }
            
            $dateTime = new DateTime($partWeightEntry->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
            $weighDate = $dateTime->format("m-d-Y");
            
            $dateTime = new DateTime($partWeightEntry->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
            $weighTime = $dateTime->format("h:i a");
            
            $newIndicator = new NewIndicator($dateTime, 60);
            $new = $newIndicator->getHtml();
            
            $viewEditIcon = "";
            $deleteIcon = "";
            if (Authentication::checkPermissions(Permission::EDIT_PART_WASHER_LOG))
            {
               $viewEditIcon =
               "<a href=\"$ROOT/partWeightLog/partWeightLogEntry.php?entryId=$partWeightEntry->partWeightEntryId&view=edit_part_weight_entry\"><i class=\"material-icons table-function-button\">mode_edit</i></a>";
               $deleteIcon =
               "<i class=\"material-icons table-function-button\" onclick=\"onDeletePartWeightEntry($partWeightEntry->partWeightEntryId)\">delete</i>";
            }
            else
            {
               $viewEditIcon =
               "<a href=\"$ROOT/partWeightLog/partWeightLogEntry.php?entryId=$partWeightEntry->partWeightEntryId&view=view_part_weight_entry\"><i class=\"material-icons table-function-button\">visibility</i></a>";
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
               <td>{$partWeightEntry->calculatePartCount()}</td>
               <td>$viewEditIcon</td>
               <td>$deleteIcon</td>
            </tr>
HEREDOC;
         }  // end if ($partWeightEntry)
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
   <link rel="stylesheet" type="text/css" href="partWeightLog.css"/>
   
   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
   <script src="partWeightLog.js"></script>
   <script src="../common/validate.js"></script>
   <script src="partWeightLog.js"></script>

</head>

<body>

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="flex-horizontal main">
     
     <div class="flex-horizontal sidebar hide-on-tablet"></div> 
   
     <div class="flex-vertical content">

        <div class="heading">Part Weight Log</div>

        <div class="description">The Part Weight Log provides an up-to-the-minute view into the part weighing process.  Here you can track the weight of your manufactured parts prior to the washing process.</div>

        <div class="flex-vertical inner-content">
        
           <?php echo $filter->getHtml(); ?>
           
           <?php echo getTable($filter); ?>
      
        </div>
         
        <?php echo getNavBar(); ?>
         
     </div>
     
   </div>

</body>

</html>