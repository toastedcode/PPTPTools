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
      <span class="mdl-layout-title">Pittsburgh Precision Tools</span>
      <!-- Add spacer, to align navigation to the right -->
      <div class="mdl-layout-spacer"></div>
      <!-- Navigation. We hide it in small screens. -->
      <nav class="mdl-navigation">
         <a class="mdl-navigation__link" href="pptpTools.php?action=logout">Logout</a>
      </nav>
    </div>
  </header>

   <main class="mdl-layout__content">
   <div class="page-content">

<?php

require_once 'database.php';

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
   <form action="pptpTools.php" method="POST">
      <input type="hidden" name="action" value="login">
      <br>
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
         <input id="username_input" class="mdl-textfield__input" type="text" name="username" value="$username">
         <label class="mdl-textfield__label" for="username_input">Username</label>
      </div>
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
         <input id="password_input" class="mdl-textfield__input" type="text" name="password" value="$password">
         <label class="mdl-textfield__label" for="password_input">Password</label>
      </div>
      <button class="mdl-button mdl-js-button mdl-button--raised">Login</button>
   </form>

HEREDOC;
}

function selectActionPage()
{
   echo
<<<HEREDOC
   <button type="button" onclick="location.href='timecard/timeCard.php?view=view_time_cards';">Time Cards</button>
   <button type="button" onclick="location.href='panTickets.html';">Pan Tickets</button>
   <button type="button" onclick="location.href='partsWasher.html';">Parts Washer Log</button>
   <button type="button" onclick="location.href='productionSummary';">Production Summary</button>
HEREDOC;
}

function isLoggedIn()
{
   return isset($_SESSION['username']);
}

function login($username, $password)
{
   $database = new PPTPDatabase("localhost", "root", "", "pptp");

   $database->connect();

   if ($database->isConnected())
   {
      $result = $database->getUser($username);

      $row = $result->fetch_assoc();
      if ($row)
      {
      	if ($password == $row['Password'])
      	{
      	   // Correct password.
      	   $_SESSION['username'] = $username;
      	}
      	else
      	{
      	   // Incorrect password.
      	}
      }
      else
      {
         // Invalid username.
      }
   }
}

function logout()
{
   unset($_SESSION['username']);
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

if (isLoggedIn())
{
   selectActionPage();
}
else
{
   loginPage();
}

?>
</div>
</main>
</div>
</body>
</html>