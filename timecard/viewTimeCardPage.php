<?php

function viewTimeCardPage($timeCardInfo)
{
   $date = date('Y-m-d');
   $name = NULL;
   
   $database = new PPTPDatabase("localhost", "root", "", "pptp");
   
   $database->connect();
   
   if ($database->isConnected())
   {
      if ($operator = $database->getOperator($timeCardInfo->employeeNumber))
      {
         $name = $operator["FirstName"] . " " . $operator["LastName"];
      }
   }
   
   echo
   <<<HEREDOC
   <script src="timeCard.js"></script>

   <form action="timeCard.php" method="POST">
   
     <input type="hidden" name="action" value="update_time_card"/>
     <input type="hidden" name="date" value="$date">
     
     Date:<br>
     <input id="date-input" type="date" value="$date" disabled>
     <br>
     
     Name:<br>
     <input type="text" name="name" value="$name" disabled>
     <br>
     
     Employee #:<br>
     <input type="text" value="$timeCardInfo->employeeNumber" disabled>
     <br>
     
     Work Center #:<br>
     <input type="text" value="$timeCardInfo->wcNumber" disabled>
     <br>
     
     Job #:<br>
     <input type="text" value="$timeCardInfo->jobNumber" disabled>
     <br>
     
     Setup time (hours):<br>
     <input type="number" min="0" max="10" value="$timeCardInfo->setupTimeHour" disabled>
     <input type="number" min="0" max="45" value="$timeCardInfo->setupTimeMinute" disabled>
     <br>
     
     Run time (hours):<br>
     <input type="number" min="1" max="10" value="$timeCardInfo->runTimeHour" disabled>
     <input type="number" min="0" max="45" value="$timeCardInfo->runTimeMinute" disabled>
     <br>
     
      Pan count:<br>
      <input type="number" id="panCount-input" min="1" max="30" value="$timeCardInfo->panCount" disabled>
      <br>
      
      Good part count:<br>
      <input type="number" min="1" max="10000" value="$timeCardInfo->partsCount" disabled>
      <br>
      
      Scrap part count:<br>
      <input type="number" min="1" max="10000" value="$timeCardInfo->scrapCount" disabled>
      <br>
      
      Comments:<br>
      <input type="text" name="comments" value="$timeCardInfo->comments">
      <br>
      
      <br><br>
      <button type="submit" name="view" value="enter_part_count">Back</button>
      <button type="submit" name="view" value="view_time_cards">Save</button>
      <button type="button" onclick="onCancel()">Cancel</button>
   </form>
HEREDOC;
}

?>