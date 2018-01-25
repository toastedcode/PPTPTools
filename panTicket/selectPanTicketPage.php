<?php
require_once '../database.php';

class SelectPanTicket
{
   public static function getHtml()
   {
      $html = "";
      
      $panTicketIdInput = SelectPanTicket::panTicketIdInput();
      
      $keypad = Keypad::getHtml();
      
      $navBar = SelectPanTicket::navBar();
      
      $html =
      <<<HEREDOC
      <form id="panTicketForm" action="panTicket.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Select Pan Ticket</div>
         <div class="flex-horizontal content-div">
         
            <div class="flex-horizontal" style="flex-grow: 1">$panTicketIdInput</div>
            
            <div class="flex-horizontal" style="flex-grow: 1">$keypad</div>
            <script type="text/javascript">initKeypad()</script>
            
         </div>
         
         $navBar
         
      </div>
      
      <script type="text/javascript">
         initKeypad();
         document.getElementById("pan-ticket-id-input").focus();
         
         var validator = new IntValidator("pan-ticket-id-input", 5, 1, 10000, false);
         validator.init();
      </script>
HEREDOC;
      
      return ($html);
   }
   
   public static function render()
   {
      echo (SelectPanTicket::getHtml());
   }
   
   private static function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('panTicketForm', 'panTicket.php', 'view_pan_tickets', 'cancel_pan_ticket')");
      $navBar->nextButton("submitForm('panTicketForm', 'panTicket.php', 'enter_weight', 'edit_pan_ticket')");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function panTicketIdInput()
   {
      $panTicketId = SelectPanTicket::getPanTicketId();
      
      $html =
      <<<HEREDOC
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
         <input id="pan-ticket-id-input" form="panTicketForm" class="mdl-textfield__input keypadInputCapable large-text-input" name="panTicketId" oninput="this.validator.validate()" value="$panTicketId">
         <label class="mdl-textfield__label" for="pan-ticket-id-input">Pan ticket #</label>
      </div>
HEREDOC;
      
      return ($html);
   }
   
   private static function getPanTicketId()
   {
      $panTicketId = null;
      
      if (isset($_POST['panTicketId']))
      {
         $panTicketId= $_POST['panTicketId'];
      }
      
      return ($panTicketId);
   }
}
?>