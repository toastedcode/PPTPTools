<!DOCTYPE html>
<html>

<head>
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
</head>

<body>

<?php

require 'database.php';
require 'keypad.php';

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
      echo '<input type="hidden" name="action" value="select_work_center"/>';

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

function selectWorkCenterPage()
{
   $employeeNumber = $_POST['employeeNumber'];

   $database = new PPTPDatabase("localhost", "root", "", "pptp");

   $database->connect();

   if ($database->isConnected())
   {
      $result = $database->getWorkCenters();

      echo "<form action=\"timeCard.php\" method=\"POST\">";
      echo "<input type=\"hidden\" name=\"employeeNumber\" value=\"$employeeNumber\"/>";
      echo "<input type=\"hidden\" name=\"action\" value=\"select_job\"/>";

      // output data of each row
      while($row = $result->fetch_assoc())
      {
         $wcNumber = $row["WCNumber"];

         echo "<button type=\"submit\" name=\"wcNumber\" value=\"$wcNumber\">$wcNumber</button>";
      }

      echo '</form>';
   }
}

function selectJobPage()
{
   $employeeNumber = $_POST['employeeNumber'];
   $wcNumber = $_POST['wcNumber'];

   echo "<body onload=initKeypad()>";

   echo
<<<HEREDOC
   <form action="timeCard.php" method="POST">

      <input type="hidden" name="action" value="enter_time"/>
      <input type="hidden" name="employeeNumber" value="$employeeNumber">
      <input type="hidden" name="wcNumber" value="$wcNumber">

      Job #:<br>
      <input type="text" id="jobNumber-input" name="jobNumber" class="keypadInputCapable" value="">

      <br><br>
      <input type="submit" value="Submit">
   </form>
   <br><br>
   <script>document.getElementById("jobNumber-input").focus();</script>
HEREDOC;

   insertKeypad();

   echo "</body>";
}

function enterTimePage()
{
   $employeeNumber = $_POST['employeeNumber'];
   $wcNumber = $_POST['wcNumber'];
   $jobNumber = $_POST['jobNumber'];

   echo
<<<HEREDOC
   <form action="timeCard.php" method="POST">

      <input type="hidden" name="action" value="enter_part_count"/>
      <input type="hidden" name="employeeNumber" value="$employeeNumber">
      <input type="hidden" name="wcNumber" value="$wcNumber">
      <input type="hidden" name="jobNumber" value="$jobNumber">

      Setup time (hours):<br>
      <button type="button" onclick="changeSetupTimeHour(-1)">-</button>
      <input id="setupTimeHour-input" name="setupTimeHour" type="number" min="0" max="10" value="0">
      <button type="button" onclick="changeSetupTimeHour(1)">+</button>
      <button type="button" onclick="changeSetupTimeMinute(-15)">-</button>
      <input id="setupTimeMinute-input" name="setupTimeMinute" type="number" min="0" max="45" value="0">
      <button type="button" onclick="changeSetupTimeMinute(15)"/>+</button>
      <br>

      Run time (hours):<br>
      <button type="button" onclick="changeRunTimeHour(-1)">-</button>
      <input id="runTimeHour-input" name="runTimeHour" type="number" min="1" max="10" value="0">
      <button type="button" onclick="changeRunTimeHour(1)">+</button>
      <button type="button" onclick="changeRunTimeMinute(-15)">-</button>
      <input id="runTimeMinute-input" name="runTimeMinute" type="number" min="0" max="45" value="0">
      <button type="button" onclick="changeRunTimeMinute(15)">+</button>
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

      function changeSetupTimeHour(delta)
      {
         var field = document.querySelector('#setupTimeHour-input');
         var newValue = parseInt(field.value, 10) + delta;

         // Constrain values.
         newValue = Math.max(0, Math.min(newValue, 10));

         field.value = newValue;
      }

      function changeSetupTimeMinute(delta)
      {
         var field = document.querySelector('#setupTimeMinute-input');
         var newValue = parseInt(field.value, 10) + delta;

         // Constrain values.
         newValue = Math.max(0, Math.min(newValue, 45));

         field.value = newValue;
      }

      function changeRunTimeHour(delta)
      {
         var field = document.querySelector('#runTimeHour-input');
         var newValue = parseInt(field.value, 10) + delta;

         // Constrain values.
         newValue = Math.max(0, Math.min(newValue, 10));

         field.value = newValue;
      }

      function changeRunTimeMinute(delta)
      {
         var field = document.querySelector('#runTimeMinute-input');
         var newValue = parseInt(field.value, 10) + delta;

         // Constrain values.
         newValue = Math.max(0, Math.min(newValue, 45));

         field.value = newValue;
      }
   </script>
HEREDOC;
}

