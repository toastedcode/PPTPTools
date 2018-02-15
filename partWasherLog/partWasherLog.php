<?php

require_once '../database.php';
require_once '../authentication.php';
require_once '../header.php';
require_once 'partWasherEntry.php';
require_once 'keypad.php';

require 'viewPartWasherLogPage.php';
require 'selectOperatorPage.php';
require 'selectPanTicketPage.php';
require 'enterPartCountPage.php';

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
         $_SESSION["partWasherEntry"]->date = Time::now("Y-m-d h:i:s A");
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
         
         $_SESSION["partWasherEntry"] = new PanTicketInfo();
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
      case 'select_operator':
      {
         SelectOperator::render();
         break;
      }
      
      case 'select_pan_ticket':
      {
         SelectPanTicket::render();
         break;
      }
      
      case 'enter_part_count':
      {
         EnterPartCount::render();
         break;
      }
         
      case 'view_part_washer_log':
      default:
      {
         ViewPartWasherLog::render();
         break;
      }
   }
}

function updatePartWasherEntry()
{
   if (isset($_POST['dateTime']))
   {
      $dateTime = new DateTime($_POST['dateTime']);
      $_SESSION["panTicketInfo"]->dateTime = $dateTime->format("Y-m-d h:i:s");
   }
   
   if (isset($_POST['employeeNumber']))
   {
      $_SESSION["partWasherEntry"]->employeeNumber = $_POST['employeeNumber'];
   }
   
   if (isset($_POST['panTicketId']))
   {
      $_SESSION["partWasherEntry"]->panTicketId = $_POST['panTicketId'];
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
      $entry = new stdClass();
      
      $entry->dateTime = $partWasherEntry->dateTime;
      $entry->employeeNumber = $partWasherEntry->employeeNumber;
      $entry->panTicketId = $partWasherEntry->panTicketId;
      $entry->panCount = $partWasherEntry->panCount;
      $entry->partCount = $partWasherEntry->partCount;
      
      if ($partWasherEntry->partWasherEntryId != 0)
      {
         $database->updatePartWasherEntry($partWasherEntry->partWasherEntryId, $entry);
      }
      else
      {
         $database->newPartWasherEntry($entry);
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
<link rel="stylesheet" type="text/css" href="flex.css"/>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
<link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css"/>
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