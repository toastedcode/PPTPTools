<?php
require_once '../database.php';

class EnterMaterialNumber
{
   public static function getHtml()
   {
      $html = "";
      
      $materialNumberInput = EnterMaterialNumber::materialNumberInput();
      
      $keypad = Keypad::getHtml(false);
      
      $navBar = EnterMaterialNumber::navBar();
      
      $html =
<<<HEREDOC
      <form id="panTicketForm" action="panTicket.php" method="POST"></form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Enter Material Number</div>
         <div class="flex-horizontal content-div">
         
            <div class="flex-horizontal" style="flex-grow: 1">$materialNumberInput</div>
            
            <div class="flex-horizontal" style="flex-grow: 1">$keypad</div>
            
         </div>
         
         $navBar
         
      </div>
      
      <script type="text/javascript">
         var keypad = new Keypad();
         keypad.onEnter = "submitForm('panTicketForm', 'panTicket.php', 'edit_pan_ticket', 'update_pan_ticket_info')";
         keypad.init();

         document.getElementById("material-number-input").focus();
         
         var validator = new IntValidator("material-number-input", 5, 1, 10000, false);
         validator.init();
      </script>
HEREDOC;
   
      return ($html);
   }
   
   public static function render()
   {
      echo (EnterMaterialNumber::getHtml());
   }
   
   private static function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('panTicketForm', 'panTicket.php', 'view_pan_tickets', 'cancel_pan_ticket')");
      $navBar->backButton("submitForm('panTicketForm', 'panTicket.php', 'enter_part_number', 'update_pan_ticket_info')");
      $navBar->nextButton("submitForm('panTicketForm', 'panTicket.php', 'edit_pan_ticket', 'update_pan_ticket_info')");
      $navBar->end();
      
      return ($navBar->getHtml());
   }

   private static function materialNumberInput()
   {
      $materialNumber = EnterMaterialNumber::getMaterialNumber();
      
      $html = 
<<<HEREDOC
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
         <input id="material-number-input" form="panTicketForm" class="mdl-textfield__input keypadInputCapable large-text-input" name="materialNumber" oninput="this.validator.validate()" value="$materialNumber">
         <label class="mdl-textfield__label" for="material-number-input">Heat #</label>
      </div>
HEREDOC;

      return ($html);
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