<?php

class ViewPartWasherEntry
{
   public function getHtml($view)
   {
      $html = "";
       
      $partWasherEntry = ViewPartWasherEntry::getPartWasherEntry();
      
      $newEntry = ($partWasherEntry->partWasherEntryId == PartWasherEntry::UNKNOWN_ENTRY_ID);
      
      $editable = (($view == "new_part_washer_entry") || ($view == "edit_part_washer_entry"));
      
      $headingDiv = ViewPartWasherEntry::headingDiv($view);
      
      $descriptionDiv = ViewPartWasherEntry::descriptionDiv($view);
      
      $entryDiv = ViewPartWasherEntry::entryDiv($partWasherEntry, $view);
      
      $navBar = ViewPartWasherEntry::navBar($view);
      
      $html =
<<<HEREDOC
      <form id="input-form" action="#" method="POST">
      </form>
      
      <div class="flex-vertical content">
      
         $headingDiv
         
         $descriptionDiv
         
         <div class="pptp-form">
            <div class="form-row">
               $entryDiv
            </div>
         </div>
         
         $navBar
         
      </div>
      
      <script>
         updateWCNumberInput();
      </script>
HEREDOC;
      
      return ($html);
   }
   
   public function render($view)
   {
      echo (ViewPartWasherEntry::getHtml($view));
   }
   
   public static function getJobNumberInput($partWasherEntry, $isDisabled)
   {
      $disabled = $isDisabled ? "disabled" : "";
      
      $jobInfo = null;
      if ($partWasherEntry->jobId != JobInfo::UNKNOWN_JOB_ID)
      {
         $jobInfo = JobInfo::load($partWasherEntry->jobId);
      }
      
      $selected =  ($partWasherEntry->jobId == JobInfo::UNKNOWN_JOB_ID) ? "selected" : "";
      
      $options = "<option disabled $selected hidden>Select Work Center</option>";
      
      $jobNumbers = JobInfo::getJobNumbers(true);
      
      foreach ($jobNumbers as $jobNumber)
      {
         $selected =  ($jobInfo && ($jobInfo->jobNumber == $jobNumber)) ? "selected" : "";
         
         $options .= "<option value=\"$jobNumber\" $selected>" . $jobNumber . "</option>";
      }
      
      $html =
<<<HEREDOC
      <select id="job-number-input" class="form-input-medium" name="jobNumber" form="input-form" $disabled>
         $options
      </select>
HEREDOC;
      
      return ($html);
   }
   
   public static function getWcNumberInput($partWasherEntry, $isDisabled)
   {
      $disabled = $isDisabled ? "disabled" : "";
      
      $jobInfo = null;
      if ($partWasherEntry->jobId != JobInfo::UNKNOWN_JOB_ID)
      {
         $jobInfo = JobInfo::load($partWasherEntry->jobId);
      }
      
      $selected = ($partWasherEntry->jobId != JobInfo::UNKNOWN_JOB_ID) ? "selected" : "";
      
      $options = "<option disabled $selected hidden>Select Work Center</option>";
      
      /*
      if ($jobInfo)
      {
         $database = new PPTPDatabase();
         
         $database->connect();
         
         if ($database->isConnected())
         {
            $result = $database->getJobsByJobNumber($jobInfo->jobNumber);
            
            $i = 0;
            while ($result && ($row = $result->fetch_assoc()))
            {
               $wcNumber = intval($row["wcNumber"]);
               
               $selected = "";
               if ((($partWasherEntry->wcNumber == 0) && ($i == 0)) ||  // no selection && first in list
                   ($wcNumber == $partWasherEntry->wcNumber))           // selected work center number
               {
                  $selected = "selected";
               }
               
               $options .= "<option value=\"$wcNumber\" $selected>" . $wcNumber. "</option>";
               
               $i++;
            }
         }
      }
      */
      
      $html =
<<<HEREDOC
      <select id="wc-number-input" class="form-input-medium" name="wcNumber" form="input-form" $disabled>
         $options
      </select>
HEREDOC;
      
      return ($html);
   }
   
   protected static function titleDiv()
   {
      $html =
<<<HEREDOC
      <div class="form-title">Part Washer Log Entry</div>
HEREDOC;
      
      return ($html);
   }
   
   protected static function headingDiv($view)
   {
      $heading = "";
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
      
      $html =
<<<HEREDOC
      <div class="heading">$heading</div>
HEREDOC;
      
      return ($html);
   }
   
   protected static function descriptionDiv($view)
   {
      $description = "";
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
      
      $html =
<<<HEREDOC
      <div class="description">$description</div>
HEREDOC;
      
      return ($html);
   }
   
