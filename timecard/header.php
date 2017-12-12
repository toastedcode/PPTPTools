<?php
require_once '../authentication.php';

class Header
{
   public static function render($pageTitle)
   {
      $authenticatedUser = Authentication::getAuthenticatedUser();
      
      echo 
<<<HEREDOC
      <div class="header-div">
         <span class="page-title">$pageTitle</span>
         <div class="flex-horizontal">
            <i class="material-icons" style="margin-right:5px; color: #ffffff; font-size: 24px;">person</i>
            <div class="nav-username">$authenticatedUser &nbsp | &nbsp</div>
            <a class="nav-link" href="../pptpTools.php?action=logout">Logout</a>
         </div>
      </div>
HEREDOC;
   }
}
?>