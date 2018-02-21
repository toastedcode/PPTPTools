<?php

require_once 'database.php';
require_once 'header.php';
require_once 'authentication.php';

function loginPage()
{
   
   $username = "";
   if (isset($_POST['username']))
   {
      $username = $_POST['username'];
   }
   
   $password = "";
   if (isset($_POST['password']))
   {
      $password = $_POST['password'];
   }

   echo
<<<HEREDOC

<!-- Wide card with share menu button -->
<style>
.demo-card-wide.mdl-card {
  width: 512px;
  margin: auto;
}
.demo-card-wide > .mdl-card__title {
  color: #fff;
  height: 176px;
  background: url('./images/parts.jpg') center / cover;
}
.demo-card-wide > .mdl-card__menu {
  color: #fff;
}

.login-form {
   margin-left: 50px;
   margin-bottom: 10px;
}
</style>

   <div class="demo-card-wide mdl-card mdl-shadow--2dp">
      <div class="mdl-card__title">
         <!--h2 class="mdl-card__title-text">Welcome</h2-->
      </div>
      <div class="mdl-card__supporting-text">
         <b>Pittsburgh Precision Tools</b> is an online production monitoring toolkit that brings
         together a suite of data entry and analysis software, giving you a clear window into your shop's 
         daily operations. 
         <br/>
         <br/>
         Please login to get started.
      </div>
      <div class="mdl-card__actions mdl-card--border">
         <div class="login-form">
         <form method="POST">
            <input type="hidden" name="action" value="login">
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
               <input id="username_input" class="mdl-textfield__input" type="text" name="username" value="$username">
               <label class="mdl-textfield__label" for="username_input">Username</label>
            </div>
            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
               <input id="password_input" class="mdl-textfield__input" type="password" name="password" value="$password">
               <label class="mdl-textfield__label" for="password_input">Password</label>
            </div>
            <div>
               <button class="mdl-button mdl-js-button mdl-button--raised">Login</button>
            </div>
          </form>
          </div>
      </div>
   </div>
HEREDOC;
}

function selectActionPage()
{
   echo
<<<HEREDOC
   <!-- Wide card with share menu button -->
   <style>

   .select-action-card-header {
     color: #fff;
     height: 176px;
     background: url('./images/parts2.jpg') center / cover;
   }

   .select-action-card {
     width: 1000px;
     margin: auto;
   }
   .select-action-card > .mdl-card__title {
     height: 176px;
   }

   .action-button {
      display:flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;

      width: 150px;
      height: 150px;

      color: #fff;
      font-size: 18px;
      text-shadow: -1px 1px #417cb8;

      margin: 10px;

      background-color: #6496c8;
      
   }

   .action-button:hover {
      background-color: #346392;
      text-shadow: -1px 1px #27496d;
   }

   .action-button:active {
      background-color: #27496d;
      text-shadow: -1px 1px #193047;
   }

   .action-button-icon {
      font-size: 80px;
   }

   .button-container {
      padding-top: 25px;
      padding-right: 25px;
      padding-bottom: 25px;
      padding-left: 25px;
      margin: auto;
   }

   .select-action-card-header-div {

   }

   </style>

   <div class="flex-vertical card-div">
      <div class="flex-vertical select-action-card-header"></div>

      <div class="flex-horizontal content-div" style="justify-content: center; height:400px;">

         <div class="action-button" onclick="location.href='jobs/jobs.php?view=view_jobs';">
            <div><i class="material-icons action-button-icon">assignment</i></div>
            <div>Jobs</div>
         </div>

         <div class="action-button" onclick="location.href='timecard/timeCard.php?view=view_time_cards';">
            <div><i class="material-icons action-button-icon">schedule</i></div>
            <div>Time Cards</div>
         </div>

        <div  class="action-button" onclick="location.href='panTicket/panTicket.php?view=view_pan_tickets';">
           <i class="material-icons action-button-icon">receipt</i>
           <div>Pan Tickets</div>
        </div>

        <div class="action-button" onclick="location.href='partWasherLog/partWasherLog.php?view=view_part_washer_log';">
           <i class="material-icons action-button-icon">opacity</i>
           <div>Parts Washer</div>
           <div>Log</div>
        </div>

        <div class="action-button" onclick="location.href='partInspection/partInspection.php?view=view_part_inspections';">
           <i class="material-icons action-button-icon">search</i>
           <div>Part</div>
           <div>Inspections</div>
        </div>

        <div class="action-button" onclick="location.href='machineStatus/machineStatus.php?view=view_machines';">
           <i class="material-icons action-button-icon">verified_user</i>
           <div>Machine</div>
           <div>Status</div>
        </div>

        <div class="action-button" onclick="location.href='productionSummary';">
           <i class="material-icons action-button-icon">show_chart</i>
           <div>Production</div>
           <div>Summary</div>
        </div>

      </div>
   </div>
HEREDOC;
}


function login($username, $password)
{
   $result = Authentication::authenticate($username, $password);
}

function logout()
{
   Authentication::deauthenticate();
}

function pageHeader()
{
   echo
<<<HEREDOC
   <style>
      .mdl-layout__header {
         margin-bottom: 100px;
      }
   </style>
   <header class="mdl-layout__header">
      <div class="mdl-layout__header-row">
         <!-- Title -->
         <span class="mdl-layout-title">Pittsburgh Precision Tools</span>
         <!-- Add spacer, to align navigation to the right -->
         <div class="mdl-layout-spacer"></div>
HEREDOC;
   
   if (Authentication::isAuthenticated())
   {
      $authenticatedUser = Authentication::getAuthenticatedUser();
      
      $self = $_SERVER['PHP_SELF'];
      
      echo
<<<HEREDOC
      <!-- Navigation.-->
      <nav class="mdl-navigation">

         <div style="display:flex; flex-direction:row;">
            <i class="material-icons button-icon">person</i>
            <div>&nbsp $authenticatedUser &nbsp | &nbsp</div>
         </div>

         <a class="mdl-navigation__link" href="$self?action=logout">Logout</a>

      </nav>
HEREDOC;
   }
   
   echo
   <<<HEREDOC
      </div>
   </header>
HEREDOC;
}

// *****************************************************************************
//                                  BEGIN

session_start();

$action = '';
if (isset($_POST['action']))
{
   $action = $_POST['action'];
}
else if (isset($_GET['action']))
{
   $action = $_GET['action'];
}

switch ($action)
{
   case 'login':
   {
      login($_POST['username'], $_POST['password']);
      break;
   }

   case 'logout':
   {
      logout();
      break;
   }

   default:
   {
     // Unsupported action.
   }
}

$background = Authentication::isAuthenticated() ? "#eee" : "url('./images/PPTPFloor.jpg') center / cover";

?>

<!DOCTYPE html>
<html>

<head>
   <!--  Material Design Lite -->
   <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
   <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css" />

   <!-- PPTP -->
   <link rel="stylesheet" type="text/css" href="flex.css"/>
   <link rel="stylesheet" type="text/css" href="pptpTools.css"/>

   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
</head>

<body style="background: <?php echo $background?>;">

<?php Header::render("Pittsburgh Precision Tools"); ?>

<div class="flex-horizontal" style="height: 700px;">

<?php 
if (Authentication::isAuthenticated())
{
   selectActionPage();
}
else
{
   loginPage();
}
?>

</div>

</body>

</html>