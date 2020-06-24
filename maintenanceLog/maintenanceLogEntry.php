<?php

require_once '../common/database.php';
require_once '../common/header.php';
require_once '../common/jobInfo.php';
require_once '../common/maintenanceEntry.php';
require_once '../common/navigation.php';
require_once '../common/panTicket.php';
require_once '../common/params.php';
require_once '../common/timeCardInfo.php';
require_once '../common/userInfo.php';

abstract class MaintenanceLogInputField
{
   const FIRST = 0;
   const MAINTENANCE_ENTRY_ID = MaintenanceLogInputField::FIRST;
   const DATE = 1;
   const EMPLOYEE_NUMBER = 2;
   const CATEGORY = 3;
   const WC_NUMBER = 4;
   const OPERATOR = 5;
   const MAINTENANCE_TIME = 6;
   const COMMENTS = 7;
   const APPROVED_BY = 8;
   const LAST = 9;
   const COUNT = MaintenanceLogInputField::LAST - MaintenanceLogInputField::FIRST;
}

abstract class View
{
   const NEW_MAINTENANCE_LOG_ENTRY = 0;
   const VIEW_MAINTENANCE_LOG_ENTRY = 1;
   const EDIT_MAINTENANCE_LOG_ENTRY = 2;
}

function getParams()
{
   static $params = null;
   
   if (!$params)
   {
      $params = Params::parse();
   }
   
   return ($params);
}

function getView()
{
   $view = View::VIEW_MAINTENANCE_LOG_ENTRY;
   
   if (getEntryId() == MaintenanceEntry::UNKNOWN_ENTRY_ID)
   {
      $view = View::NEW_MAINTENANCE_LOG_ENTRY;
   }
   else if (Authentication::checkPermissions(Permission::EDIT_MAINTENANCE_LOG))
   {
      $view = View::EDIT_MAINTENANCE_LOG_ENTRY;
   }
   
   return ($view);
}

function getMaintenanceEntry()
{
   static $maintenanceEntry = null;
   
   if ($maintenanceEntry == null)
   {
      $params = Params::parse();

      if ($params->keyExists("entryId"))
      {
         $maintenanceEntry = MaintenanceEntry::load($params->get("entryId"));
      }
   }
   
   return ($maintenanceEntry);
}

function getEntryId()
{
   $entryId = MaintenanceEntry::UNKNOWN_ENTRY_ID;
   
   $maintenanceEntry = getMaintenanceEntry();
   
   if ($maintenanceEntry)
   {
      $entryId = $maintenanceEntry->maintenanceEntryId;
   }
   
   return ($entryId);
}

function getNavBar()
{
   $navBar = new Navigation();
   
   $navBar->start();
   
   $view = getView();
   
   if (($view == View::NEW_MAINTENANCE_LOG_ENTRY) ||
       ($view == View::EDIT_MAINTENANCE_LOG_ENTRY))
   {
      // Case 1
      // Creating a new entry.
      // Editing an existing entry.
      
      $navBar->cancelButton("window.history.back();");
      $navBar->highlightNavButton("Save", "onSubmit();", false);
   }
   else if ($view == View::VIEW_MAINTENANCE_LOG_ENTRY)
   {
      // Case 2
      // Viewing an existing entry.
      
      $navBar->highlightNavButton("Ok", "submitForm('input-form', 'maintenanceLog.php', '', '')", false);
   }
   
   $navBar->end();
   
   return ($navBar->getHtml());
}

function isEditable($field)
{
   $view = getView();
   
   // Start with the edit mode, as dictated by the view.
   $isEditable = (($view == View::NEW_MAINTENANCE_LOG_ENTRY) ||
                  ($view == View::EDIT_MAINTENANCE_LOG_ENTRY));
   
   switch ($field)
   {  
      /*
      case MaintenanceLogInputField::OPERATOR:
      case MaintenanceLogInputField::WC_NUMBER:
      {
         // Edit status enabled for machine-based maintenance.
         $isEditable &= (getCategory() == MaintenanceCategory::CATEGORY_1);
         break;
      }
      */

      default:
      {
         // Edit status based solely on view.
         break;
      }
   }

   return ($isEditable);
}

function getHeading()
{
   $heading = "";
   
   $view = getView();
   
   if ($view == View::NEW_MAINTENANCE_LOG_ENTRY)
   {
      $heading = "Add to the Maintenance Log";
   }
   else if ($view == View::EDIT_MAINTENANCE_LOG_ENTRY)
   {
      $heading = "Update the Maintenance Log";
   }
   else if ($view == View::VIEW_MAINTENANCE_LOG_ENTRY)
   {
      $heading = "View a Maintenance Log Entry";
   }
   
   return ($heading);
}

