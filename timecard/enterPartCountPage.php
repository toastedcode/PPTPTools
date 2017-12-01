<?php
require_once '../database.php';

class EnterPartCount
{
   public static function getHtml()
   {
      $html = "";
      
      $navBar = EnterPartCount::navBar();
      
      $html =
<<<HEREDOC
      <form id="timeCardForm" action="timeCard.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Enter Part Count</div>
         <div class="flex-horizontal content-div">

         </div>
         
         $navBar
         
      </div>
HEREDOC;
      
      return ($html);
   }
   
   public static function render()
   {
      echo (EnterPartCount::getHtml());
   }
   
   private static function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
      $navBar->backButton("if (validateJob()){submitForm('timeCardForm', 'timeCard.php', 'select_job', 'update_time_card_info');};");
      $navBar->nextButton("if (validateJob()){submitForm('timeCardForm', 'timeCard.php', 'enter_part_counts', 'update_time_card_info');};");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function getTimeCardInfo()
   {
      $timeCardInfo = new TimeCardInfo();
      
      if (isset($_SESSION['timeCardInfo']))
      {
         $timeCardInfo = $_SESSION['timeCardInfo'];
      }
      
      return ($timeCardInfo);
   }
}
?>