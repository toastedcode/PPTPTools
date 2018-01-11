<?php
require_once '../database.php';

class SelectMaterialNumber
{
   public static function getHtml()
   {
      $html = "";
      
      $timeCards = "TODO";  // SelectMaterialNumber::timeCards();
      
      $navBar = SelectMaterialNumber::navBar();
      
      $html =
<<<HEREDOC
      <form id="panTicketForm" action="panTicket.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Select Material Number</div>
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
      echo (SelectMaterialNumber::getHtml());
   }
   
   private static function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('panTicketForm', 'panTicket.php', 'view_pan_tickets', 'cancel_pan_ticket')");
      $navBar->backButton("submitForm('panTicketForm', 'panTicket.php', 'select_part_number', 'update_time_card_info')");
      $navBar->nextButton("submitForm('panTicketForm', 'panTicket.php', 'select_material_number', 'update_pan_ticket_info')");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function getMaterialNumber()
   {
      $materialNumber = null;
      
      if (isset($_SESSION['panTicketInfo']))
      {
         $materialNumber= $_SESSION['panTicketInfo']->materialNumber;
      }
      
      return ($materialNumber);
   }
}
?>