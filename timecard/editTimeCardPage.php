<?php

function editTimeCardPage($timeCardId)
{
   $database = new PPTPDatabase("localhost", "root", "", "pptp");
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $result = $database->getTimeCard($timeCardId);
      
      if ($result)
      {
         $timeCard = $result->fetch_assoc();
         
         $timeCardInfo = new TimeCardInfo();
         $timeCardInfo->timeCardId = $timeCard['TimeCard_ID'];
         $timeCardInfo->date = $timeCard['Date'];
         $timeCardInfo->employeeNumber = $timeCard['EmployeeNumber'];
         $timeCardInfo->jobNumber = $timeCard['JobNumber'];
         $timeCardInfo->wcNumber = $timeCard['WCNumber'];
         $timeCardInfo->setupTimeHour = round($timeCard['SetupTime'] / 60);
         $timeCardInfo->setupTimeMinute = round($timeCard['SetupTime'] % 60);
         $timeCardInfo->runTimeHour = round($timeCard['RunTime'] / 60);
         $timeCardInfo->runTimeMinute = round($timeCard['RunTime'] % 60);
         $timeCardInfo->panCount = $timeCard['PanCount'];
         $timeCardInfo->partsCount = $timeCard['PartsCount'];
         $timeCardInfo->scrapCount = $timeCard['ScrapCount'];
         $timeCardInfo->comments = $timeCard['Comments'];
         
         viewTimeCardPage($timeCardInfo);
      }
   }
}
   

function viewTimeCardPage($timeCardInfo)
{
   $date = $timeCardInfo->date ? $timeCardInfo->date : date('Y-m-d');
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
   <form action="timeCard.php" method="POST">
   
     <input type="hidden" name="action" value="update_time_card"/>
     <input type="hidden" name="timeCardId" value="$timeCardInfo->timeCardId"/>
     
     Date:<br>
     <input name="date" type="date" value="$date">
     <br>
     
     Name:<br>
     <input type="text" name="name" value="$name" disabled>
     <br>
     
     Employee #:<br>
     <input name="employeeNumber" type="text" value="$timeCardInfo->employeeNumber">
     <br>
     
     Work Center #:<br>
     <input name="wcNumber" type="text" value="$timeCardInfo->wcNumber">
     <br>
     
     Job #:<br>
     <input name="jobNumber" type="text" value="$timeCardInfo->jobNumber">
     <br>
     
     Setup time (hours):<br>
     <input name="setupTimeHour" type="number" min="0" max="10" value="$timeCardInfo->setupTimeHour">
     <input name="setupTimeMinute" type="number" min="0" max="45" value="$timeCardInfo->setupTimeMinute">
     <br>
     
     Run time (hours):<br>
     <input name="runTimeHour" type="number" min="0" max="10" value="$timeCardInfo->runTimeHour">
     <input name="runTimeMinute" type="number" min="0" max="45" value="$timeCardInfo->runTimeMinute">
     <br>
     
      Pan count:<br>
      <input name="panCount" type="number" id="panCount-input" min="1" max="30" value="$timeCardInfo->panCount">
      <br>
      
      Good part count:<br>
      <input name="partsCount" type="number" min="1" max="10000" value="$timeCardInfo->partsCount">
      <br>
      
      Scrap part count:<br>
      <input name="scrapCount" type="number" min="1" max="10000" value="$timeCardInfo->scrapCount">
      <br>
      
      Comments:<br>
      <input name="comments" type="text" name="comments" value="">
      <br>
      
      <br><br>
      <input type="submit" value="Submit">
   </form>
HEREDOC;
}

?>