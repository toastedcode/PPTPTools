<?php

require_once 'root.php';

class Keypad
{
   public static function getHtml($decimal)
   {
      global $ROOT;
      
      $disabled = $decimal ? "" : "disabled";
      
      $html = 
<<<HEREDOC
      <head>
         <link rel="stylesheet" type="text/css" href="$ROOT/common/keypad.css">
         <script type="text/javascript" src="$ROOT/common/keypad.js"></script>
      </head>
   
      <div class="keypad">
         <div class="keypad-col">
            <div class="keypad-row">
               <div class="keypad-key">7</div>
               <div class="keypad-key">8</div>
               <div class="keypad-key">9</div>
            </div>
            <div class="keypad-row">
               <div class="keypad-key">4</div>
               <div class="keypad-key">5</div>
               <div class="keypad-key">6</div>
            </div>
            <div class="keypad-row">
               <div class="keypad-key">1</div>
               <div class="keypad-key">2</div>
               <div class="keypad-key">3</div>
            </div>
            <div class="keypad-row">
               <div class="keypad-key keypad-key-wide">0</div>
               <div class="keypad-key $disabled">.</div>
            </div>
         </div>
         <div class="keypad-col" style="padding-left: 5px;">
            <div class="keypad-key">Bksp</div>
            <div class="keypad-key">Clr</div>
            <div class="keypad-key keypad-key-tall">Enter</div>
         </div>
      </div>
HEREDOC;

      return ($html);
   }
   
   public static function render()
   {
      echo (Keypad::getHtml());
   }
}

?>