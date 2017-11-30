<?php
class Navigation 
{
   public static function start()
   {
      echo "<div class=\"nav-div\">";
   }
   
   public static function end()
   {
      echo "</div>";
   }
   
   public static function navButton($text, $onClick)
   {
      echo
<<<HEREDOC
      <button class="nav-button" onclick="$onClick">
         $text
      </button>
HEREDOC;
   }
   
   public static function highlightNavButton($text, $onClick)
   {
      echo
<<<HEREDOC
      <button class="nav-button nav-button-highlight" onclick="$onClick">
         $text
      </button>
HEREDOC;
   }
   
   public static function cancelButton($onClick)
   {
      echo
<<<HEREDOC
      <button class="nav-button" onclick="$onClick">
         Cancel
      </button>
HEREDOC;
   }
   
   public static function backButton($onClick)
   {
      echo
<<<HEREDOC
      <button class="nav-button" onclick="$onClick">
         <i class="material-icons">arrow_back</i>
      </button>
HEREDOC;
   }
   
   public static function nextButton($onClick)
   {
      echo
<<<HEREDOC
      <button class="nav-button nav-button-highlight" onclick="$onClick">
         <i class="material-icons">arrow_forward</i>
      </button>
HEREDOC;
   }
}