<?php
require_once 'authentication.php';
require_once 'root.php';

class Header
{
   public static function render($pageTitle)
   {
      global $ROOT;
         
      echo 
<<<HEREDOC
      <div class="header-div">
         <span class="page-title">$pageTitle</span>
HEREDOC;
         
      if (Authentication::isAuthenticated())
      {
         $username = Authentication::getAuthenticatedUser()->username;
         
         echo
<<<HEREDOC
            <div class="flex-horizontal">
               <i class="material-icons" style="margin-right:5px; color: #ffffff; font-size: 24px;">person</i>
               <div class="nav-username">$username&nbsp | &nbsp</div>
               <a class="nav-link" href="$ROOT/home.php?action=logout">Logout</a>
            </div>
HEREDOC;
      }
            
      echo "</div>";
   }
}
?>