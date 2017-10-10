<!DOCTYPE html>
<html>
<body>

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
      Login
      <br>
      Username:<input type="text" name="username" value="$username"><br>
      Password:<input type="password" name="password" value="$password"><br>
      <input type="submit" value="Submit">
   </form>

HEREDOC;
}

function selectActionPage()
{
   echo
<<<HEREDOC
   <a href="pptpTools.php?action=logout">Logout</a>
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
</body>
</html>