   protected static function entryDiv($partWasherEntry, $view)
   {
      $isDisabled = ($view == "view_part_washer_entry");
      $disabled = $isDisabled? "disabled" : "";
      
      $jobNumberInput = ViewPartWasherEntry::getJobNumberInput($partWasherEntry, $isDisabled);
      
      $wcNumberInput = ViewPartWasherEntry::getWcNumberInput($partWasherEntry, $isDisabled);
      
      $operatorInput = ViewPartWasherEntry::getOperatorInput($partWasherEntry, $isDisabled);
      
      $html =
<<<HEREDOC
      <div class="form-col">
      
         <div class="form-item">
            <div class="form-label">Job Number</div>
            $jobNumberInput
         </div>
         
         <div class="form-item">
            <div class="form-label">WC Number</div>
            <div id="wc-number-input-div">$wcNumberInput</div>
         </div>
         
         <div class="form-item">
            <div class="form-label">Operator</div>
            $operatorInput
         </div>

         <div class="form-item">
            <div class="form-label">Pan Count</div>
            <input id="panCount-input" form="input-form" class="mdl-textfield__input keypadInputCapable large-text-input" type="number" name="panCount" oninput="this.validator.validate(); validatePanCountMatch();" value="$partWasherEntry->panCount">
            <label class="mdl-textfield__label" for="panCount-input">Pan count</label>
         </div>

         <div class="form-item">
            <div class="form-label">Part Count</div>
            <input id="partCount-input" form="input-form" class="mdl-textfield__input keypadInputCapable large-text-input" type="number" name="panCount" oninput="this.validator.validate(); validatePanCountMatch();" value="$partWasherEntry->partCount">
            <label class="mdl-textfield__label" for="partCount-input">Part count</label>
         </div>
         
      </div>
HEREDOC;
                  
      return ($html);
   }
   
   protected static function navBar($view)
   {
      $navBar = new Navigation();
      
      $navBar->start();
      
      if (($view == "new_part_washer_entry") ||
          ($view == "new_part_washer_entry"))
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
         // Viewing an existing job.
         
         $navBar->highlightNavButton("Ok", "submitForm('input-form', 'partWasherLog.php', 'view_part_washer_log', 'no_action')", false);
      }
      
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   protected static function getPartWasherEntry()
   {
      $partWasherEntry = new PartWasherEntry();
      
      if (isset($_GET['entryId']))
      {
         $partWasherEntry = PartWasherEntry::load($_GET['entryId']);
      }
      else if (isset($_POST['entryId']))
      {
         $partWasherEntry = PartWasherEntry::load($_POST['entryId']);
      }
      else if (isset($_SESSION['partWasherEntry']))
      {
         $partWasherEntry = $_SESSION['partWasherEntry'];
      }
      
      return ($partWasherEntry);
   }
   
   public static function getOperatorInput($partWasherEntry, $isDisabled)
   {
      $disabled = $isDisabled ? "disabled" : "";
      
      $selected = ($partWasherEntry->operator == UserInfo::UNKNOWN_EMPLOYEE_NUMBER) ? "selected" : "";
      
      $options = "<option disabled $selected hidden>Select operator</option>";
      
      $operators = UserInfo::getUsersByRole(Role::OPERATOR);
      
      $i = 0;
      foreach ($operators as $operator)
      {
         $selected = "";
         if ($operator->employeeNumber == $partWasherEntry->operator)
         {
            $selected = "selected";
         }
         
         $options .= "<option value=\"$operator->employeeNumber\"  $selected>" . $operator->getFullName() . "</option>";
         
         $i++;
      }
      
      $html =
<<<HEREDOC
      <select id="operator-input" class="form-input-medium" name="operator" form="input-form" $disabled>
         $options
      </select>
HEREDOC;
      
      return ($html);
   }
   
   protected static function getWorkcenters()
   {
      $workcenters = array();
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getWorkCenters();
         
         if ($result)
         {
            while ($row = $result->fetch_assoc())
            {
               $workcenters[] = $row["wcNumber"];
            }
         }
      }
      
      return ($workcenters);
   }
}

// *****************************************************************************
//                          AJAX request handling
// *****************************************************************************

if (isset($_GET["action"]) &&
    ($_GET["action"] == "get_wc_number_input"))
{
   $jobNumber = $_GET["jobNumber"];
   $isDisabled = filter_var($_GET["isDisabled"], FILTER_VALIDATE_BOOLEAN);
   
   $wcNumberInput =  ViewPartWasherEntry::getWcNumberInput($jobNumber, $isDisabled);
   
   echo $wcNumberInput;
}
?>