function getDescription()
{
   $description = "";
   
   $view = getView();
   
   if ($view == View::NEW_MAINTENANCE_LOG_ENTRY)
   {
      $description = "Create a new entry in the maintenance log.";
   }
   else if ($view == View::EDIT_MAINTENANCE_LOG_ENTRY)
   {
      $description = "You may revise any of the fields for this log entry and then select save when you're satisfied with the changes.";
   }
   else if ($view == View::VIEW_MAINTENANCE_LOG_ENTRY)
   {
      $description = "View a previously saved log entry in detail.";
   }
   
   return ($description);
}

function getWcNumberOptions()
{
   $options = "<option style=\"display:none\">";
   
   $workCenters = PPTPDatabase::getInstance()->getWorkCenters();
      
   $selectedWcNumber = getWcNumber();
   
   foreach ($workCenters as $workCenter)
   {
      $selected = ($workCenter["wcNumber"] == $selectedWcNumber) ? "selected" : "";
      
      $options .= "<option value=\"{$workCenter["wcNumber"]}\" $selected>{$workCenter["wcNumber"]}</option>";
   }
   
   return ($options);
}

function getMaintenanceDate()
{
   $maintenanceDate = null;
   
   $maintenanceEntry = getMaintenanceEntry();
   
   if ($maintenanceEntry)
   {
      $dateTime = new DateTime($maintenanceEntry->dateTime, new DateTimeZone('America/New_York'));
      $maintenanceDate = $dateTime->format(Time::$javascriptDateFormat);
   }
   
   return ($maintenanceDate);
}

function getEmployeeNumberOptions()
{
   $options = "<option style=\"display:none\">";
   
   $operators = PPTPDatabase::getInstance()->getUsersByRole(Role::OPERATOR);
   
   // Create an array of employee numbers.
   $employeeNumbers = array();
   foreach ($operators as $operator)
   {
      $employeeNumbers[] = intval($operator["employeeNumber"]);
   }
   
   $selectedEmployeeNumber = getEmployeeNumber();
   
   // Add selected employee number, if not already in the array.
   // Note: This handles the case of viewing an entry with an operator that is not assigned to the OPERATOR role.
   if (($selectedEmployeeNumber != UserInfo::UNKNOWN_EMPLOYEE_NUMBER) &&
      (!in_array($selectedEmployeeNumber, $employeeNumbers)))
   {
      $employeeNumbers[] = $selectedEmployeeNumber;
      sort($employeeNumbers);
   }
   
   foreach ($employeeNumbers as $employeeNumber)
   {
      $userInfo = UserInfo::load($employeeNumber);
      if ($userInfo)
      {
         $selected = ($employeeNumber == $selectedEmployeeNumber) ? "selected" : "";
         
         $name = $employeeNumber . " - " . $userInfo->getFullName();
         
         $options .= "<option value=\"$employeeNumber\" $selected>$name</option>";
      }
   }
   
   return ($options);
}

function getOperatorOptions()
{
   $options = "<option style=\"display:none\">";
   
   $operators = PPTPDatabase::getInstance()->getUsersByRole(Role::OPERATOR);
   
   // Create an array of employee numbers.
   $employeeNumbers = array();
   foreach ($operators as $operator)
   {
      $employeeNumbers[] = intval($operator["employeeNumber"]);
   }
   
   $selectedOperator = getOperator();
   
   // Add selected operator, if not already in the array.
   // Note: This handles the case of viewing an entry with an operator that is not assigned to the OPERATOR role.
   if (($selectedOperator != UserInfo::UNKNOWN_EMPLOYEE_NUMBER) &&
       (!in_array($selectedOperator, $employeeNumbers)))
   {
      $employeeNumbers[] = $selectedOperator;
      sort($employeeNumbers);
   }
     
   foreach ($employeeNumbers as $employeeNumber)
   {
      $userInfo = UserInfo::load($employeeNumber);
      if ($userInfo)
      {
         $selected = ($employeeNumber == $selectedOperator) ? "selected" : "";
         
         $name = $employeeNumber . " - " . $userInfo->getFullName();
         
         $options .= "<option value=\"$employeeNumber\" $selected>$name</option>";
      }
   }
   
   return ($options);
}

