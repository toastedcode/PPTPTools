<?php
require_once '../database.php';

class SelectTimeCard
{
   public static function getHtml()
   {
      $html = "";
      
      $timeCards = "TODO";  // SelectTimeCard::timeCards();
      
      $navBar = SelectTimeCard::navBar();
      
      $html =
<<<HEREDOC
      <form id="panTicketForm" action="panTicket.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Select Time Card</div>
         <div class="flex-horizontal content-div" style="flex-wrap: wrap; align-items: flex-start;">
            $timeCards
         </div>
         $navBar         
      </div>
HEREDOC;
      
      return ($html);
   }
   
   public static function render()
   {
      echo (SelectTimeCard::getHtml());
   }
   
   private static function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('panTicketForm', 'panTicket.php', 'view_pan_tickets', 'cancel_pan_ticket')");
      $navBar->nextButton("submitForm('panTicketForm', 'panTicket.php', 'select_part_number', 'update_pan_ticket_info')");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function getTimeCardId()
   {
      $timeCardId = null;
      
      if (isset($_SESSION['panTicketInfo']))
      {
         $timeCardId = $_SESSION['panTicketInfo']->timeCardId;
      }
      
      return ($timeCardId);
   }
}
?>