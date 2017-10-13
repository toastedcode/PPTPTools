<?php

function viewTimeCardPage($timeCardInfo, $readOnly = true)
{
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
   
   // Convert the stored date into the format required in the <input> tag.
   $date = date_format(date_create($timeCardInfo->date), 'Y-m-d');
   
   $disabled = $readOnly ? "disabled" : "";
   
   $isNewTimeCard = ($timeCardInfo->timeCardId == 0);
   
   $backButton = "";
   if ($isNewTimeCard)
   {
      $backButton = "<button type=\"button\" onclick=\"submitForm('timeCardForm', 'timeCard.php', 'enter_part_count', 'update_time_card_info')\">Back</button>";
   }
   
   echo
   <<<HEREDOC
   <script src="timeCard.js"></script>

   <form id="timeCardForm" action="timeCard.php" method="POST">
     
      Date:<br>
      <input type="date" value="$timeCardInfo->date" disabled>
      <br>
     
      Name:<br>
      <input type="text" name="name" value="$name" disabled>
      <br>
     
      Employee #:<br>
      <input type="text" value="$timeCardInfo->employeeNumber" disabled>
      <br>
     
      Work Center #:<br>
      <input type="text" value="$timeCardInfo->wcNumber" $disabled>
      <br>
     
      Job #:<br>
      <input type="text" value="$timeCardInfo->jobNumber" $disabled>
      <br>
     
      Setup time (hours):<br>
      <input type="number" min="0" max="10" value="$timeCardInfo->setupTimeHour" $disabled>
      <input type="number" min="0" max="45" value="$timeCardInfo->setupTimeMinute" $disabled>
      <br>
     
      Run time (hours):<br>
      <input type="number" min="1" max="10" value="$timeCardInfo->runTimeHour" $disabled>
      <input type="number" min="0" max="45" value="$timeCardInfo->runTimeMinute" $disabled>
      <br>
     
      Pan count:<br>
      <input type="number" id="panCount-input" min="1" max="30" value="$timeCardInfo->panCount" $disabled>
      <br>
      
      Good part count:<br>
      <input type="number" min="1" max="10000" value="$timeCardInfo->partsCount" $disabled>
      <br>
      
      Scrap part count:<br>
      <input type="number" min="1" max="10000" value="$timeCardInfo->scrapCount" $disabled>
      <br>
      
      Comments:<br>
      <input type="text" name="comments" value="$timeCardInfo->comments" $disabled>
      <br>
      
      <br>
HEREDOC;

   if (!$readOnly)
   {
      echo "<button type=\"button\" onclick=\"submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')\">Cancel</button>";
   }
   
   if ($isNewTimeCard)
   {
      echo "<button type=\"button\" onclick=\"submitForm('timeCardForm', 'timeCard.php', 'enter_part_count', 'update_time_card_info')\">Back</button>";
   }
   
   if ($readOnly)
   {
      echo "<button type=\"button\" onclick=\"submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')\">Ok</button>";
   }
   else
   {
      echo "<button type=\"button\" onclick=\"submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'save_time_card')\">Save</button>";
   }

   echo "</form>";
}

?>