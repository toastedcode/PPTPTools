<?php

require_once './common/authentication.php';
require_once './common/database.php';
require_once './common/header.php';
require_once './common/permissions.php';

class Activity
{
   const UNKNOWN = 0;
   const FIRST = 1;
   const JOBS = Activity::FIRST;
   const TIME_CARD = 2;
   const PART_WEIGHT = 3;
   const PART_WASH = 4;
   const PART_INSPECTION = 5;
   const LINE_INSPECTION = 6;
   const MACHINE_STATUS = 7;
   const PRODUCTION_SUMMARY = 8;
   const USER = 9;
   const SIGNAGE = 10;
   const LAST = Activity::SIGNAGE;
      
   private static $permissionMasks = null;
   
   private static function getPermissionMasks()
   {
      if (Activity::$permissionMasks == null)
      {
         Activity::$permissionMasks = array(
            Permission::getPermission(Permission::VIEW_JOB)->bits,                 // JOBS
            Permission::getPermission(Permission::VIEW_TIME_CARD)->bits,           // TIME_CARD
            Permission::getPermission(Permission::VIEW_PART_WEIGHT_LOG)->bits,     // PART_WEIGHT
            Permission::getPermission(Permission::VIEW_PART_WASHER_LOG)->bits,     // PART_WASH
            Permission::getPermission(Permission::VIEW_PART_INSPECTION)->bits,     // PART_INSPECTION
            Permission::getPermission(Permission::VIEW_LINE_INSPECTION)->bits,     // LINE_INSPECTION
            Permission::getPermission(Permission::VIEW_MACHINE_STATUS)->bits,      // MACHINE_STATUS
            Permission::getPermission(Permission::VIEW_PRODUCTION_SUMMARY)->bits,  // PRODUCTION_SUMMARY
            Permission::getPermission(Permission::VIEW_USER)->bits,                // USER
            Permission::getPermission(Permission::VIEW_SIGN)->bits);               // SIGNAGE
      }
      
      return (Activity::$permissionMasks);
   }

   public static function getPermissionMask($activity)
   {
      $permissionMask = 0;
      
      if (($activity >= Activity::FIRST) && ($activity <= Activity::LAST))
      {
         $permissionMask = Activity::getPermissionMasks()[$activity - Activity::FIRST];
      }
      
      return ($permissionMask);
   }
   
   public static function isAllowed($activity, $permissions)
   {
      return (($permissions & Activity::getPermissionMask($activity)) > 0);
   }
}

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
         Login and let's get started.
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

