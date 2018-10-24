<?php

require_once '../common/userInfo.php';
require_once '../common/navigation.php';
require_once '../common/userInfo.php';

class ViewUser
{
   public function getHtml($view)
   {
      $html = "";
      
      $userInfo = ViewUser::getUserInfo();
      
      $newUser = ($userInfo->employeeNumber == UserInfo::UNKNOWN_EMPLOYEE_NUMBER);
      
      $editable = (($view == "new_user") || ($view == "edit_user"));
      
      $titleDiv = ViewUser::titleDiv();
      $userDiv = ViewUser::userDiv($userInfo, $view);
      $permissionsDiv = ViewUser::permissionsDiv($userInfo, $view);
      $navBar = ViewUser::navBar($userInfo, $view);
      
      $title = "";
      if ($view == "new_user")
      {
         $title = "New User";
      }
      else if ($view == "edit_user")
      {
         $title = "Edit User";
      }
      else if ($view == "view_user")
      {
         $title = "View User";
      }
      
      $html =
<<<HEREDOC
      <form id="input-form" action="#" method="POST"></form>

      <div class="flex-vertical card-div">
         <div class="card-header-div">$title</div>
         
         <div class="flex-vertical content-div">
            <div class="flex-vertical time-card-div">
               <div class="flex-horizontal">
                  $titleDiv
               </div>
               <div class="flex-horizontal" style="align-items: flex-start;">
                  $userDiv
                  $permissionsDiv
               </div>
            </div>
         </div>
         
         $navBar
               
      </div>
               
      <script>
         var employeeNumberValidator = new IntValidator("employee-number-input", 4, 1, 9999, false);

         employeeNumberValidator.init();
      </script>
HEREDOC;
      
      return ($html);
   }
   
   public function render($view)
   {
      echo (ViewUser::getHtml($view));
   }
   
   protected static function titleDiv()
   {
      $html =
<<<HEREDOC
      <div class="flex-horizontal time-card-table-col">
         <h1>User</h1>
      </div>
HEREDOC;
      
      return ($html);
   }
   
   protected static function userDiv($userInfo, $view)
   {
      $editable = (($view == "new_user") || ($view == "edit_user"));
      
      $disabled = ($editable) ? "" : "disabled";
      $employeeNumberDisabled = ($editable && ($view == "new_user")) ? "" : "disabled";
      
      $roleOptions = "";
      foreach (Role::getRoles() as $role)
      {
         $selected = ($userInfo->roles == $role->roleId) ? "selected" : "";
         $roleOptions.= "<option $selected value=\"" . $role->roleId . "\">" . $role->roleName . "</option>";
      }
      
      $html =
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">

         <div class="section-header-div"><h2>Identity</h2></div>

         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Employee #</h3></div>
            <input id="employee-number-input" type="text" class="medium-text-input" name="employeeNumber" form="input-form" style="width:150px;" value="$userInfo->employeeNumber" oninput="this.validator.validate()" $employeeNumberDisabled/>
         </div>

         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>First Name</h3></div>
            <input id="first-name-input" type="text" class="medium-text-input" name="firstName" form="input-form" style="width:150px;" value="$userInfo->firstName" $disabled />
         </div>

         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Last Name</h3></div>
            <input id="last-name-input" type="text" class="medium-text-input" name="lastName" form="input-form" style="width:150px;" value="$userInfo->lastName" $disabled />
         </div>

         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Email</h3></div>
            <input id="email-input" type="text" class="medium-text-input" name="email" form="input-form" style="width:300px;" value="$userInfo->email" $disabled />
         </div>

         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Role</h3></div>
            <div><select id="role-input" class="medium-text-input" name="roles" form="input-form" $disabled>$roleOptions</select></div>
         </div>

         <div class="section-header-div"><h2>Login</h2></div>

         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Username</h3></div>
            <input id="user-name-input" type="text" class="medium-text-input" name="username" form="input-form" style="width:150px;" value="$userInfo->username" $disabled />
         </div>

         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Password</h3></div>
            <input id="user-name-input" type="password" class="medium-text-input" name="username" form="input-form" style="width:150px;" value="$userInfo->username" $disabled />
         </div>
      </div>
HEREDOC;
      
      return ($html);
   }
   
