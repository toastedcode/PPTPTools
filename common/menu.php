<?php

require_once 'activity.php';
require_once 'authentication.php';

class Menu
{
   public static function render($selectedActivityId)
   {
      $menuItems = Menu::menuItems($selectedActivityId);
      
      echo
<<<HEREDOC
      <div id="menu" class="menu"> 
         $menuItems
      </div>
HEREDOC;
   }
   
   private static function menuItems($selectedActivityId)
   {
      $html = "";
      
      foreach (Activity::$VALUES as $activityId)
      {
         //if (Activity::isAllowed($activityId, Authentication::getPermissions()))
         {
            $html .= Menu::menuItem($activityId, ($activityId == $selectedActivityId));
         }
      }
      
      return ($html);
   }
   
   private static function menuItem($activityId, $isSelected)
   {
      $html = "";
      
      $activity = Activity::getActivity($activityId);
      
      if ($activity)
      {
         $selected = $isSelected ? "selected" : "";
         $html =
<<<HEREDOC
      <a class="menu-item $selected" href="{$activity->url}">
         <i class="menu-icon material-icons">{$activity->icon}</i>
         <div class="menu-label">{$activity->label}</div>
      </a>
HEREDOC;
      }
      
      return ($html);
   }
}

?>