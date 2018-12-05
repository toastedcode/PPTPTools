<?php

require_once '../common/signInfo.php';
require_once '../common/navigation.php';

class ViewSign
{
   public function getHtml($view)
   {
      $html = "";
      
      $signInfo = ViewSign::getSignInfo();
      
      $newSign = ($signInfo->signId == SignInfo::UNKNOWN_SIGN_ID);
      
      $editable = (($view == "new_sign") || ($view == "edit_sign"));
      
      $headingDiv = ViewSign::headingDiv($view);
      
      $descriptionDiv = ViewSign::descriptionDiv($view);
      
      $signDiv = ViewSign::signDiv($signInfo, $view);
      
      $navBar = ViewSign::navBar($signInfo, $view);
      
      $title = "";
      if ($view == "new_sign")
      {
         $title = "New Sign";
      }
      else if ($view == "edit_sign")
      {
         $title = "Edit Sign";
      }
      else if ($view == "view_sign")
      {
         $title = "View Sign";
      }
      
      $html =
<<<HEREDOC
      <form id="input-form" action="#" method="POST">
         <input id="sign-id-input" type="hidden" name="signId" value="$signInfo->signId"/>
      </form>

      <div class="flex-vertical content">

         $headingDiv

         $descriptionDiv
         
         <div class="flex-vertical inner-content"> 

            $signDiv

         </div>

         $navBar
               
      </div>
               
      <script>
      </script>
HEREDOC;
      
      return ($html);
   }
   
   public function render($view)
   {
      echo (ViewSign::getHtml($view));
   }
   
   protected static function headingDiv($view)
   {
      $heading = "";
      if ($view == "new_sign")
      {
         $heading = "Add a New Digital Sign";
      }
      else if ($view == "edit_sign")
      {
         $heading = "Update a Digital Sign";
      }
      else if ($view == "view_sign")
      {
         $heading = "View a Digital Sign";
      }
      
      $html =
<<<HEREDOC
      <div class="heading">$heading</div>
HEREDOC;
      
      return ($html);
   }
   
   protected static function descriptionDiv($view)
   {
      $description = "";
      if ($view == "new_sign")
      {
         $description = "Start by giving your new sign a name (ex. \"Entrance sign\") and meaningful description (ex. \"Main entrance by front doors\").<br><br>Then specify the URL of the Rapsberri Pi computer running the sign.";
      }
      else if ($view == "edit_sign")
      {
         $description = "You may revise any of the fields for sign and then select save when you're satisfied with the changes.";
      }
      else if ($view == "view_sign")
      {
         $description = "View this sign's configuration.";
      }
      
      $html =
<<<HEREDOC
      <div class="description">$description</div>
HEREDOC;
      
      return ($html);
   }
  
   protected static function signDiv($signInfo, $view)
   {
      $editable = (($view == "new_sign") || ($view == "edit_sign"));
      
      $disabled = ($editable) ? "" : "disabled";
      
      $html =
<<<HEREDOC
         <div class="form-col">

            <div class="form-item">
               <div class="form-label">Name</div>
               <input id="sign-name-input" type="text" class="form-input-medium" name="name" form="input-form" style="width:300px;" value="$signInfo->name" $disabled/>
            </div>

            <div class="form-item">
               <div class="form-label">Description</div>
               <input id="sign-description-input" type="text" class="form-input-medium" name="description" form="input-form" style="width:300px;" value="$signInfo->description" $disabled/>
            </div>

            <div class="form-item">
               <div class="form-label">URL</div>
               <input id="sign-url-input" type="text" class="form-input-medium" name="url" form="input-form" style="width:300px;" value="$signInfo->url" $disabled/>
            </div>

         </div>
HEREDOC;
      
      return ($html);
   }
   
   protected static function navBar($signInfo, $view)
   {
      $navBar = new Navigation();
      
      $navBar->start();
      
      if (($view == "new_sign") ||
          ($view == "edit_sign"))
      {
         // Case 1
         // Creating a new sign.
         // Editing an existing sign.
         
         $navBar->cancelButton("submitForm('input-form', 'signage.php', 'view_signs', 'cancel_sign')");
         $navBar->highlightNavButton("Save", "submitForm('input-form', 'signage.php', 'view_signs', 'save_sign');", false);
      }
      else if ($view == "view_sign")
      {
         // Case 2
         // Viewing an existing sign.
         
         $navBar->highlightNavButton("Ok", "submitForm('input-form', 'signage.php', 'view_signs', 'no_action')", false);
      }
      
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   protected static function getSignInfo()
   {
      $signInfo = new SignInfo();
      
      if (isset($_GET['signId']))
      {
         $signInfo = SignInfo::load($_GET['signId']);
      }
      else if (isset($_POST['signId']))
      {
         $signInfo = SignInfo::load($_POST['signId']);
      }
      else if (isset($_SESSION['signInfo']))
      {
         $signInfo = $_SESSION['signInfo'];
      }
      
      return ($signInfo);
   }
}
?>