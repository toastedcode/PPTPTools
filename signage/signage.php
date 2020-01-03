<?php

require_once '../common/authentication.php';
require_once '../common/database.php';
require_once '../common/header.php';
require_once '../common/signInfo.php';

require 'viewSign.php';
require 'viewSigns.php';

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
      case 'update_sign_info':
      {
         updateSignInfo();
         break;
      }
         
      case 'cancel_sign':
      {
         unset($_SESSION["signInfo"]);
         break;
      }
         
      case 'new_sign':
      {
         $_SESSION["signInfo"] = new SignInfo();
         break;
      }
         
      case 'edit_sign':
      {
         if (isset($_POST['signId']))
         {
            $_SESSION["signInfo"] = SignInfo::load($_POST['signId']);
         }
         break;
      }
         
      case 'save_sign':
      {
         if (isset($_SESSION['signInfo']))
         {
            updateSignInfo();
            
            updateSign($_SESSION['signInfo']);
            
            unset($_SESSION["signInfo"]);
         }
         break;
      }
         
      case 'delete_sign':
      {
         if (isset($_POST['signId']))
         {
            deleteSign($_POST['signId']);
         }
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
      case 'new_sign':
      case 'edit_sign':
      case 'view_sign':
      {
         $page = new ViewSign();
         $page->render($view);
         break;
      }
         
      case 'view_signs':
      default:
      {
         $page = new viewSigns();
         $page->render();
         break;
      }
   }
}

function updateSignInfo()
{
   if (isset($_POST['signId']))
   {
      $_SESSION["signInfo"]->signId = $_POST['signId'];
   }
   
   if (isset($_POST['name']))
   {
      $_SESSION["signInfo"]->name = $_POST['name'];
   }
   
   if (isset($_POST['description']))
   {
      $_SESSION["signInfo"]->description = $_POST['description'];
   }
   
   if (isset($_POST['url']))
   {
      $_SESSION["signInfo"]->url = $_POST['url'];
   }
}

function deleteSign($signId)
{
   $result = false;
   
   $database = new PPTPDatabase();
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $result = $database->deleteSign($signId);
   }
   
   return ($result);
}

function updateSign($signInfo)
{
   $success = false;
   
   $database = new PPTPDatabase();
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $result = $database->getSign($signInfo->signId);
      
      $signExists = ($result && ($result->num_rows == 1));
      
      if ($signExists)
      {
         $database->updateSign($signInfo);
      }
      else
      {
         $database->newSign($signInfo);
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
   
   $url = "./signage/signage.php" . "?" . http_build_query($params);
   
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

<!DOCTYPE html>
<html>

<head>

   <meta name="viewport" content="width=device-width, initial-scale=1">

   <link rel="stylesheet" type="text/css" href="../common/flex.css"/>
   <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
   <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css"/>
   <link rel="stylesheet" type="text/css" href="../common/form.css"/>
   <link rel="stylesheet" type="text/css" href="../common/common.css"/>
   <link rel="stylesheet" type="text/css" href="signage.css"/>
   
   <script src="../common/common.js"></script>
   <script src="../common/validate.js"></script>
   <script src="signage.js"></script>
   
</head>

<body>

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="flex-horizontal main">
      
      <div class="flex-horizontal sidebar hide-on-tablet"></div> 
   
      <?php processView(getView())?>
   
   </div>
   
   <script>
      preserveSession();
   </script>

</body>

</html>