<?php

require_once '../database.php';
require_once '../authentication.php';
require_once 'keypad.php';
require_once '../header.php';
require_once 'timeCardInfo.php';
require 'selectOperatorPage.php';
require 'selectWorkCenterPage.php';
require 'selectJobPage.php';
require 'enterTimePage.php';
require 'enterPartCountPage.php';
require 'enterCommentsPage.php';
require 'viewTimeCardPage.php';
require 'viewTimeCardsPage.php';

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
      case 'update_time_card_info':
      {
         updateTimeCardInfo();
         break;
      }
      
      case 'cancel_time_card':
      {
         unset($_SESSION["timeCardInfo"]);
         break;
      }
      
      case 'new_time_card':
      {
         $_SESSION["timeCardInfo"] = new TimeCardInfo();
         $_SESSION["timeCardInfo"]->date = Time::now("Y-m-d h:i:s A");
         break;
      }
      
      case 'edit_time_card':
      {
         if (isset($_POST['timeCardId']))
         {
            $_SESSION["timeCardInfo"] = getTimeCardInfo($_POST['timeCardId']);
         }
         break;
      }
      
      case 'save_time_card':
      {
         updateTimeCardInfo();
         
         updateTimeCard($_SESSION['timeCardInfo']);
         
         $_SESSION["timeCardInfo"] = new TimeCardInfo();
         break;
      }
      
      case 'delete_time_card':
      {
         deleteTimeCard($_POST['timeCardId']);
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
         
      case 'select_work_center':
      {
         SelectWorkCenter::render();
         break;
      }
         
      case 'select_job':
      {
         SelectJob::render();
         break;
      }
         
      case 'enter_time':
      {
         EnterTime::render();
         break;
      }
         
      case 'enter_part_count':
      {
         EnterPartCount::render();
         break;
      }
      
      case 'enter_comments':
      {
         CommentsPage::render();
         break;
      }
      
      case 'view_time_card':
      {
         ViewTimeCard::render($readOnly = true);
         break;
      }
      
      case 'edit_time_card':
      {
         ViewTimeCard::render($readOnly = false);
         break;
      }
      
      case 'view_time_cards':
      default:
      {
         ViewTimeCards::render();
         break;
      }
   }
}

function updateTimeCardInfo()
{
   if (isset($_POST['timeCardId']))
   {
      $_SESSION["timeCardInfo"]->timeCardId = $_POST['timeCardId'];
   }
   
   if (isset($_POST['date']))
   {
      $dateTime = new DateTime($_POST['date']);
      $_SESSION["timeCardInfo"]->date = $dateTime->format("Y-m-d h:i:s");
      echo "updateTimeCard: " . $_SESSION["timeCardInfo"]->date;
   }
   
   if (isset($_POST['employeeNumber']))
   {
      $_SESSION["timeCardInfo"]->employeeNumber = $_POST['employeeNumber'];
   }
   
   if (isset($_POST['jobNumber']))
   {
      $_SESSION["timeCardInfo"]->jobNumber = $_POST['jobNumber'];
   }
   
   if (isset($_POST['wcNumber']))
   {
      $_SESSION["timeCardInfo"]->wcNumber = $_POST['wcNumber'];
   }
   
   if (isset($_POST['setupTimeHour']))
   {
      $_SESSION["timeCardInfo"]->setupTimeHour = $_POST['setupTimeHour'];
   }
   
   if (isset($_POST['setupTimeMinute']))
   {
      $_SESSION["timeCardInfo"]->setupTimeMinute = $_POST['setupTimeMinute'];
   }
   
   if (isset($_POST['runTimeHour']))
   {
      $_SESSION["timeCardInfo"]->runTimeHour = $_POST['runTimeHour'];
   }
   
   if (isset($_POST['runTimeMinute']))
   {
      $_SESSION["timeCardInfo"]->runTimeMinute = $_POST['runTimeMinute'];
   }
   
   if (isset($_POST['panCount']))
   {
      $_SESSION["timeCardInfo"]->panCount = $_POST['panCount'];
   }
   
   if (isset($_POST['partsCount']))
   {
      $_SESSION["timeCardInfo"]->partsCount = $_POST['partsCount'];
   }
   
   if (isset($_POST['scrapCount']))
   {
      $_SESSION["timeCardInfo"]->scrapCount = $_POST['scrapCount'];
   }
   
   if (isset($_POST['comments']))
   {
      $_SESSION["timeCardInfo"]->comments = $_POST['comments'];
   }
}

function deleteTimeCard($timeCardId)
{
   $result = false;
   
   $database = new PPTPDatabase();
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $result = $database->deleteTimeCard($timeCardId);
   }
   
   return ($result);
}

function updateTimeCard($timeCardInfo)
{
   $success = false;
   
   $database = new PPTPDatabase();
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $setupTime = (($timeCardInfo->setupTimeHour * 60) + $timeCardInfo->setupTimeMinute);
      $runTime = (($timeCardInfo->runTimeHour * 60) + $timeCardInfo->runTimeMinute);
      
      $timeCard = new stdClass();
      
      $timeCard->date = $timeCardInfo->date;
      $timeCard->employeeNumber = $timeCardInfo->employeeNumber;
      $timeCard->jobNumber = $timeCardInfo->jobNumber;
      $timeCard->wcNumber = $timeCardInfo->wcNumber;
      $timeCard->setupTime = $setupTime;
      $timeCard->runTime = $runTime;
      $timeCard->panCount = $timeCardInfo->panCount;
      $timeCard->partsCount = $timeCardInfo->partsCount;
      $timeCard->scrapCount = $timeCardInfo->scrapCount;
      $timeCard->comments = $timeCardInfo->comments;
      
      if ($timeCardInfo->timeCardId != 0)
      {
         $database->updateTimeCard($timeCardInfo->timeCardId, $timeCard);
      }
      else
      {
         $database->newTimeCard($timeCard);
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
<link rel="stylesheet" type="text/css" href="timeCard.css"/>

<script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
<script src="timeCard.js"></script>
<script src="../validate.js"></script>
</head>

<body>

<?php Header::render("Time Cards"); ?>

<div class="flex-horizontal" style="height: 700px;">

   <?php processView(getView())?>

</div>

</body>
</html>