   /*
   protected static function roleDiv($userInfo, $view)
   {
      $editable = (($view == "new_user") || ($view == "edit_user"));
      
      $disabled = ($editable) ? "" : "disabled";
      
      $roleOptions = "";
      foreach (Role::getRoles() as $role)
      {
         $selected = ($userInfo->roles == $role->roleId) ? "selected" : "";
         $roleOptions.= "<option $selected value=\"" . $role->roleId . "\">" . $role->roleName . "</option>";
      }
      
      $html =
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">
         <div class="section-header-div"><h2>Role</h2></div>
         <div class="flex-horizontal time-card-table-row">
            <div class="label-div"><h3>Role</h3></div>
            <div><select id="role-input" class="medium-text-input" name="roles" form="input-form" $disabled>$roleOptions</select></div>
         </div>
      </div>
HEREDOC;

      return ($html);
   }
   */
      
   
   protected static function permissionsDiv($userInfo, $view)
   {
      $editable = (($view == "new_user") || ($view == "edit_user"));
      
      $disabled = ($editable) ? "" : "disabled";
      
      $roleOptions = "";
      foreach (Role::getRoles() as $role)
      {
         $selected = ($userInfo->roles == $role->roleId) ? "selected" : "";
         $roleOptions.= "<option $selected value=\"" . $role->roleId . "\">" . $role->roleName . "</option>";
      }
      
      $html =
<<<HEREDOC
      <div class="flex-vertical time-card-table-col">
         <div class="section-header-div"><h2>Permissions</h2></div>
HEREDOC;

      foreach (Permission::getPermissions() as $permission)
      {
         $html .= ViewUser::permissionDiv($userInfo, $permission, $view);
      }

      $html .=
<<<HEREDOC
      </div>
HEREDOC;
      
      return ($html);
   }
   
   protected static function permissionDiv($userInfo, $permission, $view)
   {
      $editable = (($view == "new_user") || ($view == "edit_user"));
      
      $disabled = ($editable) ? "" : "disabled";
      
      $id = "permission-" . $permission->permissionId . "-input";
      $name = "permission-" . $permission->permissionId;
      $description = $permission->permissionName;
      $checked = $permission->isSetIn($userInfo->permissions) ? "checked" : "";
      
      
      $html =
<<<HEREDOC
      <div class="flex-horizontal">
         <input id="$id" type="checkbox" class="permission-checkbox" form="input-form" name="$name" $checked $disabled/>
         <label for="$id" class="medium-text-input">$description</label>
      </div>
HEREDOC;

      return ($html);
   }
   
   protected static function navBar($userInfo, $view)
   {
      $navBar = new Navigation();
      
      $navBar->start();
      
      if (($view == "new_user") ||
          ($view == "edit_user"))
      {
         // Case 1
         // Creating a new user.
         // Editing an existing user.
         
         $navBar->cancelButton("submitForm('input-form', 'user.php', 'view_users', 'cancel_user')");
         $navBar->highlightNavButton("Save", "if (validateUser()){submitForm('input-form', 'user.php', 'view_users', 'save_user');};", false);
      }
      else if ($view == "view_user")
      {
         // Case 2
         // Viewing an existing user.
         
         $navBar->highlightNavButton("Ok", "submitForm('input-form', 'user.php', 'view_users', 'no_action')", false);
      }
      
      $navBar->end();
      
      return ($navBar->getHtml());
   }
   
   protected static function getUserInfo()
   {
      $userInfo = new UserInfo();
      
      if (isset($_GET['employeeNumber']))
      {
         $userInfo = UserInfo::load($_GET['employeeNumber']);
      }
      else if (isset($_POST['employeeNumber']))
      {
         $userInfo = UserInfo::load($_POST['employeeNumber']);
      }
      else if (isset($_SESSION['userInfo']))
      {
         $userInfo = $_SESSION['userInfo'];
      }
      
      return ($userInfo);
   }
}
?>