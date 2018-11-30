<?php

require_once '../common/jobInfo.php';
require_once '../common/lineInspectionInfo.php';
require_once '../common/navigation.php';
require_once '../common/userInfo.php';

class ViewLineInspection
{
   public function getHtml($view)
   {
      $html = "";
      
      $lineInspectionInfo = ViewLineInspection::getLineInspectionInfo();
      
      $newInspection = ($lineInspectionInfo->entryId == LineInspectionInfo::INVALID_ENTRY_ID);
      
      $editable = (($view == "new_line_inspection") || ($view == "edit_line_inspection"));
      
      $headingDiv = ViewLineInspection::headingDiv($view);
      
      $descriptionDiv = ViewLineInspection::descriptionDiv($view);
      
      $inspectionDiv = ViewLineInspection::inspectionDiv($lineInspectionInfo, $view);
      
      $navBar = ViewLineInspection::navBar($view);
      
      $html =
<<<HEREDOC
      <form id="input-form" action="#" method="POST">
      </form>

      <div class="flex-horizontal" style="align-items:stretch; justify-content: flex-start; height:100%">
         
         <div class="flex-horizontal sidebar hide-on-tablet"></div> 

         <div class="flex-vertical content">

            $headingDiv

            $descriptionDiv
         
            <div class="pptp-form">
               <div class="form-row">
                  $inspectionDiv
               </div>
            </div>
         
            $navBar
            
         </div>
               
      </div>
               
      <script>
         updateWCNumberInput();
      </script>
HEREDOC;
      
      return ($html);
   }
   
   public function render($view)
   {
      echo (ViewLineInspection::getHtml($view));
   }
   
   public static function getWcNumberInput($jobNumber, $isDisabled)
   {
      $disabled = $isDisabled ? "disabled" : "";
      
      $lineInspectionInfo = ViewLineInspection::getLineInspectionInfo();
      
      $selected =  ($lineInspectionInfo->wcNumber == 0) ? "selected" : ""; 
      
      $options = "<option disabled $selected hidden>Select Work Center</option>";
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getJobsByJobNumber($jobNumber);
         
         $i = 0;
         while ($result && ($row = $result->fetch_assoc()))
         {
            $wcNumber = intval($row["wcNumber"]);
            
            $selected = "";
            if ((($lineInspectionInfo->wcNumber == 0) && ($i == 0)) ||  // no selection && first in list
                ($wcNumber == $lineInspectionInfo->wcNumber))           // selected work center number
            {
               $selected = "selected";
            }
            
            $options .= "<option value=\"$wcNumber\" $selected>" . $wcNumber. "</option>";
            
            $i++;
         }
      }
      
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
      <div class="form-title">Line Inspection</div>
HEREDOC;
      
