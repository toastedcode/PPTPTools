<?php

require_once '../common/authentication.php';
require_once '../common/database.php';
require_once '../common/header.php';
require_once '../common/keypad.php';
require_once '../common/partWeightEntry.php';

require 'selectWorkCenter.php';
require 'selectJob.php';
require 'enterMaterialNumber.php';
require 'enterTime.php';
require 'enterPartCount.php';
require 'enterComments.php';
require 'viewTimeCard.php';
require 'viewTimeCards.php';

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
         
         if ($user = Authentication::getAuthenticatedUser())
         {
            $_SESSION["timeCardInfo"]->employeeNumber = $user->employeeNumber;
         }
         break;
      }
      
      case 'edit_time_card':
      {
         if (isset($_POST['timeCardId']))
         {
            $_SESSION["timeCardInfo"] = TimeCardInfo::load($_POST['timeCardId']);
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
      
      case 'enter_material_number':
      {
         EnterMaterialNumber::render();
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
         $page = new ViewTimeCards();
         $page->render();
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
   
   if (isset($_POST['dateTime']))
   {
      $dateTime = new DateTime($_POST['dateTime']);
      $_SESSION["timeCardInfo"]->date = $dateTime->format("Y-m-d h:i:s");
   }
   
   if (isset($_POST['employeeNumber']))
   {
      $_SESSION["timeCardInfo"]->employeeNumber = $_POST['employeeNumber'];
   }
   
   if (isset($_POST['jobNumber']))
   {
      $_SESSION["timeCardInfo"]->jobNumber = $_POST['jobNumber'];
   }
   
   if (isset($_POST['materialNumber']))
   {
      $_SESSION["timeCardInfo"]->materialNumber = $_POST['materialNumber'];
   }
   
   if (isset($_POST['runTimeHours']) && isset($_POST['runTimeMinutes']))
   {
      $_SESSION["timeCardInfo"]->runTime = (($_POST['runTimeHours'] * 60) + $_POST['runTimeMinutes']);
   }
   
   if (isset($_POST['setupTimeHours']) && isset($_POST['setupTimeMinutes']))
   {
      $_SESSION["timeCardInfo"]->setupTime = (($_POST['setupTimeHours'] * 60) + $_POST['setupTimeMinutes']);
   }
   
   if (isset($_POST['panCount']))
   {
      $_SESSION["timeCardInfo"]->panCount = $_POST['panCount'];
   }
   
   if (isset($_POST['partCount']))
   {
      $_SESSION["timeCardInfo"]->partCount = $_POST['partCount'];
   }
   
   if (isset($_POST['scrapCount']))
   {
      $_SESSION["timeCardInfo"]->scrapCount = $_POST['scrapCount'];
   }
   
   if (isset($_POST['comments']))
   {
      $_SESSION["timeCardInfo"]->comments = $_POST['comments'];
   }
   
   if (isset($_POST['commentCodes']))
   {
      $commentCodes = CommentCode::getCommentCodes();
      
      foreach ($commentCodes as $commentCode)
      {
         $code = $commentCode->code;
         $name = "code-" . $code;
         
         if (isset($_POST[$name]))
         {
            $_SESSION["timeCardInfo"]->setCommentCode($code);
         }
         else
         {
            $_SESSION["timeCardInfo"]->clearCommentCode($code);
         }
      }
   }
     
   if (isset($_POST['approvedBy']))
   {
      $_SESSION["timeCardInfo"]->approvedBy = intval($_POST['approvedBy']);
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
      if ($timeCardInfo->timeCardId != 0)
      {
         $database->updateTimeCard($timeCardInfo);
      }
      else
      {
         $database->newTimeCard($timeCardInfo);
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
   
   if (isset($_GET["timeCardId"]))
   {
      $params["timeCardId"] = $_GET["timeCardId"];
   }
   else if (isset($_POST["timeCardId"]))
   {
      $params["timeCardId"] = $_POST["timeCardId"];
   }
   
   $url = "./timecard/timeCard.php" . "?" . http_build_query($params);
   
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
<link rel="stylesheet" type="text/css" href="../common/flex.css"/>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
<link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css"/>
<link rel="stylesheet" type="text/css" href="../common/common.css"/>
<link rel="stylesheet" type="text/css" href="../common/form.css"/>
<link rel="stylesheet" type="text/css" href="../common/tooltip.css"/>
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