<?php

require_once '../common/database.php';
require_once '../common/header.php';
require_once '../common/jobInfo.php';
require_once '../common/navigation.php';
require_once '../common/params.php';
require_once '../common/partWasherEntry.php';
require_once '../common/timeCardInfo.php';

const ONLY_ACTIVE = true;

function getView()
{
   $params = Params::parse();
   
   return ($params->keyExists("view") ? $params->get("view") : "");
}

function getPartWasherEntry()
{
   static $partWasherEntry = null;
   
   if ($partWasherEntry == null)
   {
      $params = Params::parse();

      if ($params->keyExists("entryId"))
      {
         $partWasherEntry = PartWasherEntry::load($params->get("entryId"));
      }
      else if (isset($_SESSION['partWasherEntry']))
      {
         $partWasherEntry = $_SESSION['partWasherEntry'];
      }
   }
   
   return ($partWasherEntry);
}

function getNavBar()
{
   $navBar = new Navigation();
   
   $navBar->start();
   
   $view = getView();
   
   if (($view == "new_part_washer_entry") ||
       ($view == "edit_part_washer_entry"))
   {
      // Case 1
      // Creating a new entry.
      // Editing an existing entry.
      
      $navBar->cancelButton("submitForm('input-form', 'partWasherLog.php', 'view_part_washer_log', 'cancel_part_washer_entry')");
      $navBar->highlightNavButton("Save", "submitForm('input-form', 'partWasherLog.php', 'view_part_washer_log', 'save_part_washer_entry');", false);
   }
   else if ($view == "view_part_washer_entry")
   {
      // Case 2
      // Viewing an existing entry.
      
      $navBar->highlightNavButton("Ok", "submitForm('input-form', 'partWasherLog.php', 'view_part_washer_log', 'no_action')", false);
   }
   
   $navBar->end();
   
   return ($navBar->getHtml());
}

function isEditable()
{
   $view = getView();
   
   return (($view == "new_part_washer_entry") ||
           ($view == "edit_part_washer_entry"));
}

function getHeading()
{
   $heading = "";
   
   $view = getView();
   
   if ($view == "new_part_washer_entry")
   {
      $heading = "Add to the Part Washer Log";
   }
   else if ($view == "edit_part_washer_entry")
   {
      $heading = "Update the Part Washer Log";
   }
   else if ($view == "view_part_washer_entry")
   {
      $heading = "View a Part Washer Log Entry";
   }
   
   return ($heading);
}

function getDescription()
{
   $description = "";
   
   $view = getView();
   
   if ($view == "new_part_washer_entry")
   {
      $description = "<TODO>Start by selecting a work center, then any of the currently active jobs for that station.  If any of the categories are not relevant to the part you're inspecting, just leave it set to \"N/A\"";
   }
   else if ($view == "edit_part_washer_entry")
   {
      $description = "You may revise any of the fields for this log entry and then select save when you're satisfied with the changes.";
   }
   else if ($view == "view_part_washer_entry")
   {
      $description = "View a previously saved log entry in detail.";
   }
   
   return ($description);
}

function getJobNumberOptions()
{   
   $options = "";
   
   $jobNumbers = JobInfo::getJobNumbers(ONLY_ACTIVE);
   
   $selectedJobNumber = getJobNumber();
   
   foreach ($jobNumbers as $jobNumber)
   {
      $selected = ($jobNumber == $selectedJobNumber) ? "selected" : "";
      
      $options .= "<option value=\"{$jobNumber}\" $selected>{$jobNumber}</option>";
   }
   
   return ($options);
}

function getWcNumberOptions()
{
   $options = "";
   
   $workCenters = PPTPDatabase::getInstance()->getWorkCenters();
   
   foreach ($workCenters as $workCenter)
   {
      $selected = "";  // TODO
      
      $options .= "<option value=\"{$workCenter["wcNumber"]}\" $selected>{$workCenter["wcNumber"]}</option>";
   }
   
   return ($options);
}

function getManufactureDate()
{
   $manufactureDate = Time::now(Time::$javascriptDateFormat);
   
   $partWasherEntry = getPartWasherEntry();
   
   if ($partWasherEntry)
   {
      $timeCardId = $partWasherEntry->timeCardId;
      
      if (getTimeCardId() != 0)
      {
         $timeCardInfo = TimeCardInfo::load($timeCardId);
         
         if ($timeCardInfo)
         {
            $dateTime = new DateTime($timeCardInfo->dateTime, new DateTimeZone('America/New_York'));
            $manufactureDate = $dateTime->format(Time::$javascriptDateFormat);
         }
      }
      else if ($partWasherEntry->manufactureDate)
      {
         $dateTime = new DateTime($partWasherEntry->manufactureDate, new DateTimeZone('America/New_York'));
         $manufactureDate = $dateTime->format(Time::$javascriptDateFormat);
      }
   }
   
   return ($manufactureDate);
}

function getOperatorOptions()
{
   $options = "";
   
   $operators = PPTPDatabase::getInstance()->getUsersByRole(Role::OPERATOR);
   
   foreach ($operators as $operator)
   {
      $userInfo = UserInfo::load($operator["employeeNumber"]);
      if ($userInfo)
      {
         $selected = "";  // TODO
         
         $name = $userInfo->employeeNumber . " - " . $userInfo->getFullName();
         
         $options .= "<option value=\"$userInfo->employeeNumber\" $selected>$name</option>";
      }
   }
   
   return ($options);
}

