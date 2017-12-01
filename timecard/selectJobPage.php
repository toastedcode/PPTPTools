<?php
require_once '../database.php';

class SelectJob
{
   public static function getHtml()
   {
      $html = "";
      
      $keypad = Keypad::getHtml();
      
      $navBar = SelectJob::navBar();
      
      $html =
      <<<HEREDOC
      <form id="timeCardForm" action="timeCard.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Enter Job</div>
         <div class="flex-horizontal content-div">
            $keypad
         </div>
         
         $navBar
         
      </div>
HEREDOC;
      
      return ($html);
   }
   
   public static function render()
   {
      echo (SelectJob::getHtml());
   }
   
   private static function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
      $navBar->backButton("if (validateJob()){submitForm('timeCardForm', 'timeCard.php', 'select_work_center', 'update_time_card_info');};");
      $navBar->nextButton("if (validateJob()){submitForm('timeCardForm', 'timeCard.php', 'enter_time', 'update_time_card_info');};");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function getJobNumber()
   {
      $jobNumber = null;
      
      if (isset($_SESSION['timeCardInfo']))
      {
         $jobNumber = $_SESSION['timeCardInfo']->jobNumber;
      }
      
      return ($jobNumber);
   }
}
?>