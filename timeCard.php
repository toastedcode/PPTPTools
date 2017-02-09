<!DOCTYPE html>
<html>
<body>

<?php

require 'database.php';

function editNewPage()
{
   echo
<<<HEREDOC
   <button type="button" onclick="location.href='timeCard.php?action=';">View/Edit Time Card</button>
   <button type="button" onclick="location.href='timeCard.php?action=select_operator';">New Time Card</button>
HEREDOC;
}

function selectOperatorPage()
{
   $database = new PPTPDatabase("localhost", "root", "", "pptp");

   $database->connect();

   if ($database->isConnected())
   {
      $result = $database->getOperators();

      // output data of each row
      while($row = $result->fetch_assoc())
      {
         $name = $row["FirstName"] . $row["LastName"];

         echo
<<<HEREDOC
         <button type="button" onclick="location.href='timeCard.php?action=new_time_card';">$name</button>
HEREDOC;
      }
   }
}

function newTimeCardPage()
{
   echo
<<<HEREDOC
   <form action="action_page.php">
     Date:<br>
     <input id="date-input" type="date" name="date">
     <br>

     Name:<br>
     <input type="text" name="name" disabled>
     <br>

     Employee #:<br>
     <input type="text" name="employeeNumber" value="" disabled>
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
     <input name="setupTime" type="time" min="0:00" max="100:59">
     <br>

     Run time:<br>
     <input name="runTime" type="time" min="0:00" max="100:59">
     <br>

     Pan count:<br>
     <input type="number" name="panCount" min="1" max="10">
     <br>

     Good part count:<br>
     <input type="number" name="goodCount" min="1" max="10000">
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

function editTimeCardPage()
{
   echo "editTimeCardPage";
}

switch ($_GET['action'])
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
      deleteTimeCardPage();
      break;
   }

   default:
   {
      break;
   }
}

?>

</body>
</html>