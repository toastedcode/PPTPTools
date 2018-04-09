<?php

require_once '../common/authentication.php';
require_once '../common/header.php';
require_once 'viewMachineStatusPage.php';

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
      case 'view_machines':
      default:
         {
            MachineStatusPage::render();
            break;
         }
   }
}

?>

<!-- ********************************** BEGIN ********************************************* -->

<?php 
session_start();

if (!Authentication::isAuthenticated())
{
   header('Location: ../pptpTools.php');
   exit;
}

processAction(getAction());
?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="../flex.css"/>
<link rel="stylesheet" type="text/css" href="../pptpTools.css"/>
<link rel="stylesheet" type="text/css" href="machineStatus.css"/>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>

<script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
<script src="machineStatus.js"></script>
</head>

<body>

<?php Header::render("Machine Status"); ?>

<div class="flex-horizontal" style="height: 700px;">

   <?php processView(getView())?>

</div>

</body>
</html>