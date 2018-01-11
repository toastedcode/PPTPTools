<?php
require_once '../database.php';

class SelectPartNumber
{
   public static function getHtml()
   {
      $html = "";
      
      $timeCards = "TODO";  // SelectPartNumber::timeCards();
      
      $navBar = SelectPartNumber::navBar();
      
      $html =
<<<HEREDOC
      <form id="panTicketForm" action="panTicket.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Select Part Number</div>
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
      echo (SelectPartNumber::getHtml());
   }
   
   private static function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('panTicketForm', 'panTicket.php', 'view_pan_tickets', 'cancel_pan_ticket')");
      $navBar->backButton("submitForm('panTicketForm', 'panTicket.php', 'select_time_card', 'update_time_card_info')");
      $navBar->nextButton("submitForm('panTicketForm', 'panTicket.php', 'select_material_number', 'update_pan_ticket_info')");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function getPartNumber()
   {
      $partNumber = null;
      
      if (isset($_SESSION['panTicketInfo']))
      {
         $partNumber = $_SESSION['panTicketInfo']->partNumber;
      }
      
      return ($partNumber);
   }
}
?>