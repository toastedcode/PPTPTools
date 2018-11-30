<?php
require_once '../common/database.php';

class EnterPartCount
{
   public static function getHtml()
   {
      $html = "";
      
      $partCountInput = EnterPartCount::partCountInput();
      
      $keypad = Keypad::getHtml(false);
      
      $navBar = EnterPartCount::navBar();
      
      $html =
<<<HEREDOC
      <form id="timeCardForm" action="#" method="POST"></form>

      <div class="flex-vertical content">

         <div class="heading">Enter Part Counts</div>

         <div class="description">Record the number of baskets, good parts, and scrap parts, that were made during this run.</div>

        <div class="flex-horizontal">

            <div class="flex-vertical" style="justify-content: space-evenly; flex-grow: 1">$partCountInput</div>
         
            <div class="flex-horizontal hide-on-tablet" style="flex-grow: 1">$keypad</div>

        </div>
         
         $navBar
         
      </div>

      <script type="text/javascript">
         var keypad = new Keypad();
         keypad.onEnter = "if (validatePartCount()){submitForm('timeCardForm', 'timeCard.php', 'enter_comments', 'update_time_card_info');};";
         keypad.init();

         document.getElementById("panCount-input").focus();

         var panCountValidator = new IntValidator("panCount-input", 2, 2, 40, false);
         var partsCountValidator = new IntValidator("partsCount-input", 6, 0, 100000, false);
         var scrapCountValidator = new IntValidator("scrapCount-input", 6, 0, 100000, false);

         panCountValidator.init();
         partsCountValidator.init();
         scrapCountValidator.init();
      </script>
HEREDOC;
      
      return ($html);
   }
   
   public static function render()
   {
      echo (EnterPartCount::getHtml());
   }
   
   private static function partCountInput()
   {
      $timeCardInfo = EnterPartCount::getTimeCardInfo();
      
      $panCount = $timeCardInfo->panCount;
      $partCount = $timeCardInfo->partCount;
      $scrapCount = $timeCardInfo->scrapCount;
      
      $html =
<<<HEREDOC
      <!-- Pan count -->
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
         <input id="panCount-input" form="timeCardForm" class="mdl-textfield__input keypadInputCapable large-text-input" type="number" name="panCount" oninput="this.validator.validate()" value="$panCount">
         <label class="mdl-textfield__label" for="panCount-input">Basket count</label>
      </div>

      <!-- Parts count -->
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
         <input id="partsCount-input" form="timeCardForm" class="mdl-textfield__input keypadInputCapable large-text-input" type="number" name="partCount" oninput="this.validator.validate()" value="$partCount">
         <label class="mdl-textfield__label" for="partsCount-input">Good part count</label>
      </div>

      <!-- Scrap count -->
      <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
         <input id="scrapCount-input" form="timeCardForm" class="mdl-textfield__input keypadInputCapable large-text-input" type="number" name="scrapCount" oninput="this.validator.validate()" value="$scrapCount">
         <label class="mdl-textfield__label" for="scrapCount-input">Scrap part count</label>
      </div>
HEREDOC;
      
      return ($html);
   }
   
   private static function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->cancelButton("submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
      $navBar->backButton("if (validatePartCount()){submitForm('timeCardForm', 'timeCard.php', 'enter_time', 'update_time_card_info');};");
      $navBar->nextButton("if (validatePartCount()){submitForm('timeCardForm', 'timeCard.php', 'enter_comments', 'update_time_card_info');};");
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private static function getTimeCardInfo()
   {
      $timeCardInfo = new TimeCardInfo();
      
      if (isset($_SESSION['timeCardInfo']))
      {
         $timeCardInfo = $_SESSION['timeCardInfo'];
      }
      
      return ($timeCardInfo);
   }
}
?>