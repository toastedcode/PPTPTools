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
      <form id="partWasherForm" action="#" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Select Pan Ticket</div>
         <div class="flex-horizontal content-div" style="align-items:stretch;">
         
            <div class="flex-vertical" style="flex-grow:1;">
               <div class="flex-horizontal" style="flex-grow:1; flex-shrink: 0;">$panTicketIdInput</div>
               <div id="pan-ticket-div" style="flex-grow:1; flex-shrink: 0; width:350px;"></div>
            </div>
            
            <div class="flex-horizontal" style="flex-grow:1">$keypad</div>
            
         </div>
         
         $navBar
         
      </div>
      
      <script type="text/javascript">
         var keypad = new Keypad();
         keypad.onEnter = "if (validatePanTicketId()){submitForm('partWasherForm', 'partWasherLog.php', 'enter_weight', 'update_pan_ticket');}";
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
      $navBar->cancelButton("submitForm('partWasherForm', 'partWasherLog.php', 'view_part_washer_log', 'cancel_part_washer_entry')");
      $navBar->backButton("submitForm('partWasherForm', 'partWasherLog.php', 'select_operator', 'update_time_card_info')");
      $navBar->nextButton("if (validatePanTicketId()){submitForm('partWasherForm', 'partWasherLog.php', 'enter_part_count', 'update_part_washer_entry');}");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function panTicketIdInput()
   {
      $panTicketId = SelectPanTicket::getPanTicketId();
      
      $html =
      <<<HEREDOC
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
         <input id="pan-ticket-id-input" form="partWasherForm" class="mdl-textfield__input keypadInputCapable large-text-input" name="panTicketId" oninput="this.validator.validate()" value="$panTicketId">
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