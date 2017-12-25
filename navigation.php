<?php
class Navigation 
{
   public function getHtml()
   {
      return ($this->html);
   }
   
   public function render()
   {
      echo ($this->getHtml());
   }
   
   public function start()
   {
      $this->html = "<div class=\"nav-div\">";
   }
   
   public function end()
   {
      $this->html .= "</div>";
   }
   
   public function navButton($text, $onClick, $wide)
   {
      $wideClass = $wide ? "nav-button-wide" : "";
      
      $this->html .=
<<<HEREDOC
      <button class="nav-button $wideClass" onclick="$onClick">
         $text
      </button>
HEREDOC;
   }
   
   public function highlightNavButton($text, $onClick, $wide)
   {
      $wideClass = $wide ? "nav-button-wide" : "";
      
      $this->html .=
<<<HEREDOC
      <button class="nav-button nav-button-highlight $wideClass" onclick="$onClick">
         $text
      </button>
HEREDOC;
   }
      
   public function mainMenuButton()
   {
      $this->html .=
      <<<HEREDOC
      <button class="nav-button nav-button-wide" onclick="location.href='../home.php'">
         Main Menu
      </button>
HEREDOC;
   }
   
   public function cancelButton($onClick)
   {
      $this->html .=
<<<HEREDOC
      <button class="nav-button" onclick="$onClick">
         Cancel
      </button>
HEREDOC;
   }
   
   public function backButton($onClick)
   {
      $this->html .=
<<<HEREDOC
      <button class="nav-button" onclick="$onClick">
         <i class="material-icons">arrow_back</i>
      </button>
HEREDOC;
   }
   
   public function nextButton($onClick)
   {
      $this->html .=
<<<HEREDOC
      <button class="nav-button nav-button-highlight" onclick="$onClick">
         <i class="material-icons">arrow_forward</i>
      </button>
HEREDOC;
   }
      
   public function printButton($onClick)
   {
      $this->html .=
      <<<HEREDOC
      <button class="nav-button" onclick="$onClick">
         <i class="material-icons">print</i>
      </button>
HEREDOC;
   }
      
   private $html = "";
}