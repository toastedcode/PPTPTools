<?php
require_once '../database.php';

class SelectPanTicket
{
   public static function getHtml()
   {
      $html = "";
      
      $panTicketIdInput = SelectPanTicket::panTicketIdInput();
      
      $keypad = Keypad::getHtml(false);
      
      $navBar = SelectPanTicket::navBar();
      
      $html =
      <<<HEREDOC
      <form id="panTicketForm" action="panTicket.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Select Pan Ticket</div>
         <div class="flex-horizontal content-div">
         
            <div class="flex-vertical" style="flex-grow: 1">
               <div class="flex-horizontal">$panTicketIdInput</div>
               <div id="pan-ticket-div"></div>
            </div>
            
            <div class="flex-horizontal" style="flex-grow: 1">$keypad</div>
            
         </div>
         
         $navBar
         
      </div>
      
      <script type="text/javascript">
         var keypad = new Keypad();
         keypad.onEnter = "if (validatePanTicketId()){submitForm('panTicketForm', 'panTicket.php', 'verify_weight', 'update_pan_ticket');}";
         keypad.init();

         document.getElementById("pan-ticket-id-input").focus();
         
         var validator = new PanTicketIdValidator("pan-ticket-id-input");
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
      $navBar->nextButton("if (validatePanTicketId()){submitForm('panTicketForm', 'panTicket.php', 'enter_weight', 'edit_pan_ticket');}");
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