function getCategoryOptions()
{
   $options = "<option style=\"display:none\">";
   
   $selectedCategory = getCategory();
   
   for ($category = MaintenanceCategory::FIRST; $category < MaintenanceCategory::LAST; $category++)
   {
      $selected = ($category == $selectedCategory) ? "selected" : "";
      
      $label = MaintenanceCategory::getLabel($category);
      
      $options .= "<option value=\"$category\" $selected>$label</option>";
   }
   
   return ($options);
}

function getEmployeeNumber()
{
   $employeeNumber = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
   
   $maintenanceEntry = getMaintenanceEntry();
   
   if ($maintenanceEntry)
   {
      $employeeNumber = $maintenanceEntry->employeeNumber;
   }
   else
   {
      $employeeNumber = Authentication::getAuthenticatedUser()->employeeNumber;
   }
   
   return ($employeeNumber);
}

function getWcNumber()
{
   $wcNumber = JobInfo::UNKNOWN_WC_NUMBER;
   
   $maintenanceEntry = getMaintenanceEntry();
   
   if ($maintenanceEntry)
   {
      $wcNumber = $maintenanceEntry->wcNumber;
   }
   
   return ($wcNumber);
}

function getOperator()
{
   $operator = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
   
   $maintenanceEntry = getMaintenanceEntry();
   
   if ($maintenanceEntry)
   {
      $operator = $maintenanceEntry->operator;
   }
   
   return ($operator);
}

function getCategory()
{
   $category = MaintenanceCategory::UNKNOWN;
   
   $maintenanceEntry = getMaintenanceEntry();
   
   if ($maintenanceEntry)
   {
      $category = $maintenanceEntry->category;
   }
   
   return ($category);
}

function getComments()
{
   $comments = "";
   
   $maintenanceEntry = getMaintenanceEntry();
   
   if ($maintenanceEntry)
   {
      $comments = $maintenanceEntry->comments;
   }
   
   return ($comments);
}

function getMaintenanceTimeHours()
{
   $hours = 0;
   
   $maintenanceEntry = getMaintenanceEntry();
   
   if ($maintenanceEntry)
   {
      $hours = $maintenanceEntry->getMaintenanceTimeHours();
   }
   
   return ($hours);
}

function getMaintenanceTimeMinutes()
{
   $minutes = 0;
   
   $maintenanceEntry = getMaintenanceEntry();
   
   if ($maintenanceEntry)
   {
      $minutes = $maintenanceEntry->getMaintenanceTimeMinutes();
   }
   
   return ($minutes);
}

function getMaintenanceTime()
{
   $maintenanceTime = 0;
   
   $maintenanceEntry = getMaintenanceEntry();
   
   if ($maintenanceEntry)
   {
      $maintenanceTime = $maintenanceEntry->maintenanceTime;
   }
   
   return ($maintenanceTime);
}

// ********************************** BEGIN ************************************

Time::init();

session_start();

