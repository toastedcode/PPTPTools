<?php
require_once '../common/database.php';

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
      <form id="input-form" method="POST"></form>

      <div class="flex-vertical content">

         <div class="heading">Enter the Heat Number</div>

         <div class="description">The heat number (sometimes called the material number) can also be found by referring to your Job Sheet.</div>

         <div class="flex-horizontal inner-content">
         
            <div class="flex-horizontal" style="margin-right:150px;">$materialNumberInput</div>
         
            <div class="flex-horizontal hide-on-tablet">$keypad</div>

         </div>
         
         $navBar
         
      </div>
      
      <script type="text/javascript">
         var keypad = new Keypad();
         keypad.onEnter = "submitForm('input-form', 'timeCard.php', 'enter_time', 'update_time_card_info')";
         keypad.init();

         document.getElementById("material-number-input").focus();
         
         var validator = new IntValidator("material-number-input", 4, 1, 9999, false);
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
      $navBar->cancelButton("submitForm('input-form', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
      $navBar->backButton("if (validateMaterialNumber()){submitForm('input-form', 'timeCard.php', 'select_job', 'update_time_card_info');}");
      $navBar->nextButton("if (validateMaterialNumber()){submitForm('input-form', 'timeCard.php', 'enter_time', 'update_time_card_info');}");
      $navBar->end();
      
      return ($navBar->getHtml());
   }

   private static function materialNumberInput()
   {
      $materialNumber = EnterMaterialNumber::getMaterialNumber();
      
      $html = 
<<<HEREDOC
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
         <input id="material-number-input" type="number" form="input-form" class="mdl-textfield__input keypadInputCapable large-text-input" name="materialNumber" oninput="this.validator.validate()" value="$materialNumber">
         <label class="mdl-textfield__label" for="material-number-input">Heat #</label>
      </div>
HEREDOC;

      return ($html);
   }
   
   private static function getMaterialNumber()
   {
      $materialNumber = null;
      
      if (isset($_SESSION['timeCardInfo']))
      {
         $materialNumber= $_SESSION['timeCardInfo']->materialNumber;
      }
      
      return ($materialNumber);
   }
}
?>