<?php

require_once '../common/authentication.php';
require_once '../common/database.php';
require_once '../common/header.php';
require_once '../common/userInfo.php';

require 'viewUser.php';
require 'viewUsers.php';

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
      case 'update_user_info':
      {
         updateUserInfo();
         break;
      }
      
      case 'cancel_user':
      {
         unset($_SESSION["userInfo"]);
         break;
      }
      
      case 'new_user':
      {
         $_SESSION["userInfo"] = new UserInfo();
         break;
      }
      
      case 'edit_user':
      {
         if (isset($_POST['employeeNumber']))
         {
            $_SESSION["userInfo"] = UserInfo::load($_POST['employeeNumber']);
         }
         break;
      }
      
      case 'save_user':
      {
         if (isset($_SESSION['userInfo']))
         {
            updateUserInfo();
            
            updateUser($_SESSION['userInfo']);
            
            unset($_SESSION["userInfo"]);
         }
         break;
      }
      
      case 'delete_user':
      {
         if (isset($_POST['employeeNumber']))
         {
            deleteUser($_POST['employeeNumber']);
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
      case 'new_user':
      case 'edit_user':
      case 'view_user':
      {
         $page = new ViewUser();
         $page->render($view);
         break;
      }
      
      case 'view_users':
      default:
      {
         $page = new ViewUsers();
         $page->render();
         break;
      }
   }
}

function updateUserInfo()
{
   if (isset($_POST['employeeNumber']))
   {
      $_SESSION["userInfo"]->employeeNumber = intval($_POST['employeeNumber']);
   }
   
   if (isset($_POST['username']))
   {
      $_SESSION["userInfo"]->username = $_POST['username'];
   }
   
   if (isset($_POST['password']))
   {
      $_SESSION["userInfo"]->password = $_POST['password'];
   }
   
   if (isset($_POST['firstName']))
   {
      $_SESSION["userInfo"]->firstName = $_POST['firstName'];
   }
   
   if (isset($_POST['lastName']))
   {
      $_SESSION["userInfo"]->lastName = $_POST['lastName'];
   }
   
   if (isset($_POST['roles']))
   {
      $_SESSION["userInfo"]->roles= intval($_POST['roles']);
   }
   
   if (isset($_POST['permissions']))
   {
      $_SESSION["userInfo"]->permissions = intval($_POST['permissions']);
   }
   
   if (isset($_POST['email']))
   {
      $_SESSION["userInfo"]->email = $_POST['email'];
   }
   
   foreach (Permission::getPermissions() as $permission)
   {
      $name = "permission-" . $permission->permissionId;
      
      if (isset($_POST[$name]))
      {
         // Set bit.
         $_SESSION["userInfo"]->permissions |= $permission->bits;                  
      }
      else if ($permission->isSetIn($_SESSION["userInfo"]->permissions))
      {
         // Clear bit.
         $_SESSION["userInfo"]->permissions &= ~($permission->bits);
      }
   }
   
}

function deleteUser($employeeNumber)
{
   $result = false;
   
   $database = new PPTPDatabase();
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $result = $database->deleteUser($employeeNumber);
   }
   
   return ($result);
}

function updateUser($userInfo)
{
   $success = false;
   
   $database = new PPTPDatabase();
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $result = $database->getUser($userInfo->employeeNumber);
      
      $userExists = ($result && ($result->num_rows == 1));
      
      if ($userExists)
      {
         $database->updateUser($userInfo);
      }
      else
      {
         $database->newUser($userInfo);
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
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
<link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css"/>
<link rel="stylesheet" type="text/css" href="../common/flex.css"/>
<link rel="stylesheet" type="text/css" href="../common/common.css"/>
<link rel="stylesheet" type="text/css" href="user.css"/>


<script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
<script src="user.js"></script>
<script src="/pptp/common/common.js"></script> <!--  use $ROOT variable -->
<script src="../validate.js"></script>
</head>

<body>

<?php Header::render("Users"); ?>

<div class="flex-horizontal" style="height: 700px;">

   <?php processView(getView())?>

</div>

</body>
</html>