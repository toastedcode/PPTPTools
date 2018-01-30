<?php

class Keypad
{
   public static function getHtml()
   {
      $html = 
<<<HEREDOC
      <head>
         <link rel="stylesheet" type="text/css" href="keypad.css">
         <script type="text/javascript" src="keypad.js"></script>
      </head>
   
      <div class="keypad">
         <div class="keypadKey">7</div>
         <div class="keypadKey">8</div>
         <div class="keypadKey">9</div>
         <div class="keypadKey">4</div>
         <div class="keypadKey">5</div>
         <div class="keypadKey">6</div>
         <div class="keypadKey">1</div>
         <div class="keypadKey">2</div>
         <div class="keypadKey">3</div>
         <div class="keypadKey">0</div>
         <div class="keypadKey">Bksp</div>
         <div class="keypadKey">Clr</div>
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