      return ($html);
   }
   
   protected static function headingDiv($view)
   {
      $heading = "";
      if ($view == "new_line_inspection")
      {
         $heading = "Add a New Inspection";
      }
      else if ($view == "edit_line_inspection")
      {
         $heading = "Update an Inspection";
      }
      else if ($view == "view_line_inspection")
      {
         $heading = "View an Inspection";
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
      if ($view == "new_line_inspection")
      {
         $description = "Start by selecting a work center, then any of the currently active jobs for that station.  If any of the categories are not relevant to the part you're inspecting, just leave it set to \"N/A\"";
      }
      else if ($view == "edit_line_inspection")
      {
         $description = "You may revise any of the fields for this inspection and then select save when you're satisfied with the changes.";
      }
      else if ($view == "view_line_inspection")
      {
         $description = "View a previously saved inspection in detail.";
      }
      
      $html =
<<<HEREDOC
      <div class="description">$description</div>
HEREDOC;
      
      return ($html);
   }
   
   protected static function inspectionDiv($lineInspectionInfo, $view)
   {
      $isDisabled = ($view == "view_line_inspection");
      $disabled = $isDisabled? "disabled" : "";
      
      $selected= ($lineInspectionInfo->jobNumber == JobInfo::UNKNOWN_JOB_NUMBER) ? "selected" : "";
      
      $options = "<option disabled $selected hidden>Select job</option>";
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getActiveJobs(null);
         
         $i = 0;
         while ($result && ($row = $result->fetch_assoc()))
         {
            $jobNumber = $row["jobNumber"];
            
            $selected = "";
            if ($jobNumber == $lineInspectionInfo->jobNumber)
            {
               $selected = "selected";
            }
            
            $options .= "<option value=\"$jobNumber\" $selected>" . $jobNumber . "</option>";
            
            $i++;
         }
      }
      
      $wcNumberInput = ViewLineInspection::getWcNumberInput("", $lineInspectionInfo->wcNumber, $isDisabled);
      
      $operatorInput = ViewLineInspection::getOperatorInput($lineInspectionInfo, $isDisabled);
      
      $inspectionInput = array();
      $fail = array();
      for ($i = 0; $i < LineInspectionInfo::NUM_INSPECTIONS; $i++)
      {
         $inspectionInput[$i] = ViewLineInspection::inspectionInput($lineInspectionInfo, $i, $isDisabled);
      }
      
      $html =
<<<HEREDOC
      <div class="form-col">

         <div class="form-item">
            <div class="form-label">Job Number</div>
            <select id="job-number-input" class="form-input-medium" name="jobNumber" form="input-form" oninput="updateWCNumberInput();" $disabled>
               $options
            </select>
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
            <div class="form-label hide-on-mobile">Inspection</div>
            <table class="inspection-table">
               <tr>
                  <td>Thread #1</td>
                  {$inspectionInput[0]}
               </tr>
               <tr>
                  <td>Thread #2</td>
                  {$inspectionInput[1]}
               </tr>
               <tr>
                  <td>Thread #3</td>
                  {$inspectionInput[2]}
               </tr>
               <tr>
                  <td>Visual</td>
                  {$inspectionInput[3]}
               </tr>
            </table>
         </div>
   
         <div class="form-item">
            <div class="form-label hide-on-mobile">Comments</div>
            <textarea form="input-form" class="comments-input" type="text" name="comments" placeholder="Enter comments ...">$lineInspectionInfo->comments</textarea>
         </div>

      </div>
HEREDOC;
      
      return ($html);
   }
   
   protected static function navBar($view)
   {
      $navBar = new Navigation();
      
      $navBar->start();
      
      if (($view == "new_line_inspection") ||
          ($view == "edit_line_inspection"))
      {
         // Case 1
         // Creating a new inspection.
         // Editing an existing inspection.
         
         $navBar->cancelButton("submitForm('input-form', 'lineInspection.php', 'view_line_inspections', 'cancel_line_inspection')");
         $navBar->highlightNavButton("Save", "submitForm('input-form', 'lineInspection.php', 'view_line_inspections', 'save_line_inspection');", false);
      }
      else if ($view == "view_line_inspection")
      {
         // Case 2
         // Viewing an existing job.
         
         $navBar->highlightNavButton("Ok", "submitForm('input-form', 'jobs.php', 'view_line_inspections', 'no_action')", false);
      }
      
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   protected static function getLineInspectionInfo()
   {
      $lineInspectionInfo = new LineInspectionInfo();
      
      if (isset($_GET['entryId']))
      {
         $lineInspectionInfo= LineInspectionInfo::load($_GET['entryId']);
      }
      else if (isset($_POST['entryId']))
      {
         $lineInspectionInfo= LineInspectionInfo::load($_POST['entryId']);
      }
      else if (isset($_SESSION['lineInspectionInfo']))
      {
         $lineInspectionInfo= $_SESSION['lineInspectionInfo'];
      }
      
      return ($lineInspectionInfo);
   }
   
   public static function getOperatorInput($lineInspectionInfo, $isDisabled)
   {
      $disabled = $isDisabled ? "disabled" : "";
      
      $selected = ($lineInspectionInfo->wcNumber == 0) ? "selected" : ""; 
      
      $options = "<option disabled $selected hidden>Select operator</option>";
      
      $operators = UserInfo::getUsersByRole(Role::OPERATOR);
      
      $i = 0;
      foreach ($operators as $operator)
      {
         $selected = "";
         if ($operator->employeeNumber == $lineInspectionInfo->operator)
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
   
   public static function inspectionInput($lineInspectionInfo, $inspectionIndex, $isDisabled)
   {
      $name = LineInspectionInfo::getInspectionName($inspectionIndex);
      $pass = ($lineInspectionInfo->inspections[$inspectionIndex] == InspectionStatus::PASS) ? "checked" : "";
      $fail = ($lineInspectionInfo->inspections[$inspectionIndex] == InspectionStatus::FAIL) ? "checked" : "";
      $nonApplicable = ($lineInspectionInfo->inspections[$inspectionIndex] == InspectionStatus::UNKNOWN) ? "checked" : "";
      
      $passId = $name . "-pass-button";
      $failId = $name . "-fail-button";
      $nonApplicableId = $name . "-na-button";
      
      $html = 
<<<HEREDOC
      <td>
         <input id="$passId" type="radio" class="invisible-radio-button pass" form="input-form" name="$name" value="1" $pass/>
         <label for="$passId">
            <div class="select-button">PASS</div>
         </label>
      </td>
      <td>
         <input id="$failId" type="radio" class="invisible-radio-button fail" form="input-form" name="$name" value="2" $fail/>
         <label for="$failId">
            <div class="select-button">FAIL</div>
         </label>
      </td>
      <td>
         <input id="$nonApplicableId" type="radio" class="invisible-radio-button nonApplicable" form="input-form" name="$name" value="0" $nonApplicable/>
         <label for="$nonApplicableId">
            <div class="select-button">N/A</div>
         </label>
      </td>
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
   
   $wcNumberInput =  ViewLineInspection::getWcNumberInput($jobNumber, $isDisabled);
   
   echo $wcNumberInput;
}
?>