<?php

require 'database.php';

$db = new PPTPDatabase("localhost", "root", "", "pptp");

$db->connect();

if ($db->isConnected())
{
   $result = $db->getOperators();

   if ($result->num_rows > 0)
   {
      // output data of each row
      while($row = $result->fetch_assoc())
      {
         echo "employee id: " . $row["EmployeeNumber"]. " - Name: " . $row["FirstName"]. " " . $row["LastName"]. "<br>";
      }
   }
   else
   {
      echo "0 results";
   }

   $db->disconnect();
}

?>