function enterPartCountPage()
{
   $employeeNumber = $_POST['employeeNumber'];
   $wcNumber = $_POST['wcNumber'];
   $jobNumber = $_POST['jobNumber'];
   $setupTimeHour = $_POST['setupTimeHour'];
   $setupTimeMinute = $_POST['setupTimeMinute'];
   $runTimeHour = $_POST['runTimeHour'];
   $runTimeMinute = $_POST['runTimeMinute'];

   echo "<body onload=initKeypad()>";

   echo
<<<HEREDOC
   <form action="timeCard.php" method="POST">

      <input type="hidden" name="action" value="new_time_card"/>
      <input type="hidden" name="employeeNumber" value="$employeeNumber">
      <input type="hidden" name="wcNumber" value="$wcNumber">
      <input type="hidden" name="jobNumber" value="$jobNumber">
      <input type="hidden" name="setupTimeHour" value="$setupTimeHour">
      <input type="hidden" name="setupTimeMinute" value="$setupTimeMinute">
      <input type="hidden" name="runTimeHour" value="$runTimeHour">
      <input type="hidden" name="runTimeMinute" value="$runTimeMinute">

      Pan count:<br>
      <input type="number" id="panCount-input" name="panCount" class="keypadInputCapable" min="1" max="30">
      <br>

      Good part count:<br>
      <input type="number" name="partsCount" class="keypadInputCapable" min="1" max="10000">
      <br>

      Scrap part count:<br>
      <input type="number" name="scrapCount" class="keypadInputCapable" min="1" max="10000">
      <br>

      <br><br>
      <input type="submit" value="Submit">
   </form>
   <br><br>
   <script>document.getElementById("panCount-input").focus();</script>
HEREDOC;

   insertKeypad();

   echo "</body>";
}

function newTimeCardPage()
{
   $date = date('Y-m-d H:i:s');
   $employeeNumber = $_POST['employeeNumber'];
   $wcNumber = $_POST['wcNumber'];
   $jobNumber = $_POST['jobNumber'];
   $setupTimeHour = $_POST['setupTimeHour'];
   $setupTimeMinute = $_POST['setupTimeMinute'];
   $runTimeHour = $_POST['runTimeHour'];
   $runTimeMinute = $_POST['runTimeMinute'];
   $panCount = $_POST['panCount'];
   $partsCount = $_POST['partsCount'];
   $scrapCount = $_POST['scrapCount'];
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
     <input type="hidden" name="date" value="$date">
     <input type="hidden" name="employeeNumber" value="$employeeNumber">
     <input type="hidden" name="wcNumber" value="$wcNumber">
     <input type="hidden" name="jobNumber" value="$jobNumber">
     <input type="hidden" name="setupTimeHour" value="$setupTimeHour">
     <input type="hidden" name="setupTimeMinute" value="$setupTimeMinute">
     <input type="hidden" name="runTimeHour" value="$runTimeHour">
     <input type="hidden" name="runTimeMinute" value="$runTimeMinute">
     <input type="hidden" name="panCount" value="$panCount">
     <input type="hidden" name="partsCount" value="$partsCount">
     <input type="hidden" name="scrapCount" value="$scrapCount">

     Date:<br>
     <input id="date-input" type="date" disabled>
     <br>

     Name:<br>
     <input type="text" name="name" value="$name" disabled>
     <br>

     Employee #:<br>
     <input type="text" value="$employeeNumber" disabled>
     <br>

     Work Center #:<br>
     <input type="text" value="$wcNumber" disabled>
     <br>

     Job #:<br>
     <input type="text" value="$jobNumber" disabled>
     <br>

     Setup time (hours):<br>
     <input type="number" min="0" max="10" value="$setupTimeHour" disabled>
     <input type="number" min="0" max="45" value="$setupTimeMinute" disabled>
     <br>

     Run time (hours):<br>
     <input type="number" min="1" max="10" value="$runTimeHour" disabled>
     <input type="number" min="0" max="45" value="$runTimeMinute" disabled>
     <br>

      Pan count:<br>
      <input type="number" id="panCount-input" min="1" max="30" value="$panCount" disabled>
      <br>

      Good part count:<br>
      <input type="number" min="1" max="10000" value="$partsCount" disabled>
      <br>

      Scrap part count:<br>
      <input type="number" min="1" max="10000" value="$scrapCount" disabled>
      <br>

      Comments:<br>
      <input type="text" name="comments" value="">
      <br>

      <br><br>
      <input type="submit" value="Submit">
   </form>
HEREDOC;
}

