<?php
require_once '../database.php';

class EnterPartNumber
{
   public static function getHtml()
   {
      $html = "";
      
      $partNumberInput = EnterPartNumber::partNumberInput();
      
      $keypad = Keypad::getHtml();
      
      $navBar = EnterPartNumber::navBar();
      
      $html =
      <<<HEREDOC
      <form id="panTicketForm" action="panTicket.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Enter Part Number</div>
         <div class="flex-horizontal content-div">
         
            <div class="flex-horizontal" style="flex-grow: 1">$partNumberInput</div>
            
            <div class="flex-horizontal" style="flex-grow: 1">$keypad</div>
            <script type="text/javascript">initKeypad()</script>
            
         </div>
         
         $navBar
         
      </div>
      
      <script type="text/javascript">
         initKeypad();
         document.getElementById("part-number-input").focus();
         
         var validator = new IntValidator("part-number-input", 5, 1, 10000, false);
         validator.init();
      </script>
HEREDOC;
      
      return ($html);
   }
   
   public static function render()
   {
      echo (EnterPartNumber::getHtml());
   }
   
   private static function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('panTicketForm', 'panTicket.php', 'view_pan_tickets', 'cancel_pan_ticket')");
      $navBar->backButton("submitForm('panTicketForm', 'panTicket.php', 'select_time_card', 'update_time_card_info')");
      $navBar->nextButton("submitForm('panTicketForm', 'panTicket.php', 'enter_material_number', 'update_pan_ticket_info')");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function partNumberInput()
   {
      $partNumber = EnterPartNumber::getPartNumber();
      
      $html =
      <<<HEREDOC
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
         <input id="part-number-input" form="panTicketForm" class="mdl-textfield__input keypadInputCapable large-text-input" name="partNumber" oninput="this.validator.validate()" value="$partNumber">
         <label class="mdl-textfield__label" for="part-number-input">Part #</label>
      </div>
HEREDOC;
      
      return ($html);
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