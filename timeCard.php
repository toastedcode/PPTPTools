<!DOCTYPE html>
<html>
<body>

<?php

require 'database.php';

function selectActionPage()
{
   echo
<<<HEREDOC
   <form action="timeCard.php" method="POST">
      <button type="submit" name="action" value="edit_time_card">View/Edit Time Card</button>
      <button type="submit" name="action" value="select_operator">New Time Card</button>
   </form>
HEREDOC;
}

function selectOperatorPage()
{
   $database = new PPTPDatabase("localhost", "root", "", "pptp");

   $database->connect();

   if ($database->isConnected())
   {
      $result = $database->getOperators();

      echo '<form action="timeCard.php" method="POST">';
      echo '<input type="hidden" name="action" value="new_time_card"/>';

      // output data of each row
      while($row = $result->fetch_assoc())
      {
         $name = $row["FirstName"] . $row["LastName"];

         $employeeNumber = $row["EmployeeNumber"];

         echo "<button type=\"submit\" name=\"employeeNumber\" value=\"$employeeNumber\">$name</button>";
      }

      echo '</form>';
   }
}

function newTimeCardPage()
{
   $employeeNumber = $_POST['employeeNumber'];
   $name = NULL;

   $database = new PPTPDatabase("localhost", "root", "", "pptp");

   $database->connect();

   if ($database->isConnected())
   {
      if ($operator = $database->getOperator($employeeNumber))
      {
         $name = $operator["FirstName"] . " " . $operator["LastName"];
      }
   }

   echo
<<<HEREDOC
   <form action="timeCard.php" method="POST">

     <input type="hidden" name="action" value="update_time_card"/>
     <input type="hidden" name="employeeNumber" value="$employeeNumber">

     Date:<br>
     <input id="date-input" type="date" name="date">
     <br>

     Name:<br>
     <input type="text" name="name" value="$name" disabled>
     <br>

     Employee #:<br>
     <input type="text" value="$employeeNumber" disabled>
     <br>

     Job #:<br>
     <input type="text" name="jobNumber" value="">
     <br>

     WC #:<br>
     <input type="text" name="wcNumber" value="">
     <br>

     OPP #:<br>
     <input type="text" name="oppNumber" value="">
     <br>

     Setup time:<br>
     <input name="setupTime" type="number" min="1" max="8">
     <br>

     Run time:<br>
     <input name="runTime" type="number" min="1" max="8">
     <br>

     Pan count:<br>
     <input type="number" name="panCount" min="1" max="10">
     <br>

     Good part count:<br>
     <input type="number" name="partsCount" min="1" max="10000">
     <br>

     Scrap part count:<br>
     <input type="number" name="scrapCount" min="1" max="10000">
     <br>

     Comments:<br>
     <input type="text" name="comments" value="">
     <br>

     <br><br>
     <input type="submit" value="Submit">
   </form>

   <script>

      var date = new Date();
      field = document.querySelector('#date-input');

      var day = date.getDate();
      if (day < 10)
      {
         day= "0" + day;
      }

      var month = date.getMonth() + 1;
      if (month < 10)
      {
         month= "0" + month;
      }

      field.value = date.getFullYear()+ "-" + month + "-" + day;

   </script>
HEREDOC;
}

function updateTimeCardPage()
{
   $success = false;

   $database = new PPTPDatabase("localhost", "root", "", "pptp");

   $database->connect();

   if ($database->isConnected())
   {
      $timeCard = new stdClass();

      $timeCard->date = $_POST['date'];
      $timeCard->employeeNumber = $_POST['employeeNumber'];
      $timeCard->jobNumber = $_POST['jobNumber'];
      $timeCard->wcNumber = $_POST['wcNumber'];
      $timeCard->oppNumber = $_POST['oppNumber'];
      $timeCard->setupTime = $_POST['setupTime'];
      $timeCard->runTime = $_POST['runTime'];
      $timeCard->panCount = $_POST['panCount'];
      $timeCard->partsCount = $_POST['partsCount'];
      $timeCard->scrapCount = $_POST['scrapCount'];
      $timeCard->comments = $_POST['comments'];

      if (isset($_POST['timeCardId']))
      {
         $database->updateTimeCard($_POST['timeCardId'], $timeCard);
      }
      else
      {
         $database->newTimeCard($timeCard);
      }

      $success = true;
   }

   if ($success)
   {
      echo 'Successful operation.<br>';
   }
   else
   {
      echo 'Unsuccessful operation.<br>';
   }

   echo '<button type="button" onclick="location.href=\'pptpTools.php\';">Time Cards</button>';
}

function editTimeCardPage()
{
   echo "editTimeCardPage";
}

$action = 'select_action';
if (isset($_POST['action']))
{
   $action = $_POST['action'];
}

switch ($action)
{
   case 'edit_new':
   {
      editNewPage();
      break;
   }

   case 'select_operator':
   {
      selectOperatorPage();
      break;
   }

   case 'new_time_card':
   {
      newTimeCardPage();
      break;
   }

   case 'edit_time_card':
   {
      editTimeCardPage();
      break;
   }

   case 'update_time_card':
   {
      updateTimeCardPage();
      break;
   }

   case 'select_action':
   default:
   {
      selectActionPage();
      break;
   }
}

?>

</body>
</html>