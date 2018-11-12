<?php

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
      //$creationDiv = ViewLineInspection::creationDiv($jobInfo);
      //$jobDiv = ViewLineInspection::jobDiv($jobInfo, $view);
      //$partDiv = ViewLineInspection::partDiv($jobInfo, $editable);
      
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
            <div class="form-row">
               TODO
            </div>
         </div>
         
         $navBar
               
      </div>
               
      <script>
      </script>
HEREDOC;
      
      return ($html);
   }
   
   public function render($view)
   {
      echo (ViewLineInspection::getHtml($view));
   }
   
   protected static function titleDiv()
   {
      $html =
<<<HEREDOC
      <div class="form-title">Line Inspection</div>
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
?>