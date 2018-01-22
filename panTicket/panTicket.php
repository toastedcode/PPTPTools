<?php

require_once '../database.php';
require_once '../authentication.php';
require_once '../header.php';
require_once 'panTicketInfo.php';

require 'viewPanTicketsPage.php';
require 'viewPanTicketPage.php';
require 'selectOperatorPage.php';
require 'selectTimeCardPage.php';
require 'enterPartNumberPage.php';
require 'enterMaterialNumberPage.php';

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
      case 'update_pan_ticket_info':
      {
         updatePanTicketInfo();
         break;
      }
      
      case 'cancel_pan_ticket':
      {
         unset($_SESSION["panTicketInfo"]);
         break;
      }
      
      case 'new_pan_ticket':
      {
         $_SESSION["panTicketInfo"] = new PanTicketInfo();
         $_SESSION["panTicketInfo"]->date = date('Y-m-d h:m:i');
         break;
      }
      
      case 'edit_pan_ticket':
      {
         if (isset($_POST['panTicketId']))
         {
            $_SESSION["panTicketInfo"] = getPanTicketInfo($_POST['panTicketId']);
         }
         break;
      }
      
      case 'save_pan_ticket':
      {
         updatePanTicketInfo();
         
         updatePanTicket($_SESSION['panTicketInfo']);
         
         $_SESSION["panTicketInfo"] = new PanTicketInfo();
         break;
      }
      
      case 'delete_pan_ticket':
      {
         deletePanTicket($_POST['panTicketId']);
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
         
      case 'select_time_card':
      {
         SelectTimeCard::render();
         break;
      }
         
      case 'enter_part_number':
      {
         EnterPartNumber::render();
         break;
      }
      
      case 'enter_material_number':
      {
         EnterMaterialNumber::render();
         break;
      }
      
      case 'view_pan_ticket':
      {
         ViewPanTicket::render($readOnly = true);
         break;
      }
      
      case 'edit_pan_ticket':
      {
         ViewPanTicket::render($readOnly = false);
         break;
      }
      
      case 'view_pan_tickets':
      default:
      {
         ViewPanTickets::render();
         break;
      }
   }
}

function updatePanTicketInfo()
{
   if (isset($_POST['panTicketId']))
   {
      $_SESSION["panTicketInfo"]->panTicketId = $_POST['panTicketId'];
   }
   
   if (isset($_POST['timeCardId']))
   {
      $_SESSION["panTicketInfo"]->timeCardId = $_POST['timeCardId'];
   }
   
   if (isset($_POST['date']))
   {
      $_SESSION["panTicketInfo"]->date = $_POST['date'];
   }
   
   if (isset($_POST['employeeNumber']))
   {
      $_SESSION["panTicketInfo"]->employeeNumber = $_POST['employeeNumber'];
   }
   
   if (isset($_POST['partNumber']))
   {
      $_SESSION["panTicketInfo"]->partNumber = $_POST['partNumber'];
   }
   
   if (isset($_POST['materialNumber']))
   {
      $_SESSION["panTicketInfo"]->materialNumber = $_POST['materialNumber'];
   }
   
   if (isset($_POST['weight']))
   {
      $_SESSION["panTicketInfo"]->weight = $_POST['weight'];
   }
}

function deletePanTicket($panTicketId)
{
   $result = false;
   
   $database = new PPTPDatabase("localhost", "root", "", "pptp");
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $result = $database->deletePanTicket($panTicketId);
   }
   
   return ($result);
}

function updatePanTicket($panTicketInfo)
{
   $success = false;
   
   $database = new PPTPDatabase();
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $panTicket = new stdClass();
      
      $panTicket->date = $panTicketInfo->date;
      $panTicket->timeCardId = $panTicketInfo->timeCardId;
      $panTicket->partNumber = $panTicketInfo->partNumber;
      $panTicket->materialNumber = $panTicketInfo->materialNumber;
      $panTicket->weight = $panTicketInfo->weight;
      
      if ($panTicketInfo->panTicketId != 0)
      {
         $database->updatePanTicket($panTicketInfo->panTicketId, $panTicket);
      }
      else
      {
         $database->newPanTicket($panTicket);
      }
      
      $success = true;
   }
   
   return ($success);
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
<link rel="stylesheet" type="text/css" href="flex.css"/>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
<link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css"/>
<link rel="stylesheet" type="text/css" href="panTicket.css"/>

<script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
<script src="panTicket.js"></script>
<script src="../validate.js"></script>
</head>

<body>

<?php Header::render("Pan Tickets"); ?>

<div class="flex-horizontal" style="height: 700px;">

   <?php processView(getView())?>

</div>

</body>
</html>