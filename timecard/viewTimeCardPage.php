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
      <form id="timeCardForm" action="timeCard.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">View Time Card</div>
         <div class="flex-vertical content-div" style="justify-content: space-evenly">
            <div class="flex-horizontal">
               <div>Time Card</div>
               $dateDiv
            </div>
            <div class="flex-horizontal">
               <div class="flex-vertical" style="justify-content: space-evenly">
                  $operatorDiv
                  $jobDiv
               </div>
               <div class="flex-vertical" style="justify-content: space-evenly">
                  $timeDiv
                  $partsDiv
               </div>
            </div>
            $commentsDiv
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
      <div>
         Date
         <input type="date" class="medium-text-input" style="width:250px;" value="$timeCardInfo->date"/>
      </div>
HEREDOC;
      return ($html);
   }
   
   private static function operatorDiv($timeCardInfo)
   {
      $name = ViewTimeCard::getOperatorName($timeCardInfo->employeeNumber);
      
      $html = 
<<<HEREDOC
      <div class="flex-vertical">
         <div>Operator</div>
         <div class="flex-horizontal">
            Name
            <input type="text" class="medium-text-input" name="name" style="width:300px;" value="$name" disabled>
         </div>
         <div class="flex-horizontal">
            Employee #
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
      <div class="flex-vertical">
         <div>Job</div>
         <div class="flex-horizontal">
            Job #
            <input type="text" class="medium-text-input" name="name" style="width:150px;" value="$timeCardInfo->jobNumber">
         </div>
         <div class="flex-horizontal">
            Work center #
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
      <div class="flex-vertical">
         <div>Time</div>
         <div class="flex-horizontal">
            Setup time
            <input type="number" class="medium-text-input" min="0" max="10" style="width:50px;" value="$timeCardInfo->setupTimeHour">
            <input type="number" class="medium-text-input" min="0" max="45" style="width:50px;" value="$timeCardInfo->setupTimeMinute">
         </div>
         <div class="flex-horizontal">
            Run time
            <input type="number" class="medium-text-input" min="0" max="10" style="width:50px;" value="$timeCardInfo->runTimeHour">
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
      <div class="flex-vertical">
         <div>Part Counts</div>
         <div class="flex-horizontal">
            Pan count
            <input type="text" class="medium-text-input" name="name" style="width:100px;" value="$timeCardInfo->panCount">
         </div>
         <div class="flex-horizontal">
            Good count
            <input type="text" class="medium-text-input" name="name" style="width:100px;" value="$timeCardInfo->partsCount">
         </div>
         <div class="flex-horizontal">
            Scrap count
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
      <div>
         <textarea form="timeCardForm" class="comments-input" type="text" name="comments" rows="10" placeholder="Enter comments ...">$timeCardInfo->comments</textarea>
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