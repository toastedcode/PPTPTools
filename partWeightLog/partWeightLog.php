<?php

require_once '../common/authentication.php';
require_once '../common/database.php';
require_once '../common/header.php';
require_once '../common/partWeightEntry.php';

require 'viewPartWeightLog.php';
require 'selectEntryMethod.php';
require 'selectWorkCenter.php';
require 'selectJob.php';
require 'selectOperator.php';
require 'enterPanCount.php';
require 'selectTimeCard.php';
require 'enterWeight.php';

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
      case 'new_part_weight_entry':
      {
         $_SESSION["partWeightEntry"] = new PartWeightEntry();
         $_SESSION["partWeightEntry"]->dateTime = Time::now("Y-m-d h:i:s A");
         
         if ($user = Authentication::getAuthenticatedUser())
         {
            $_SESSION["partWeightEntry"]->employeeNumber = $user->employeeNumber;
         }
         
         updatePartWeightEntry();
         break;
      }
         
      case 'update_part_weight_entry':
      {
         updatePartWeightEntry();
         break;   
      }
      
      case 'cancel_part_weight_entry':
      {
         unset($_SESSION["partWeightEntry"]);
         unset($_SESSION["wcNumber"]);
         break;
      }
      
      case 'save_part_weight_entry':
      {
         updatePartWeightEntry();
         
         updatePartWeightLog($_SESSION['partWeightEntry']);
         
         $_SESSION["partWeightEntry"] = new PartWeightEntry();
         break;
      }
      
      case 'delete_part_weight_entry':
      {
         deletePartWeightEntry($_POST['partWeightEntryId']);
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
      case 'select_entry_method':
      {
         unset($_SESSION["wcNumber"]);
         
         $page = new SelectEntryMethod();
         $page->render($view);
         break;
      }
         
      case 'select_time_card':
      {
         $page = new SelectTimeCard_PartWeight();
         $page->render($view);
         break;
      }
      
      case 'select_job':
      {
         $page = new SelectJob_PartWeight();
         $page->render();
         break;
      }
         
      case 'select_operator':
      {
         $page = new SelectOperator_PartWeight();
         $page->render();
         break;
      }
      
      case 'enter_pan_count':
      {
         $page = new EnterPanCount();
         $page->render();
         break;
      }
         
      case 'select_work_center':
      {
         $page = new SelectWorkCenter_PartWeight();
         $page->render();
         break;
      }
      
      case 'enter_weight':
      {
         $page = new EnterWeight();
         $page->render();
         break;
      }
         
      case 'view_part_weight_log':
      default:
      {
         $page = new ViewPartWeightLog();
         $page->render();
         break;
      }
   }
}

function updatePartWeightEntry()
{
   if (isset($_POST['dateTime']))
   {
      $dateTime = new DateTime($_POST['dateTime']);
      $_SESSION["partWeightEntry"]->dateTime = $dateTime->format("Y-m-d h:i:s");
   }
   
   if (isset($_POST['employeeNumber']))
   {
      $_SESSION["partWeightEntry"]->employeeNumber = $_POST['employeeNumber'];
   }

   if (isset($_GET['timeCardId']))  // When called from viewTimeCard.php
   {
      $_SESSION["partWeightEntry"]->timeCardId = $_GET['timeCardId'];
   }
   else if (isset($_POST['timeCardId']))
   {
      $_SESSION["partWeightEntry"]->timeCardId = $_POST['timeCardId'];
   }
   
   if (isset($_POST['weight']))
   {
      $_SESSION["partWeightEntry"]->weight = $_POST['weight'];
   }
   
   // Temporary input variable, part of selecting job.
   if (isset($_POST['wcNumber']))
   {
      $_SESSION["wcNumber"] = $_POST['wcNumber'];
   }
   
   if (isset($_POST['jobId']))
   {
      $_SESSION["partWeightEntry"]->jobId = $_POST['jobId'];
   }
   
   if (isset($_POST['operator']))
   {
      $_SESSION["partWeightEntry"]->operator = $_POST['operator'];
   }
   
   if (isset($_POST['panCount']))
   {
      $_SESSION["partWeightEntry"]->panCount= $_POST['panCount'];
   }
}

function deletePartWeightEntry($partWeightEntryId)
{
   $result = false;
   
   $database = new PPTPDatabase();
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $result = $database->deletePartWeightEntry($partWeightEntryId);
   }
   
   return ($result);
}

function updatePartWeightLog($partWeightEntry)
{
   $success = false;
   
   $database = new PPTPDatabase();
   
   $database->connect();
   
   if ($database->isConnected())
   {
      if ($partWeightEntry->partWeightEntryId != 0)
      {
         $database->updatePartWeightEntry($partWeightEntry->partWeightEntryId, $partWeightEntry);
      }
      else
      {
         // Delete any existing part weight.
         // TODO: Any reason to preserve old entries?
         if ($partWeightEntry->timeCardId != PartWeightEntry::UNKNOWN_TIME_CARD_ID)
         {
            $database->deleteAllPartWeightEntries($partWeightEntry->timeCardId);
         }
         
         $database->newPartWeightEntry($partWeightEntry);
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

   <meta name="viewport" content="width=device-width, initial-scale=1">
   
   <link rel="stylesheet" type="text/css" href="../common/flex.css"/>
   <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
   <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css"/>
   <link rel="stylesheet" type="text/css" href="../common/common.css"/>
   <link rel="stylesheet" type="text/css" href="partWeightLog.css"/>
   
   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
   <script src="partWeightLog.js"></script>
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