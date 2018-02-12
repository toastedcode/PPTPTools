<?php
require_once '../database.php';

class EnterWeight
{
   public static function getHtml()
   {
      $html = "";
      
      $panTicketId = EnterWeight::getPanTicketId();
      
      $weightInput = EnterWeight::weightInput();
      
      $keypad = Keypad::getHtml(true);
      
      $navBar = EnterWeight::navBar();
      
      $html =
      <<<HEREDOC
      <form id="panTicketForm" action="panTicket.php" method="POST">
         <input type="hidden" name="panTicketId" value="$panTicketId"/>
      </form>
      <div class="flex-vertical card-div">
         <div class="card-header-div">Enter Weight</div>
         <div class="flex-horizontal content-div">
         
            <div class="flex-horizontal" style="flex-grow: 1">$weightInput</div>
            
            <div class="flex-horizontal" style="flex-grow: 1">$keypad</div>
            
         </div>
         
         $navBar
         
      </div>
      
      <script type="text/javascript">
         var keypad = new Keypad();
         keypad.onEnter = "submitForm('panTicketForm', 'panTicket.php', 'verify_weight', 'update_pan_ticket')";
         keypad.init();

         document.getElementById("weight-input").focus();
         
         var validator = new IntValidator("weight-input", 7, 1, 10000, false);
         validator.init();
      </script>
HEREDOC;
      
      return ($html);
   }
   
   public static function render()
   {
      echo (EnterWeight::getHtml());
   }
   
   private static function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('panTicketForm', 'panTicket.php', 'view_pan_tickets', 'cancel_pan_ticket')");
      $navBar->nextButton("submitForm('panTicketForm', 'panTicket.php', 'verify_weight', 'update_pan_ticket')");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function weightInput()
   {
      $html = "";
      
      $panTicketId = EnterWeight::getPanTicketId();
      
      if ($panTicketId)
      {
         $weight = EnterWeight::getWeight($panTicketId);
         
         $html =
<<<HEREDOC
         <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
            <input id="weight-input" form="panTicketForm" class="mdl-textfield__input keypadInputCapable large-text-input" name="weight" oninput="this.validator.validate()" value="$weight">
            <label class="mdl-textfield__label" for="pan-ticket-id-input">Weight</label>
         </div>
HEREDOC;
      }
      
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
   
   private static function getWeight($panTicketId)
   {
      $panTicketInfo = getPanTicketInfo($panTicketId);
      
      $weight = $panTicketInfo ? $panTicketInfo->weight : 0;
      
      return ($weight);
   }
}
?>