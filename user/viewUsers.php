<?php

require_once '../common/database.php';
require_once '../common/filter.php';
require_once '../common/navigation.php';
require_once '../common/permissions.php';
require_once '../common/roles.php';

class ViewUsers
{
   private $filter;
   
   public function __construct()
   {
      // TODO: A filter?
   }

   public function getHtml()
   {
      $usersDiv = ViewUsers::usersDiv();
      
      $navBar = ViewUsers::navBar();
      
      $html = 
<<<HEREDOC
      <div class="flex-vertical content">

         <div class="heading">Users</div>

         <div class="description">Add, view, and delete users from the PPTP Tools system from here.</div>

         <div class="flex-vertical inner-content"> 
   
            $usersDiv
         
         </div>

         $navBar;

      </div>
HEREDOC;
      
      return ($html);
   }
   
   public function render()
   {
      echo (ViewUsers::getHtml());
   }
   
   private function navBar()
   {
      $navBar = new Navigation();
      
      $navBar->start();
      $navBar->mainMenuButton();
      $navBar->highlightNavButton("New User", "onNewUser()", true);
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   private function usersDiv()
   {
      $html = "";
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getUsers();
         
         if (($result) && ($database->countResults($result) > 0))
         {
            
            $html = 
<<<HEREDOC
            <div class="table-container">
               <table class="user-table">
                  <tr>
                     <th>Employee Number</th>
                     <th>Name</th>
                     <th>Username</th>                  
                     <th class="hide-on-tablet">Email</th>
                     <th>Role</th>
                     <th class="hide-on-tablet">Permissions</th>
                     <th/>
                     <th/>
                     </tr>
HEREDOC;
      
            while ($row = $result->fetch_assoc())
            {
               $userInfo = UserInfo::load($row["employeeNumber"]);
               
               if ($userInfo)
               {
                  // TODO: Use roles as a bitmap of roles.
                  $roleName = Role::getRole($userInfo->roles)->roleName;
                  
                  $viewEditIcon = "";
                  $deleteIcon = "";
                  if (Authentication::checkPermissions(Permission::EDIT_USER))
                  {
                     $viewEditIcon =
                        "<i class=\"material-icons pan-ticket-function-button\" onclick=\"onEditUser('$userInfo->employeeNumber')\">mode_edit</i>";
                     
                     $deleteIcon =
                        "<i class=\"material-icons pan-ticket-function-button\" onclick=\"onDeleteUser('$userInfo->employeeNumber')\">delete</i>";
                  }
                  else
                  {
                     $viewEditIcon =
                        "<i class=\"material-icons table-function-button\" onclick=\"onViewUser('$userInfo->employeeNumber')\">visibility</i>";
                  }
                  
                  $html .=
<<<HEREDOC
                  <tr>
                     <td>$userInfo->employeeNumber</td>
                     <td>{$userInfo->getFullName()}</td>
                     <td>$userInfo->username</td>
                     <td class="hide-on-tablet">$userInfo->email</td>
                     <td>$roleName</td>
                     <td class="hide-on-tablet">$userInfo->permissions</td>
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
            $html = "<div class=\"no-data\">No data is available for the selected range.  Use the filter controls above to select a new operator or date range.</div>";
         }
      }
      
      return ($html);
   }
}
?>