function getLaborerOptions()
{
   $options = "";
   
   $operators = PPTPDatabase::getInstance()->getUsersByRole(Role::LABORER);
   
   foreach ($operators as $operator)
   {
      $userInfo = UserInfo::load($operator["employeeNumber"]);
      if ($userInfo)
      {
         $selected = "";  // TODO
         
         $name = $userInfo->employeeNumber . " - " . $userInfo->getFullName();
         
         $options .= "<option value=\"$userInfo->employeeNumber\" $selected>$name</option>";
      }
   }
   
   return ($options);
}

function getTimeCardId()
{
   $timeCardId = 0;
   
   $partWasherEntry = getPartWasherEntry();
   
   if ($partWasherEntry)
   {
      $timeCardId = $partWasherEntry->timeCardId;
   }
   
   return ($timeCardId);
}

function getJobNumber()
{
   $jobNumber = "";
   
   $partWasherEntry = getPartWasherEntry();
   
   if ($partWasherEntry)
   {
      $timeCardId = $partWasherEntry->timeCardId;
      
      if (getTimeCardId() != 0)
      {
         $timeCardInfo = TimeCardInfo::load($timeCardId);
         
         if ($timeCardInfo)
         {
            $jobInfo = JobInfo::load($timeCardInfo->jobId);
            
            if ($jobInfo)
            {
               $jobNumber = $jobInfo->jobNumber;
            }
         }
      }
      else
      {
         $jobNumber = $partWasherEntry->jobNumber;
      }
   }
   
   return ($jobNumber);
}

function getPanCount()
{
   $panCount = null;
   
   $partWasherEntry = getPartWasherEntry();
   
   if ($partWasherEntry)
   {
      $panCount = $partWasherEntry->panCount;
   }
   
   return ($panCount);
}

function getPartCount()
{
   $partCount = null;
   
   $partWasherEntry = getPartWasherEntry();
   
   if ($partWasherEntry)
   {
      $partCount = $partWasherEntry->partCount;
   }
   
   return ($partCount);
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
   <link rel="stylesheet" type="text/css" href="partWasherLog.css"/>
   
   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
   <script src="partWasherLog.js"></script>
   <script src="../common/validate.js"></script>

</head>

<body>

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="flex-horizontal main">
     
     <div class="flex-horizontal sidebar hide-on-tablet"></div> 
   
      <form id="input-form" action="#" method="POST">
      
      <div class="flex-vertical content">
      
         <div class="heading"><?php echo getHeading(); ?></div>
         
         <div class="description"><?php echo getDescription(); ?></div>
         
         <div class="pptp-form">
            <div class="form-row">
               <div class="form-col">
               
                  <div class="form-item">
                     <div class="form-label">Time Card ID</div>
                     <input id="time-card-id-input" class="form-input-medium" type="number" name="timeCardId" form="input-form" oninput="" value="<?php echo getTimeCardId(); ?>" <?php echo !isEditable() ? "disabled" : ""; ?>>
                  </div>               
               
                  <div class="form-item">
                     <div class="form-label">Job Number</div>
                     <select id="job-number-input" class="form-input-medium" name="jobNumber" form="input-form" oninput="updateWCNumberInput();" <?php echo !isEditable() ? "disabled" : ""; ?>>
                        <?php echo getJobNumberOptions(); ?>
                     </select>
                  </div>
                  
                  <div class="form-item">
                     <div class="form-label">Work Center</div>
                     <select id="wc-number-input" class="form-input-medium" name="wcNumber" form="input-form" oninput="" <?php echo !isEditable() ? "disabled" : ""; ?>>
                        <?php echo getWcNumberOptions(); ?>
                     </select>
                  </div>
                  
                  <div class="flex-horizontal">
                     <div class="form-item">
                        <div class="form-label">Manufacture Date</div>
                        <div class="flex-horizontal">
                           <input id="manufacture-date-input" class="form-input-medium" type="date" name="manufactureDate" form="input-form" oninput="" value="<?php echo getManufactureDate(); ?>" <?php echo !isEditable() ? "disabled" : ""; ?>>
                           <button onclick="">Today</button>
                           <button onclick="">Yesterday</button>
                        </div>
                     </div>
                  </div>
                  
                  <div class="form-item">
                     <div class="form-label">Operator</div>
                     <select id="operator-input" class="form-input-medium" name="operator" form="input-form" oninput="" <?php echo !isEditable() ? "disabled" : ""; ?>>
                        <?php echo getOperatorOptions(); ?>
                     </select>
                  </div>
                  
                  <div class="form-item">
                     <div class="form-label">Laborer</div>
                     <select id="laborer-input" class="form-input-medium" name="laborer" form="input-form" oninput="" <?php echo !isEditable() ? "disabled" : ""; ?>>
                        <?php echo getLaborerOptions(); ?>
                     </select>
                  </div>
                  
                  <div class="form-item">
                     <div class="form-label">Pan Count</div>
                     <input id="pan-count-input" class="form-input-medium" type="number" name="panCount" form="input-form" oninput="" value="<?php echo getPanCount(); ?>" <?php echo !isEditable() ? "disabled" : ""; ?>>
                  </div>
                  
                  <div class="form-item">
                     <div class="form-label">Part Count</div>
                     <input id="part-count-input" class="form-input-medium" type="number" name="partCount" form="input-form" oninput="" value="<?php echo getPartCount(); ?>" <?php echo !isEditable() ? "disabled" : ""; ?>>
                  </div>
                  
               </div>
            </div>
         </div>
         
         <?php echo getNavBar(); ?>
         
      </div>
      
      </form>
      
      <script>
         updateWCNumberInput();
      </script>
     
   </div>

</body>

</html>