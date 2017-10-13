<!DOCTYPE html>
<html>

<head>
   <!--  Material Design Lite -->
   <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
   <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css" />
   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
</head>

<body>

   <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
      <header class="mdl-layout__header">
         <div class="mdl-layout__header-row">
            <!-- Title -->
            <span class="mdl-layout-title">Time Cards</span>
            <!-- Add spacer, to align navigation to the right -->
            <div class="mdl-layout-spacer"></div>
            <!-- Navigation. We hide it in small screens. -->
            <nav class="mdl-navigation">
               <a class="mdl-navigation__link" href="../pptpTools.php?action=logout">Logout</a>
            </nav>
         </div>
      </header>

      <main class="mdl-layout__content">
         <div class="page-content">

<?php

require_once '../database.php';
require_once 'keypad.php';
require 'selectActionPage.php';
require 'selectOperatorPage.php';
require 'selectWorkCenterPage.php';
require 'selectJobPage.php';
require 'enterTimePage.php';
require 'enterPartCountPage.php';
//require 'newTimeCardPage.php';
//require 'updateTimeCardPage.php';
require 'viewTimeCardPage.php';
require 'viewTimeCardsPage.php';
//require 'editTimeCardPage.php';

class TimeCardInfo
{
    public $timeCardId;
    public $date;
    public $employeeNumber;
    public $jobNumber;
    public $wcNumber;
    public $setupTimeHour = 0;
    public $setupTimeMinute = 0;
    public $runTimeHour = 0;
    public $runTimeMinute = 0;
    public $panCount;
    public $partsCount;
    public $scrapCount;
    public $comments;
}

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
         $_SESSION["timeCardInfo"]->date = date('Y-m-d');
         break;
      }
      
      case 'add_time_card':
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
         selectOperatorPage($_SESSION['timeCardInfo']);
         break;
      }
         
      case 'select_work_center':
      {
         selectWorkCenterPage($_SESSION['timeCardInfo']);
         break;
      }
         
      case 'select_job':
      {
         selectJobPage($_SESSION['timeCardInfo']);
         break;
      }
         
      case 'enter_time':
      {
         enterTimePage($_SESSION['timeCardInfo']);
         break;
      }
         
      case 'enter_part_count':
      {
         enterPartCountPage($_SESSION['timeCardInfo']);
         break;
      }
      
      case 'view_time_card':
      {
         $timeCardInfo = new TimeCardInfo();
         
         if (isset($_POST['timeCardId']))
         {
            $timeCardInfo = getTimeCardInfo($_POST['timeCardId']);
         }
         else
         {
            $timeCardInfo = $_SESSION['timeCardInfo'];
         }
         
         viewTimeCardPage($timeCardInfo, true);  // read only
         break;
      }
      
      case 'edit_time_card':
      {
         viewTimeCardPage($_SESSION['timeCardInfo'], false);  // editable
         break;
      }
      
      case 'view_time_cards':
      default:
      {
         viewTimeCardsPage();
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
      $_SESSION["timeCardInfo"]->date = $_POST['date'];
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

function getTimeCardInfo($timeCardId)
{
   $timeCardInfo = new TimeCardInfo();
   
   $database = new PPTPDatabase("localhost", "root", "", "pptp");
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $result = $database->getTimeCard($timeCardId);
      
      $timeCard = $result->fetch_assoc();
      
      $timeCardInfo = new TimeCardInfo();
      $timeCardInfo->timeCardId = $timeCard['TimeCard_ID'];
      $timeCardInfo->date = $timeCard['Date'];
      $timeCardInfo->employeeNumber = $timeCard['EmployeeNumber'];
      $timeCardInfo->jobNumber = $timeCard['JobNumber'];
      $timeCardInfo->wcNumber = $timeCard['WCNumber'];
      $timeCardInfo->setupTimeHour = round($timeCard['SetupTime'] / 60);
      $timeCardInfo->setupTimeMinute = round($timeCard['SetupTime'] % 60);
      $timeCardInfo->runTimeHour = round($timeCard['RunTime'] / 60);
      $timeCardInfo->runTimeMinute = round($timeCard['RunTime'] % 60);
      $timeCardInfo->panCount = $timeCard['PanCount'];
      $timeCardInfo->partsCount = $timeCard['PartsCount'];
      $timeCardInfo->scrapCount = $timeCard['ScrapCount'];
      $timeCardInfo->comments = $timeCard['Comments'];
   }
   
   return ($timeCardInfo);
}

function deleteTimeCard($timeCardId)
{
   $result = false;
   
   $database = new PPTPDatabase("localhost", "root", "", "pptp");
   
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
   
   $database = new PPTPDatabase("localhost", "root", "", "pptp");
   
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

function navButton($text, $onClick)
{
   echo
   <<<HEREDOC
   <button class="mdl-button mdl-js-button mdl-button--raised" onclick="$onClick">
      $text
   </button>
HEREDOC;
}
   
function highlightNavButton($text, $onClick)
{
   echo
   <<<HEREDOC
   <button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" onclick="$onClick">
      $text
   </button>
HEREDOC;
}

function cancelButton($onClick)
{
   echo
   <<<HEREDOC
   <button class="mdl-button mdl-js-button mdl-button--raised" onclick="$onClick">
      Cancel
   </button>
HEREDOC;
}
   
function backButton($onClick)
{
   echo
   <<<HEREDOC
   <button class="mdl-button mdl-js-button mdl-button--raised" onclick="$onClick">
      <i class="material-icons">arrow_back</i>
   </button>
HEREDOC;
}

function nextButton($onClick)
{
   echo
<<<HEREDOC
   <button class="mdl-button mdl-js-button mdl-button--raised mdl-button--colored" onclick="$onClick">
      <i class="material-icons">arrow_forward</i>
   </button>
HEREDOC;
}

// *****************************************************************************
//                                  BEGIN

session_start();

processAction(getAction());

processView(getView());

?>

         </div>
      </main>
   </div>
</body>

</html>