<?php
require_once '../database.php';

class ViewTimeCard
{
   public static function getHtml()
   {
      $html = "";
      
      $timeCardInfo = ViewTimeCard::getTimeCardInfo();
      
      $dateDiv = ViewTimeCard::dateDiv($timeCardInfo);
      $operatorDiv = ViewTimeCard::operatorDiv($timeCardInfo);
      $jobDiv = ViewTimeCard::jobDiv($timeCardInfo);
      $timeDiv = ViewTimeCard::timeDiv($timeCardInfo);
      $partsDiv = ViewTimeCard::partsDiv($timeCardInfo);
      $commentsDiv = ViewTimeCard::commentsDiv($timeCardInfo);
      
      $navBar = ViewTimeCard::navBar();
      
      $html =
<<<HEREDOC
      <style>
         .label-div {
            width: 100px;
         }

         .section-header-div {
            align-self: center;
         }

         .time-card-table-row {
            margin-bottom: 5px;
         }

         .time-card-div h1 {
            font-weight:bold;
            font-size: 40px;
            margin: 0px 0px 0px 0px;
            display: flex;
         }

         .time-card-div h2 {
            font-weight:bold;
            font-size: 18px;
            margin: 0px 0px 0px 0px;
            display: flex;
         }

         .time-card-div h3 {
            font-weight:bold;
            font-size: 14px;
            margin: 0px 0px 0px 0px;
            display: flex;
         }

         .time-card-div .comments-input {
            width:700px;
            rows: 10;
 
      </style>

      <form id="timeCardForm" action="timeCard.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">View Time Card</div>

         <div class="flex-vertical content-div">
         <div class="flex-vertical time-card-div" style="width:700px; align-items: stretch;">
            <div class="flex-horizontal" style="align-items: flex-start;">
               <div style="flex-basis: 50%; align-items: flex-start;"><h1>Time Card</h1></div>
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
   
   private static function dateDiv($timeCardInfo)
   {
      $html = "";
      
      $html .=
<<<HEREDOC
      <div class="flex-horizontal" style="flex-basis: 50%; justify-content: flex-start;">
         <div class="label-div"><h3>Date</h3></div>
         <input type="date" class="medium-text-input" style="width:270px;" value="$timeCardInfo->date"/>
      </div>
HEREDOC;
      return ($html);
   }
   
   private static function operatorDiv($timeCardInfo)
   {
      $name = ViewTimeCard::getOperatorName($timeCardInfo->employeeNumber);
      
      $html = 
<<<HEREDOC
      <div class="flex-vertical" style="flex-basis: 50%; align-items: flex-start;">
         <div class="section-header-div"><h2>Operator</h2></div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Name</h3></div>
            <input type="text" class="medium-text-input" name="name" style="width:200px;" value="$name" disabled>
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Employee #</h3></div>
            <input type="text" class="medium-text-input" name="name" style="width:100px;" value="$timeCardInfo->employeeNumber" disabled>
         </div>
      </div>
HEREDOC;
         
      return ($html);
   }
   
   private static function jobDiv($timeCardInfo)
   {
      $html =
<<<HEREDOC
      <div class="flex-vertical" style="flex-basis: 50%; align-items: flex-start;">
         <div class="section-header-div"><h2>Job</h2></div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Job #</h3></div>
            <input type="text" class="medium-text-input" name="name" style="width:150px;" value="$timeCardInfo->jobNumber">
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Work center #</h3></div>
            <input type="text" class="medium-text-input" name="name" style="width:150px;" value="$timeCardInfo->wcNumber">
         </div>
      </div>
HEREDOC;
         
      return ($html);
   }
   
   private static function timeDiv($timeCardInfo)
   {
      $html =
<<<HEREDOC
      <div class="flex-vertical" style="flex-basis: 50%; align-items: flex-start;">
         <div class="section-header-div"><h2>Time</h2></div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Setup time</h3></div>
            <input type="number" class="medium-text-input" min="0" max="10" style="width:50px;" value="$timeCardInfo->setupTimeHour">
            <div style="padding: 5px;">:</div>
            <input type="number" class="medium-text-input" min="0" max="45" style="width:50px;" value="$timeCardInfo->setupTimeMinute">
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Run time</h3></div>
            <input type="number" class="medium-text-input" min="0" max="10" style="width:50px;" value="$timeCardInfo->runTimeHour">
            <div style="padding: 5px;">:</div>
            <input type="number" class="medium-text-input" min="0" max="45" style="width:50px;" value="$timeCardInfo->runTimeMinute">
         </div>
      </div>
HEREDOC;
         
      return ($html);
   }
   
   private static function partsDiv($timeCardInfo)
   {
      $html =
<<<HEREDOC
      <div class="flex-vertical" style="flex-basis: 50%; align-items: flex-start;">
         <div class="section-header-div"><h2>Part Counts</h2></div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Pan count</h3></div>
            <input type="text" class="medium-text-input" name="name" style="width:100px;" value="$timeCardInfo->panCount">
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Good count</h3></div>
            <input type="text" class="medium-text-input" name="name" style="width:100px;" value="$timeCardInfo->partsCount">
         </div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Scrap count</h3></div>
            <input type="text" class="medium-text-input" name="name" style="width:100px;" value="$timeCardInfo->scrapCount">
         </div>
      </div>
HEREDOC;
         
      return ($html);
   }
   
   private static function commentsDiv($timeCardInfo)
   {
      $html =
<<<HEREDOC
      <div class="flex-horizontal">
         <textarea form="timeCardForm" class="comments-input" type="text" name="comments" rows="4" placeholder="Enter comments ...">$timeCardInfo->comments</textarea>
      </div>
HEREDOC;
      
      return ($html);
   }
   
   private static function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      
      // Case 1
      
      $navBar->cancelButton("submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
      $navBar->backButton("submitForm('timeCardForm', 'timeCard.php', 'enter_comments', 'update_time_card_info');");
      $navBar->highlightNavButton("Save", "submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'save_time_card');", false);

      // Case 2
      //$navBar->navButton("Ok", "submitForm('timeCardForm', 'timeCard.php', '', 'view_time_cards');};", false);
  
      // Case 3
      //$navBar->cancelButton("submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
      //$navBar->highlightNavButton("Save", "if (validateTime()){submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'save_time_card');};", false);
      
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function getTimeCardInfo()
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
   
   private static function getOperatorName($employeeNumber)
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