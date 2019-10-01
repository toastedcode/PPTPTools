<?php

require_once '../common/authentication.php';
require_once '../common/database.php';
require_once '../common/header.php';
require_once '../common/keypad.php';
require_once '../common/inspectionInfo.php';

require 'viewInspections.php';

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
      case 'update_inspection_info':
      {
         updateInspectionInfo();
         break;
      }
      
      case 'cancel_inspection':
      {
         unset($_SESSION["inspectionInfo"]);
         break;
      }
      
      case 'new_inspection':
      {
         $_SESSION["inspectionInfo"] = new inspectionInfo();
         
         $_SESSION["inspectionInfo"]->dateTime = Time::now("Y-m-d h:i:s A");
         
         if ($user = Authentication::getAuthenticatedUser())
         {
            $_SESSION["inspectionInfo"]->inspector = $user->employeeNumber;
         }
         break;
      }
      
      case 'edit_inspection':
      {
         if (isset($_POST['inspectionId']))
         {
            $_SESSION["inspectionInfo"] = InspectionInfo::load($_POST['inspectionId']);
         }
         break;
      }
      
      case 'save_inspection':
      {
         updateInspectionInfo();

         updateInspection($_SESSION['inspectionInfo']);
         
         $_SESSION["inspectionInfo"] = new InspectionInfo();
         break;
      }
      
      case 'delete_inspection':
      {
         deleteInspection($_POST['inspectionId']);
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
      case 'new_inspection':
      case 'view_inspection':
      case 'edit_inspection':
      {
         /*
         $page = new ViewInspection();
         $page->render($view);
         */
         break;
      }
      
      case 'view_inspections':
      default:
      {
         $page = new ViewInspections();
         $page->render();
         break;
      }
   }
}

function updateInspectionInfo()
{
   if (isset($_POST['inspectionId']))
   {
      $_SESSION["inspectionInfo"]->inspectionId = $_POST['inspectionId'];
   }
   
   if (isset($_POST['dateTime']))
   {
      $dateTime = new DateTime($_POST['dateTime']);
      $_SESSION["inspectionInfo"]->date = $dateTime->format("Y-m-d h:i:s");
   }
   
   if (isset($_POST['inspector']))
   {
      $_SESSION["inspectionInfo"]->inspector = $_POST['inspector'];
   }
   
   if (isset($_POST['operator']))
   {
      $_SESSION["inspectionInfo"]->operator = $_POST['operator'];
   }
   
   if (isset($_POST['jobNumber']))
   {
      $_SESSION["inspectionInfo"]->jobNumber = $_POST['jobNumber'];
   }
   
   if (isset($_POST['wcNumber']))
   {
      $_SESSION["inspectionInfo"]->wcNumber= $_POST['wcNumber'];
   }
   
   for ($i = 0; $i < InspectionInfo::NUM_INSPECTIONS; $i++)
   {
      $name = InspectionInfo::getInspectionName($i);
      
      if (isset($_POST[$name]))
      {
         $_SESSION["inspectionInfo"]->inspections[$i] = $_POST[$name];
      }
   }
   
   if (isset($_POST['comments']))
   {
      $_SESSION["inspectionInfo"]->comments = $_POST['comments'];
   }
}

function deleteInspection($inspectionId)
{
   $result = false;
   
   $database = new PPTPDatabase();
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $result = $database->deleteInspection($inspectionId);
   }
   
   return ($result);
}

function updateInspection($inspectionInfo)
{
   $success = false;
   
   $database = new PPTPDatabase();
   
   $database->connect();
   
   if ($database->isConnected())
   {
      if ($inspectionInfo->inspectionId != 0)
      {
         $database->updateInspection($inspectionInfo);
      }
      else
      {
         $database->newInspection($inspectionInfo);
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
   
   if (isset($_GET["inspectionId"]))
   {
      $params["inspectionId"] = $_GET["inspectionId"];
   }
   else if (isset($_POST["inspectionId"]))
   {
      $params["inspectionId"] = $_POST["inspectionId"];
   }
   
   $url = "./inspection/inspection.php" . "?" . http_build_query($params);
   
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

<!DOCTYPE html>
<html>

<head>

   <meta name="viewport" content="width=device-width, initial-scale=1">
   
   <link rel="stylesheet" type="text/css" href="../common/flex.css"/>
   <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
   <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css"/>
   <link rel="stylesheet" type="text/css" href="../common/form.css"/>
   <link rel="stylesheet" type="text/css" href="../common/common.css"/>
   <link rel="stylesheet" type="text/css" href="../common/tooltip.css"/>
   <link rel="stylesheet" type="text/css" href="inspection.css"/>
   
   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
   <script src="inspection.js"></script>
   <script src="../common/validate.js"></script>

</head>

<body>

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="flex-horizontal main">
      
      <div class="flex-horizontal sidebar hide-on-tablet"></div> 
   
      <?php processView(getView())?>
   
   </div>

</body>

</html>