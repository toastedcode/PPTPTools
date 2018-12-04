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
   
      <div class="flex-vertical content">

         <div class="heading">Digital Signage</div>

         <div class="description">You can use PPTP Tools to set up and configure your Screenly digital signs.</div>

         <div class="flex-vertical inner-content"> 
   
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
      $html = "";
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getSigns();
         
         if ($result && ($database->countResults($result) > 0))
         {
         
            $html = 
<<<HEREDOC
            <div class="table-container">
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
            
            $html .=
<<<HEREDOC
               </table>
            </div>
HEREDOC;
         }
         else
         {
            $html = "<div class=\"no-data\">No signs are currently set up in the system.</div>";
         }
      }
      
      return ($html);
   }
}
?>