function selectActivityPage()
{
   $permissions = Authentication::getPermissions();
   
   // Jobs
   $usersButton = "";
   if (Activity::isAllowed(Activity::USER, $permissions))
   {
      $usersButton =
<<<HEREDOC
      <div class="action-button" onclick="location.href='user/user.php?view=view_users';">
         <div><i class="material-icons action-button-icon">group</i></div>
         <div>Users</div>
      </div>
HEREDOC;
   }

   // Jobs
   $jobsButton = "";
   if (Activity::isAllowed(Activity::JOBS, $permissions))
   {
      $jobsButton = 
<<<HEREDOC
      <div class="action-button" onclick="location.href='jobs/jobs.php?view=view_jobs';">
         <div><i class="material-icons action-button-icon">assignment</i></div>
         <div>Jobs</div>
      </div>
HEREDOC;
   }
      
   // Time Card
   $timeCardButton = "";
   if (Activity::isAllowed(Activity::TIME_CARD, $permissions))
   {
      $timeCardButton =
<<<HEREDOC
      <div class="action-button" onclick="location.href='timecard/timeCard.php?view=view_time_cards';">
         <div><i class="material-icons action-button-icon">schedule</i></div>
         <div>Time Cards</div>
      </div>
HEREDOC;
   }
      
   // Part Weight
   $partWeightButton = "";
   if (Activity::isAllowed(Activity::PART_WEIGHT, $permissions))
   {
      $partWeightButton =
<<<HEREDOC
     <div class="action-button" onclick="location.href='partWeightLog/partWeightLog.php?view=view_part_weight_log';">
        <i class="material-icons action-button-icon">fingerprint</i>
        <div>Part Weight</div>
        <div>Log</div>
     </div>
HEREDOC;
   }
      
   // Part Wash
   $partWashButton = "";
   if (Activity::isAllowed(Activity::PART_WASH, $permissions))
   {
      $partWashButton =
<<<HEREDOC
     <div class="action-button" onclick="location.href='partWasherLog/partWasherLog.php?view=view_part_washer_log';">
        <i class="material-icons action-button-icon">opacity</i>
        <div>Part Washer</div>
        <div>Log</div>
     </div>
HEREDOC;
   }
      
   // Part Inspection
   $partInspectionButton = "";
   if (Activity::isAllowed(Activity::PART_INSPECTION, $permissions))
   {
      $partInspectionButton =
<<<HEREDOC
     <div class="action-button" onclick="location.href='partInspection/partInspection.php?view=view_part_inspections';">
        <i class="material-icons action-button-icon">search</i>
        <div>Part</div>
        <div>Inspections</div>
     </div>
HEREDOC;
   }
      
   // Line Inspection
   $lineInspectionButton = "";
   if (Activity::isAllowed(Activity::LINE_INSPECTION, $permissions))
   {
      $lineInspectionButton =
<<<HEREDOC
     <div class="action-button" onclick="location.href='lineInspection/lineInspection.php?view=view_line_inspections';">
        <i class="material-icons action-button-icon">thumbs_up_down</i>
        <div>Line</div>
        <div>Inspections</div>
     </div>
HEREDOC;
   }
      
   // Machine Status
   $machineStatusButton = "";
   /*
   if (Activity::isAllowed(Activity::MACHINE_STATUS, $permissions))
   {
      $machineStatusButton =
<<<HEREDOC
     <div class="action-button" onclick="location.href='machineStatus/machineStatus.php?view=view_machines';">
        <i class="material-icons action-button-icon">verified_user</i>
        <div>Machine</div>
        <div>Status</div>
     </div>
HEREDOC;
   }
   */
      
   // Production Summary
   $productionSummaryButton = "";
   /*
   if (Activity::isAllowed(Activity::PRODUCTION_SUMMARY, $permissions))
   {
      $productionSummaryButton =
<<<HEREDOC
     <div class="action-button" onclick="location.href='productionSummary';">
        <i class="material-icons action-button-icon">show_chart</i>
        <div>Production</div>
        <div>Summary</div>
     </div>
HEREDOC;
   }
   */
      
   // Digital Signage
   $digitalSignageButton = "";
   if (Activity::isAllowed(Activity::SIGNAGE, $permissions))
   {
      $digitalSignageButton =
<<<HEREDOC
     <div class="action-button" onclick="location.href='signage/signage.php?view=view_signs';">
        <i class="material-icons action-button-icon">tv</i>
        <div>Digital</div>
        <div>Signage</div>
     </div>
HEREDOC;
   }
   
   echo
<<<HEREDOC
   <div class="flex-horizontal" style="align-items:stretch; justify-content: flex-start; height:100%">
      
      <div class="flex-horizontal sidebar hide-on-tablet"></div> 

      <div class="flex-vertical content">

         <div class="heading">Let's Get Started!</div>

         <div class="description">PPTP Tools gives you tons of ways to analyze production every step of the way.  Select one of the activity icons below to get a window into how your floor is operating today.</div>

         <div class="flex-horizontal inner-content;" style="flex-wrap: wrap; max-width: 900px;">
   
            $usersButton
            
            $jobsButton
   
            $timeCardButton
            
            $partWeightButton
            
            $partWashButton
            
            $partInspectionButton
            
            $lineInspectionButton
            
            $machineStatusButton
            
            $productionSummaryButton
            
            $digitalSignageButton
   
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
   
   session_unset();
}

function redirect($url)
{
   unset($_SESSION["redirect"]);
   
   header("Location: $url");
   exit;
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

   <meta name="viewport" content="width=device-width, initial-scale=1">

   <!--  Material Design Lite -->
   <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
   <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css" />

   <!-- PPTP -->
   <link rel="stylesheet" type="text/css" href="common/flex.css"/>
   <link rel="stylesheet" type="text/css" href="common/common.css"/>
   <link rel="stylesheet" type="text/css" href="pptpTools.css"/>

   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
   
</head>

<body style="background: <?php echo $background?>;">

   <?php Header::render("PPTP Tools"); ?>
   
   <?php 
   if (Authentication::isAuthenticated())
   {
      if (isset($_SESSION["redirect"]))
      {
         redirect($_SESSION["redirect"]);
      }
      else
      {
         selectActivityPage();
      }
   }
   else
   {
      loginPage();
   }
   ?>

</body>

</html>