function updateTimeCardPage()
{
   $success = false;

   echo $_POST['jobNumber'];

   $database = new PPTPDatabase("localhost", "root", "", "pptp");

   $database->connect();

   if ($database->isConnected())
   {
      $setupTime = (($_POST['setupTimeHour'] * 60) + $_POST['setupTimeMinute']);
      $runTime = (($_POST['runTimeHour'] * 60) + $_POST['runTimeMinute']);

      $timeCard = new stdClass();

      $timeCard->date = $_POST['date'];
      $timeCard->employeeNumber = $_POST['employeeNumber'];
      $timeCard->jobNumber = $_POST['jobNumber'];
      $timeCard->wcNumber = $_POST['wcNumber'];
      $timeCard->setupTime = $setupTime;
      $timeCard->runTime = $runTime;
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
   $employeeNumber = isset($_POST['employeeNumber']) ? $_POST['employeeNumber'] : 0;
   $startDate = isset($_POST['startDate']) ? $_POST['startDate'] : date('Y-m-d');
   $endDate = isset($_POST['endDate']) ? $_POST['endDate'] : date('Y-m-d');

   $database = new PPTPDatabase("localhost", "root", "", "pptp");

   $database->connect();

   if ($database->isConnected())
   {
      $result = $database->getOperators();

      $selected = ($employeeNumber == 0) ? "selected" : "";
      $options = "<option $selected value=0>All</option>";
      while($row = $result->fetch_assoc())
      {
         $selected = ($row["EmployeeNumber"] == $employeeNumber) ? "selected" : "";
         $options .= "<option $selected value=\"" . $row["EmployeeNumber"] . "\">" . $row["FirstName"] . " " . $row["LastName"] . "</option>";
      }

      echo
<<<HEREDOC
      <form action="timeCard.php" method="POST">
         <input type="hidden" name="action" value="edit_time_card"/>
         Employee:
         <select name="employeeNumber">$options</select>
         Start Date:
         <input type="date" name="startDate" value="$startDate">
         End Date:
         <input type="date" name="endDate" value="$endDate">
         <input type="submit" value="Filter">
      </form>
HEREDOC;

      $result = $database->getTimeCards($employeeNumber, $startDate, $endDate);

      echo "<table>";

      // Table header
      echo
<<<HEREDOC
      <tr>
         <td>Date</td>
         <td>Name</td>
         <td>Employee #</td>
         <td>Work Center #</td>
         <td>Job #</td>
         <td>Setup Time</td>
         <td>Run Time</td>
         <td>Pan Count</td>
         <td>Parts Count</td>
         <td>Scrap Count</td>
      <tr>
HEREDOC;

      // output data of each row
      while($row = $result->fetch_assoc())
      {
         $operator = $database->getOperator($row['EmployeeNumber']);
         $name = $operator["FirstName"] . " " . $operator["LastName"];
         $setupTime = round($row['SetupTime'] / 60) . ":" . ($row['SetupTime'] % 60);
         $runTime = round($row['RunTime'] / 60) . ":" . ($row['RunTime'] % 60);

      echo
<<<HEREDOC
      <tr>
         <td>{$row['Date']}</td>
         <td>$name</td>
         <td>{$row['EmployeeNumber']}</td>
         <td>{$row['WCNumber']}</td>
         <td>{$row['JobNumber']}</td>
         <td>$setupTime</td>
         <td>$runTime</td>
         <td>{$row['PanCount']}</td>
         <td>{$row['PartsCount']}</td>
         <td>{$row['ScrapCount']}</td>
      <tr>
HEREDOC;
      }

      echo "</table>";
   }
}

function updateSession()
{
    if (isset($_POST['action']))
    {
        $_SESSION["timeCard"].employeeNumber = $_POST['employeeNumber'];
    }
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

   case 'select_work_center':
   {
      selectWorkCenterPage();
      break;
   }

   case 'select_job':
   {
      selectJobPage();
      break;
   }

   case 'enter_time':
   {
      enterTimePage();
      break;
   }

   case 'enter_part_count':
   {
      enterPartCountPage();
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