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
      <div class="flex-vertical card-div">
         <div class="card-header-div">View Users</div>
         <div class="flex-vertical content-div" style="justify-content: flex-start; height:400px;">
   
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
      $html = 
<<<HEREDOC
         <div class="users-div">
            <table class="user-table">
               <tr>
                  <th>Employee Number</th>
                  <th>Name</th>
                  <th>Username</th>                  
                  <th>Email</th>
                  <th>Role</th>
                  <th>Permissions</th>
                  <th/>
                  <th/>
               </tr>
HEREDOC;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getUsers();
         
         if ($result)
         {
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
                        <td>$userInfo->email</td>
                        <td>$roleName</td>
                        <td>$userInfo->permissions</td>
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