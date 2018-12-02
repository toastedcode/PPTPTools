<?php

require_once '../common/authentication.php';
require_once '../common/database.php';
require_once '../common/header.php';
require_once '../common/keypad.php';
require_once '../common/lineInspectionInfo.php';

require 'viewLineInspections.php';

function getAction()
{
   $action = '';
   
   if (isset($_POST['action']))
   {
      $action = $_POST['action'];
   }
   else if (isset($_GET['action']))
   {
      $action = $_GET['action'];
   }
   
   return ($action);
}

function getView()
{
   $view = '';
   
   if (isset($_POST['view']))
   {
      $view = $_POST['view'];
   }
   else if (isset($_GET['view']))
   {
      $view = $_GET['view'];
   }
   
   return ($view);
}

function processAction($action)
{
   switch ($action)
   {
      case 'update_line_inspection_info':
      {
         updateLineInspectionInfo();
         break;
      }
      
      case 'cancel_line_inspection':
      {
         unset($_SESSION["lineInspectionInfo"]);
         break;
      }
      
      case 'new_line_inspection':
      {
         $_SESSION["lineInspectionInfo"] = new lineInspectionInfo();
         
         $_SESSION["lineInspectionInfo"]->dateTime = Time::now("Y-m-d h:i:s A");
         
         if ($user = Authentication::getAuthenticatedUser())
         {
            $_SESSION["lineInspectionInfo"]->inspector = $user->employeeNumber;
         }
         break;
      }
      
      case 'edit_line_inspection':
      {
         if (isset($_POST['entryId']))
         {
            $_SESSION["lineInspectionInfo"] = LineInspectionInfo::load($_POST['entryId']);
         }
         break;
      }
      
      case 'save_line_inspection':
      {
         updateLineInspectionInfo();

         updateLineInspection($_SESSION['lineInspectionInfo']);
         
         $_SESSION["lineInspectionInfo"] = new LineInspectionInfo();
         break;
      }
      
      case 'delete_line_inspection':
      {
         deleteLineInspection($_POST['entryId']);
         break;
      }
      
      default:
      {
         // Unhandled action.
      }
   }
}

function processView($view)
{
   switch ($view)
   {  
      case 'new_line_inspection':
      case 'view_line_inspection':
      case 'edit_line_inspection':
      {
         $page = new ViewLineInspection();
         $page->render($view);
         break;
      }
      
      case 'view_line_inspections':
      default:
      {
         $page = new ViewLineInspections();
         $page->render();
         break;
      }
   }
}

function updateLineInspectionInfo()
{
   if (isset($_POST['entryId']))
   {
      $_SESSION["lineInspectionInfo"]->entryId = $_POST['entryId'];
   }
   
   if (isset($_POST['dateTime']))
   {
      $dateTime = new DateTime($_POST['dateTime']);
      $_SESSION["lineInspectionInfo"]->date = $dateTime->format("Y-m-d h:i:s");
   }
   
   if (isset($_POST['inspector']))
   {
      $_SESSION["lineInspectionInfo"]->inspector = $_POST['inspector'];
   }
   
   if (isset($_POST['operator']))
   {
      $_SESSION["lineInspectionInfo"]->operator = $_POST['operator'];
   }
   
   if (isset($_POST['jobNumber']))
   {
      $_SESSION["lineInspectionInfo"]->jobNumber = $_POST['jobNumber'];
   }
   
   if (isset($_POST['wcNumber']))
   {
      $_SESSION["lineInspectionInfo"]->wcNumber= $_POST['wcNumber'];
   }
   
   for ($i = 0; $i < LineInspectionInfo::NUM_INSPECTIONS; $i++)
   {
      $name = LineInspectionInfo::getInspectionName($i);
      
      if (isset($_POST[$name]))
      {
         $_SESSION["lineInspectionInfo"]->inspections[$i] = $_POST[$name];
      }
   }
   
   if (isset($_POST['comments']))
   {
      $_SESSION["lineInspectionInfo"]->comments = $_POST['comments'];
   }
}

function deleteLineInspection($entryId)
{
   $result = false;
   
   $database = new PPTPDatabase();
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $result = $database->deleteLineInspection($entryId);
   }
   
   return ($result);
}

function updateLineInspection($lineInspectionInfo)
{
   $success = false;
   
   $database = new PPTPDatabase();
   
   $database->connect();
   
   if ($database->isConnected())
   {
      if ($lineInspectionInfo->entryId != 0)
      {
         $database->updateLineInspection($lineInspectionInfo);
      }
      else
      {
         $database->newLineInspection($lineInspectionInfo);
      }
      
      $success = true;
   }
   
   return ($success);
}

function redirectToLogin()
{
   $params = array();
   
   $action = getAction();
   if ($action != "")
   {
      $params["action"] = $action;
   }
  
   $view = getView();
   if ($view!= "")
   {
      $params["view"] = $view;
   }
   
   if (isset($_GET["entryId"]))
   {
      $params["entryId"] = $_GET["entryId"];
   }
   else if (isset($_POST["entryId"]))
   {
      $params["entryId"] = $_POST["entryId"];
   }
   
   $url = "./lineInspection/lineInspection.php" . "?" . http_build_query($params);
   
   $_SESSION["redirect"] = $url;
   header('Location: ../home.php');
   exit;
}
?>

<!-- ********************************** BEGIN ********************************************* -->

<?php 
Time::init();

session_start();

if (!Authentication::isAuthenticated())
{
   redirectToLogin();  // Note: exits.
}

processAction(getAction());
?>

<html>
<head>

   <meta name="viewport" content="width=device-width, initial-scale=1">
   
   <link rel="stylesheet" type="text/css" href="../common/flex.css"/>
   <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
   <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css"/>
   <link rel="stylesheet" type="text/css" href="../common/form.css"/>
   <link rel="stylesheet" type="text/css" href="../common/common.css"/>
   <link rel="stylesheet" type="text/css" href="../common/tooltip.css"/>
   <link rel="stylesheet" type="text/css" href="lineInspection.css"/>
   
   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
   <script src="lineInspection.js"></script>
   <script src="../validate.js"></script>

</head>

<body>

<?php Header::render("PPTP Tools"); ?>

<div class="flex-horizontal main">
   
   <div class="flex-horizontal sidebar hide-on-tablet"></div> 

   <?php processView(getView())?>

</div>

</body>
</html>