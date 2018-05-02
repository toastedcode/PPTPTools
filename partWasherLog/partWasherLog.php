<?php

require_once '../common/authentication.php';
require_once '../common/database.php';
require_once '../common/header.php';
require_once '../common/partWasherEntry.php';

require 'viewPartWasherLog.php';
require 'selectTimeCard.php';
require 'enterPartCount.php';

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
      case 'new_part_washer_entry':
      {
         $_SESSION["partWasherEntry"] = new PartWasherEntry();
         $_SESSION["partWasherEntry"]->dateTime = Time::now("Y-m-d h:i:s A");
         
         if ($user = Authentication::getAuthenticatedUser())
         {
            $_SESSION["partWasherEntry"]->employeeNumber = $user->employeeNumber;
         }
         
         updatePartWasherEntry();
         break;
      }
         
      case 'update_part_washer_entry':
      {
         updatePartWasherEntry();
         break;   
      }
      
      case 'cancel_part_washer_entry':
      {
         unset($_SESSION["partWasherEntry"]);
         break;
      }
      
      case 'save_part_washer_entry':
      {
         updatePartWasherEntry();
         
         updatePartWasherLog($_SESSION['partWasherEntry']);
         
         $_SESSION["partWasherEntry"] = new PartWasherEntry();
         break;
      }
      
      case 'delete_part_washer_entry':
      {
         deletePartWasherEntry($_POST['partWasherEntryId']);
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
      case 'select_time_card':
      {
         $page = new SelectTimeCard();
         $page->render($view);
         break;
      }
      
      case 'enter_part_count':
      {
         $page = new EnterPartCount();
         $page->render();
         break;
      }
         
      case 'view_part_washer_log':
      default:
      {
         $page = new ViewPartWasherLog();
         $page->render();
         break;
      }
   }
}

function updatePartWasherEntry()
{
   if (isset($_POST['dateTime']))
   {
      $dateTime = new DateTime($_POST['dateTime']);
      $_SESSION["partWasherEntry"]->dateTime = $dateTime->format("Y-m-d h:i:s");
   }
   
   if (isset($_POST['employeeNumber']))
   {
      $_SESSION["partWasherEntry"]->employeeNumber = $_POST['employeeNumber'];
   }
   
   if (isset($_GET['timeCardId']))  // When called from viewTimeCard.php
   {
      $_SESSION["partWasherEntry"]->timeCardId= $_GET['timeCardId'];
   }
   else if (isset($_POST['timeCardId']))
   {
      $_SESSION["partWasherEntry"]->timeCardId= $_POST['timeCardId'];
   }
   
   if (isset($_POST['panCount']))
   {
      $_SESSION["partWasherEntry"]->panCount = $_POST['panCount'];
   }
   
   if (isset($_POST['partCount']))
   {
      $_SESSION["partWasherEntry"]->partCount= $_POST['partCount'];
   }
}

function deletePartWasherEntry($partWasherEntryId)
{
   $result = false;
   
   $database = new PPTPDatabase();
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $result = $database->deletePartWasherEntry($partWasherEntryId);
   }
   
   return ($result);
}

function updatePartWasherLog($partWasherEntry)
{
   $success = false;
   
   $database = new PPTPDatabase();
   
   $database->connect();
   
   if ($database->isConnected())
   {
      if ($partWasherEntry->partWasherEntryId != 0)
      {
         $database->updatePartWasherEntry($partWasherEntry->partWasherEntryId, $partWasherEntry);
      }
      else
      {
         $database->newPartWasherEntry($partWasherEntry);
      }
      
      $success = true;
   }
   
   return ($success);
}
?>

<!-- ********************************** BEGIN ********************************************* -->

<?php 
Time::init();

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
<link rel="stylesheet" type="text/css" href="../common/flex.css"/>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
<link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css"/>
<link rel="stylesheet" type="text/css" href="../common/common.css"/>
<link rel="stylesheet" type="text/css" href="partWasherLog.css"/>

<script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
<script src="partWasherLog.js"></script>
<script src="../validate.js"></script>
</head>

<body>

<?php Header::render("Part Washer Log"); ?>

<div class="flex-horizontal" style="height: 700px;">

   <?php processView(getView())?>

</div>

</body>
</html>