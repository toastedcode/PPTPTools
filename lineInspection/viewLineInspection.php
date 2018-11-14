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
      
      $titleDiv = ViewLineInspection::titleDiv();
      $inspectionDiv = ViewLineInspection::inspectionDiv($lineInspectionInfo, $view);
      
      $navBar = ViewLineInspection::navBar($view);
      
      $title = "";
      if ($view == "new_line_inspection")
      {
         $title = "New Inspection";
      }
      else if ($view == "edit_line_inspection")
      {
         $title = "Edit Inspection";
      }
      else if ($view == "view_line_inspection")
      {
         $title = "View Inspection";
      }
      
      $html =
<<<HEREDOC
      <form id="input-form" action="#" method="POST">
      </form>

      <div class="flex-vertical card-div">
         <div class="card-header-div">$title</div>
         
         <div class="pptp-form" style="height:500px;">
            $titleDiv
            <div class="form-column">
               $inspectionDiv
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
      echo (ViewLineInspection::getHtml($view));
   }
   
   public static function getWcNumberInput($jobNumber, $isDisabled)
   {
      $disabled = $isDisabled ? "disabled" : "";
      
      $options = "";
      $selected = "";
      
      $lineInspectionInfo = ViewLineInspection::getLineInspectionInfo();
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getJob($jobNumber);
         
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
      <select id="wc-number-input" name="wcNumber" form="input-form" $disabled>
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
   
   protected static function inspectionDiv($lineInspectionInfo, $view)
   {
      $isDisabled = ($view == "view_line_inspection");
      $disabled = $isDisabled? "disabled" : "";
      
      $options = "";
      
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
            if ((($lineInspectionInfo->jobNumber == "") && ($i == 0)) ||  // no selection && first in list
                ($jobNumber == $lineInspectionInfo->jobNumber))           // selected job number
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
      <!--div class="form-title">Line Inspection</div-->
      <div class="form-item">
         <div class="form-label-long">Job Number</div>
         <select id="job-number-input" name="jobNumber" form="input-form" oninput="updateWCNumberInput();" $disabled>
            $options
         </select>
      </div>
      <div class="form-item">
         <div class="form-label-long">WC Number</div>
         <div id="wc-number-input-div">$wcNumberInput</div>
      </div>
      <div class="form-item">
         <div class="form-label-long">Operator</div>
         $operatorInput
      </div>
      <div class="form-item">
         <div class="form-label-long">Thread #1</div>
         {$inspectionInput[0]}
      </div>
      <div class="form-item">
         <div class="form-label-long">Thread #2</div>
         {$inspectionInput[1]}
      </div>
      <div class="form-item">
         <div class="form-label-long">Thread #3</div>
         {$inspectionInput[2]}
      </div>
      <div class="form-item">
         <div class="form-label-long">Visual #3</div>
         {$inspectionInput[3]}
      </div>
      <div class="form-item">
         <div class="form-label-long">Operator</div>
         <textarea form="input-form" class="comments-input" type="text" name="comments" rows="10" maxlength="256" placeholder="Enter comments ...">$lineInspectionInfo->comments</textarea>
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
      
      $options = "";
      
      $operators = UserInfo::getUsersByRole(Role::OPERATOR);
      
      $i = 0;
      foreach ($operators as $operator)
      {
         $selected = "";
         if ((($lineInspectionInfo->operator == UserInfo::UNKNOWN_EMPLOYEE_NUMBER) && ($i == 0)) ||  // no selection && first in list
             ($operator->employeeNumber == $lineInspectionInfo->operator))                            // selected operator
         {
            $selected = "selected";
         }
         
         $options .= "<option value=\"$operator->employeeNumber\"  $selected>" . $operator->getFullName() . "</option>";
         
         $i++;
      }
      
      $html =
<<<HEREDOC
      <select id="operator-input" name="operator" form="input-form" $disabled>
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
      
      $html = 
<<<HEREDOC
      <input type="radio" form="input-form" name="$name" value="1" $pass/>
      <input type="radio" form="input-form" name="$name" value="2" $fail/>
      <input type="radio" form="input-form" name="$name" value="0" $nonApplicable/>
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