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
      <div class="header">
         <div id="menu-button" class="menu-button"><i class="menu-icon material-icons action-button-icon">menu</i></div>
         <div class="flex-horizontal" style="justify-content: space-between; width: 100%; padding-right:20px;">
            <a class="page-title" href="$ROOT/home.php">$pageTitle</a>
HEREDOC;
         
      if (Authentication::isAuthenticated())
      {
         $username = Authentication::getAuthenticatedUser()->username;
         
         echo
<<<HEREDOC
            <div class="flex-horizontal flex-v-center">
               <i class="material-icons" style="margin-right:5px; color: #ffffff; font-size: 24px;">person</i>
               <div class="nav-username">$username&nbsp | &nbsp</div>
               <a class="nav-link" href="$ROOT/home.php?action=logout">Logout</a>
            </div>
HEREDOC;
      }
            
      echo "</div></div>";
   }
}
?>