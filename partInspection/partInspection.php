<?php

require_once '../common/authentication.php';
require_once '../common/database.php';
require_once '../common/header.php';
require_once '../common/partInspectionInfo.php';

require 'viewPartInspections.php';

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
      case 'view_part_inspections':
      default:
      {
         $page = new ViewPartInspections();
         $page->render();
         break;
      }
   }
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
   
   $url = "./partInspection/partInspection.php" . "?" . http_build_query($params);
   
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
   <link rel="stylesheet" type="text/css" href="../common/common.css"/>
   <link rel="stylesheet" type="text/css" href="partInspection.css"/>
   
   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
   
</head>

<body>

<div class="flex-horizontal main">
   
   <div class="flex-horizontal sidebar hide-on-tablet"></div> 

   <?php processView(getView())?>

</div>

</body>
</html>