if (!Authentication::isAuthenticated())
{
   header('Location: ../home.php');
   exit;
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
   <link rel="stylesheet" type="text/css" href="../common/form.css"/>
   <link rel="stylesheet" type="text/css" href="../common/tooltip.css"/>
   <link rel="stylesheet" type="text/css" href="maintenanceLog.css"/>
   
   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
   <script src="maintenanceLog.js"></script>
   <script src="../common/common.js"></script>
   <script src="../common/validate.js"></script>

</head>

<body>

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="flex-horizontal main">
     
     <div class="flex-horizontal sidebar hide-on-tablet"></div> 
   
      <form id="input-form" action="" method="POST">
         <!-- Hidden inputs make sure disabled fields below get posted. -->
         <input id="maintenance-time-input" type="hidden" name="maintenanceTime" value="<?php echo getMaintenanceTime(); ?>">
         <input id="entry-id-input" type="hidden" name="entryId" value="<?php echo getEntryId(); ?>">
      </form>
      
      <div class="flex-vertical content">
      
         <div class="heading"><?php echo getHeading(); ?></div>
         
         <div class="description"><?php echo getDescription(); ?></div>
         
         <div class="pptp-form">
            <div class="form-row">
            <div class="form-col" style="margin-right: 20px;">  

               <div class="form-item">
                  <div class="form-label">Date</div>
                     <div class="flex-horizontal">
                        <input id="maintenance-date-input" class="form-input-medium" type="date" name="dateTime" form="input-form" oninput="" value="<?php echo getMaintenanceDate(); ?>" <?php echo !isEditable(MaintenanceLogInputField::DATE) ? "disabled" : ""; ?>>
                        &nbsp<button id="today-button" form="" onclick="onTodayButton()" <?php echo !isEditable(MaintenanceLogInputField::DATE) ? "disabled" : ""; ?>>Today</button>
                        &nbsp<button id="yesterday-button" form="" onclick="onYesterdayButton()" <?php echo !isEditable(MaintenanceLogInputField::DATE) ? "disabled" : ""; ?>>Yesterday</button>
                     </div>
               </div>

               <div class="form-item">
                  <div class="form-label">Employee</div>
                  <select id="employee-number-input" class="form-input-medium" name="employeeNumber" form="input-form" oninput="this.validator.validate();" <?php echo !isEditable(MaintenanceLogInputField::EMPLOYEE_NUMBER) ? "disabled" : ""; ?>>
                     <?php echo getEmployeeNumberOptions(); ?>
                  </select>
               </div>
               
               <div class="form-item">
                  <div class="form-label">Category</div>
                  <select id="category-input" class="form-input-medium" name="category" form="input-form" oninput="this.validator.validate(); onCategoryChange();" <?php echo !isEditable(MaintenanceLogInputField::CATEGORY) ? "disabled" : ""; ?>>
                     <?php echo getCategoryOptions(); ?>
                  </select>
               </div>
               
               <div class="form-item">
                  <div class="form-label">Work Center</div>
                  <select id="wc-number-input" class="form-input-medium" name="wcNumber" form="input-form" oninput="this.validator.validate();" <?php echo !isEditable(MaintenanceLogInputField::WC_NUMBER) ? "disabled" : ""; ?>>
                     <?php echo getWcNumberOptions(); ?>
                  </select>
               </div>
               
               <div class="form-item">
                  <div class="form-label">Operator</div>
                  <select id="operator-input" class="form-input-medium" name="operator" form="input-form" oninput="this.validator.validate();" <?php echo !isEditable(MaintenanceLogInputField::EMPLOYEE_NUMBER) ? "disabled" : ""; ?>>
                     <?php echo getOperatorOptions(); ?>
                  </select>
               </div>
               
               <div class="form-item">
                  <div class="form-label">Maintenance time</div>
                  <input id="maintenance-time-hour-input" type="number" class="form-input-medium" form="input-form" name="maintenanceTimeHours" style="width:50px;" oninput="/*this.validator.validate();*/ onMaintenanceTimeChange();" value="<?php echo getMaintenanceTimeHours(); ?>" <?php echo !isEditable(MaintenanceLogInputField::MAINTENANCE_TIME) ? "disabled" : ""; ?> />
                  <div style="padding: 5px;">:</div>
                  <input id="maintenance-time-minute-input" type="number" class="form-input-medium" form="input-form" name="maintenanceTimeMinutes" style="width:50px;" oninput="/*this.validator.validate();*/ onMaintenanceTimeChange();" value="<?php echo getMaintenanceTimeMinutes(); ?>" step="15" <?php echo !isEditable(MaintenanceLogInputField::MAINTENANCE_TIME) ? "disabled" : ""; ?> />
               </div>
               
               <div class="form-item">
                  <div class="form-label">Comments</div>
                  <textarea form="input-form" class="comments-input" type="text" form="input-form" name="comments" rows="4" maxlength="256" style="width:300px" <?php echo !isEditable(MaintenanceLogInputField::COMMENTS) ? "disabled" : ""; ?>><?php echo getComments(); ?></textarea>
               </div>

            </div>
                     
         </div>
         
         <?php echo getNavBar(); ?>
         
      </div>
      
      <script>
         preserveSession();

         /*
         var panTicketCodeValidator = new HexValidator("pan-ticket-code-input", 4, 1, 65536, true);
         var jobNumberValidator = new SelectValidator("job-number-input");
         var wcNumberValidator = new SelectValidator("wc-number-input");
         var operatorValidator = new SelectValidator("operator-input");
         var partWasherValidator = new SelectValidator("part-washer-input");
         var panCountValidator = new IntValidator("pan-count-input", 2, 1, 40, false);
         var partCountValidator = new IntValidator("part-count-input", 6, 1, 100000, false);

         panTicketCodeValidator.init();
         jobNumberValidator.init();
         wcNumberValidator.init();
         operatorValidator.init();
         partWasherValidator.init();
         panCountValidator.init();
         partCountValidator.init();
         */
      </script>
     
   </div>

</body>

</html>