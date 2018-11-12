<?php

require_once '../common/database.php';
require_once '../common/navigation.php';
require_once '../common/permissions.php';
require_once '../common/roles.php';
require_once '../common/userInfo.php';

class ViewSigns
{
   public function __construct()
   {
   }
   
   public function getHtml()
   {
      $signsDiv = ViewSigns::signsDiv();
      
      $navBar = ViewSigns::navBar();
      
      $html = 
<<<HEREDOC
      <script src="signage.js"></script>
   
      <div class="flex-vertical card-div">
         <div class="card-header-div">View Signs</div>
         <div class="flex-vertical content-div" style="justify-content: flex-start; height:400px;">
   
               $signsDiv
         
         </div>

         $navBar;

      </div>
HEREDOC;
      
      return ($html);
   }
   
   public function render()
   {
      echo (ViewSigns::getHtml());
   }
   
   private function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->mainMenuButton();
      $navBar->highlightNavButton("New Sign", "onNewSign()", true);
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private function signsDiv()
   {
      $html = 
<<<HEREDOC
         <div class="signs-div">
            <table class="signs-table">
               <tr>
                  <th/>
                  <th>Name</th>
                  <th>Description</th>
                  <th>URL</th>
                  <th/>
                  <th/>
               </tr>
HEREDOC;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {         
         $result = $database->getSigns();
         
         if ($result)
         {
            while ($row = $result->fetch_assoc())
            {
               $signInfo = SignInfo::load($row["signId"]);
               
               $signButton = "<button class=\"launch-button\" onclick=\"openURL('$signInfo->url')\">Go</button>";
               
               $viewEditIcon = "";
               $deleteIcon = "";
               if (Authentication::checkPermissions(Permission::EDIT_SIGN))
               {
                  $viewEditIcon =
                  "<i class=\"material-icons table-function-button\" onclick=\"onEditSign('$signInfo->signId')\">mode_edit</i>";
                  
                  $deleteIcon =
                  "<i class=\"material-icons table-function-button\" onclick=\"onDeleteSign('$signInfo->signId')\">delete</i>";
               }
               else
               {
                  $viewEditIcon =
                  "<i class=\"material-icons table-function-button\" onclick=\"onViewSign('$signInfo->signId')\">visibility</i>";
               }
               
               if ($signInfo)
               {
                  $html .=
<<<HEREDOC
                     <tr>
                        <td>$signButton</td>
                        <td>$signInfo->name</td>
                        <td>$signInfo->description</td>
                        <td>$signInfo->url</td>
                        <td>$viewEditIcon</td>
                        <td>$deleteIcon</td>
                     </tr>
HEREDOC;
               }
            }
         }
      }
      
      $html .=
<<<HEREDOC
            </table>
         </div>
HEREDOC;
      
      return ($html);
   }
}
?>