<?php
class Header
{
   public static function render($pageTitle)
   {
      echo 
<<<HEREDOC
      <div class="header-div">
         <span class="page-title">$pageTitle</span>
         <a class="nav-link" href="../pptpTools.php?action=logout">Logout</a>
      </div>
HEREDOC;
   }
}
?>