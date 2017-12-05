<?php
require_once '../database.php';
require_once 'timeCardInfo.php';
require_once 'navigation.php';

class ViewTimeCard
{
   public static function getHtml()
   {
      $html = "";
      
      $timeCardInfo = ViewTimeCard::getTimeCardInfo();
      
      $titleDiv = ViewTimeCard::titleDiv();
      $dateDiv = ViewTimeCard::dateDiv($timeCardInfo);
      $operatorDiv = ViewTimeCard::operatorDiv($timeCardInfo);
      $jobDiv = ViewTimeCard::jobDiv($timeCardInfo);
      $timeDiv = ViewTimeCard::timeDiv($timeCardInfo);
      $partsDiv = ViewTimeCard::partsDiv($timeCardInfo);
      $commentsDiv = ViewTimeCard::commentsDiv($timeCardInfo);
      
      $navBar = ViewTimeCard::navBar($timeCardInfo);
      
      $html =
<<<HEREDOC
      <form id="timeCardForm" action="timeCard.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">View Time Card</div>

         <div class="flex-vertical content-div">
         <div class="flex-vertical time-card-div">
            <div class="flex-horizontal">
               $titleDiv
               $dateDiv
            </div>
            <div class="flex-horizontal" style="align-items: flex-start;">
               $operatorDiv
               $timeDiv
            </div>
            <div class="flex-horizontal" style="align-items: flex-start;">
               $jobDiv
               $partsDiv
            </div>
            $commentsDiv
         </div>
         </div>
         
         $navBar
         
      </div>
HEREDOC;
      
      return ($html);
   }
   
   public static function render()
   {
      echo (ViewTimeCard::getHtml());
   }
   
   protected static function titleDiv()
   {
      $html =
<<<HEREDOC
      <div class="flex-horizontal time-card-table-col">
         <h1>Time Card</h1>
      </div>
HEREDOC;

      return ($html);
   }
   
   protected static function dateDiv($timeCardInfo)
   {
      $html =
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Date</h3></div>
            <input type="date" class="medium-text-input" form="timeCardForm" name="date" style="width:180px;" value="$timeCardInfo->date"/>
         </div>
      </div>
HEREDOC;
      return ($html);
   }
   
   protected static function operatorDiv($timeCardInfo)
   {
      $name = ViewTimeCard::getOperatorName($timeCardInfo->employeeNumber);
      
      $html = 
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">
         <div class="section-header-div"><h2>Operator</h2></div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Name</h3></div>
            <input type="text" class="medium-text-input" form="timeCardForm" name="employeeName" style="width:200px;" value="$name" disabled>
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Employee #</h3></div>
            <input type="text" class="medium-text-input" form="timeCardForm" name="employeeNumber" style="width:100px;" value="$timeCardInfo->employeeNumber" disabled>
         </div>
      </div>
HEREDOC;
         
      return ($html);
   }
   
   protected static function jobDiv($timeCardInfo)
   {
      $html =
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">
         <div class="section-header-div"><h2>Job</h2></div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Job #</h3></div>
            <input type="text" class="medium-text-input" form="timeCardForm" name="jobNumber" style="width:150px;" value="$timeCardInfo->jobNumber">
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Work center #</h3></div>
            <input type="text" class="medium-text-input" form="timeCardForm" name="wcNumber" style="width:150px;" value="$timeCardInfo->wcNumber">
         </div>
      </div>
HEREDOC;
         
      return ($html);
   }
   
   protected static function timeDiv($timeCardInfo)
   {
      $html =
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">
         <div class="section-header-div"><h2>Time</h2></div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Setup time</h3></div>
            <input type="number" class="medium-text-input" form="timeCardForm" name="setupTimeHour" min="0" max="10" style="width:50px;" value="$timeCardInfo->setupTimeHour">
            <div style="padding: 5px;">:</div>
            <input type="number" class="medium-text-input" form="timeCardForm" name="setupTimeMinute" min="0" max="45" style="width:50px;" value="$timeCardInfo->setupTimeMinute">
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Run time</h3></div>
            <input type="number" class="medium-text-input" form="timeCardForm" name="runTimeHour"min="0" max="10" style="width:50px;" value="$timeCardInfo->runTimeHour">
            <div style="padding: 5px;">:</div>
            <input type="number" class="medium-text-input" form="timeCardForm" name="runTimeMinute" min="0" max="45" style="width:50px;" value="$timeCardInfo->runTimeMinute">
         </div>
      </div>
HEREDOC;
         
      return ($html);
   }
   
   protected static function partsDiv($timeCardInfo)
   {
      $html =
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">
         <div class="section-header-div"><h2>Part Counts</h2></div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Pan count</h3></div>
            <input type="text" class="medium-text-input" form="timeCardForm" name="panCount" style="width:100px;" value="$timeCardInfo->panCount">
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Good count</h3></div>
            <input type="text" class="medium-text-input" form="timeCardForm" name="partsCount" style="width:100px;" value="$timeCardInfo->partsCount">
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Scrap count</h3></div>
            <input type="text" class="medium-text-input" form="timeCardForm" name="scrapCount" style="width:100px;" value="$timeCardInfo->scrapCount">
         </div>
      </div>
HEREDOC;
         
      return ($html);
   }
   
   protected static function commentsDiv($timeCardInfo)
   {
      $html =
<<<HEREDOC
      <div class="flex-horizontal">
         <textarea form="timeCardForm" class="comments-input" type="text" form="timeCardForm" name="comments" rows="4" placeholder="Enter comments ...">$timeCardInfo->comments</textarea>
      </div>
HEREDOC;
      
      return ($html);
   }
   
   protected static function navBar($timeCardInfo)
   {
      $navBar = new Navigation();
      
      $navBar->start();
      
      if ($timeCardInfo->timeCardId == 0)
      {
         // Case 1
         // Viewing as last step of creating a new time card.
         
         $navBar->cancelButton("submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
         $navBar->backButton("submitForm('timeCardForm', 'timeCard.php', 'enter_comments', 'update_time_card_info');");
         $navBar->highlightNavButton("Save", "submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'save_time_card');", false);
      }
      else
      {
         // Case 2
         // Viewing single time card selected from table of time cards.
         $navBar->printButton("onPrint($timeCardInfo->timeCardId)");
         $navBar->highlightNavButton("Ok", "submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'no_action')", false);
     
         // Case 3
         // Editing a single time card selected from table of time cards.
         //$navBar->cancelButton("submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
         //$navBar->printButton("onPrint($timeCardInfo->timeCardId)");
         //$navBar->highlightNavButton("Save", "if (validateTime()){submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'save_time_card');};", false);
      }
      
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   protected static function getTimeCardInfo()
   {
      $timeCardInfo = new TimeCardInfo();
      
      if (isset($_POST['timeCardId']))
      {
         $timeCardInfo = getTimeCardInfo($_POST['timeCardId']);
      }
      else if (isset($_SESSION['timeCardInfo']))
      {
         $timeCardInfo = $_SESSION['timeCardInfo'];
      }
      
      return ($timeCardInfo);
   }
   
   protected static function getOperatorName($employeeNumber)
   {
      $name = "";
      
      $database = new PPTPDatabase("localhost", "root", "", "pptp");
      
      $database->connect();
      
      if ($database->isConnected())
      {
         if ($operator = $database->getOperator($employeeNumber))
         {
            $name = $operator["FirstName"] . " " . $operator["LastName"];
         }
      }
      
      return ($name);
   }
}
?>