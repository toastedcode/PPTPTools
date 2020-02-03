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
      
      $headingDiv = ViewUser::headingDiv($view);
      $descriptionDiv = ViewUser::descriptionDiv($view);
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
      <form id="input-form" method="POST"></form>

      <div class="flex-vertical content">

         $headingDiv

         $descriptionDiv

         <div class="flex-horizontal inner-content" style="justify-content: flex-start; flex-wrap: wrap;">

            $userDiv
            
            $permissionsDiv
            
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
   
   protected static function headingDiv($view)
   {
      $heading = "";
      if ($view == "new_user")
      {
         $heading = "Add a New User";
      }
      if ($view == "edit_user")
      {
         $heading = "Edit an Existing User";
      }
      else if ($view == "view_user")
      {
         $heading = "View User Details";
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
      if ($view == "new_user")
      {
         $description = "Users of the PPTP Tools system can be given a variety of roles and permissions.  Here you can set up a new user and give them as much access as their job requires.";
      }
      else if ($view == "edit_user")
      {
         $description = "You may revise any of the settings associated with this user and then select save when you're satisfied with the changes.";
      }
      else if ($view == "view_user")
      {
         $description = "View the settings and access permissions of this user.";
      }
      
      $html =
<<<HEREDOC
      <div class="description">$description</div>
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
      <div class="flex-vertical" style="align-items: flex-start; margin-right: 50px;">

         <div class="form-section-header">Identity</div>

         <div class="form-item">
            <div class="form-label">Employee #</div>
            <input id="employee-number-input" type="text" class="form-input-medium" name="employeeNumber" form="input-form" style="width:150px;" value="$userInfo->employeeNumber" oninput="this.validator.validate()" $employeeNumberDisabled/>
         </div>

         <div class="form-item">
            <div class="form-label">First Name</div>
            <input id="first-name-input" type="text" class="form-input-medium" name="firstName" form="input-form" style="width:150px;" value="$userInfo->firstName" $disabled />
         </div>

         <div class="form-item">
            <div class="form-label">Last Name</div>
            <input id="last-name-input" type="text" class="form-input-medium" name="lastName" form="input-form" style="width:150px;" value="$userInfo->lastName" $disabled />
         </div>

         <div class="form-item">
            <div class="form-label">Email</div>
            <input id="email-input" type="text" class="form-input-medium" name="email" form="input-form" style="width:300px;" value="$userInfo->email" $disabled />
         </div>

         <div class="form-item">
            <div class="form-label">Role</div>
            <div><select id="role-input" class="form-input-medium" name="roles" form="input-form" $disabled>$roleOptions</select></div>
         </div>

         <div class="form-section-header">Login</div>

         <div class="form-item">
            <div class="form-label">Username</div>
            <input id="user-name-input" type="text" class="form-input-medium" name="username" form="input-form" style="width:150px;" value="$userInfo->username" $disabled />
         </div>

         <div class="form-item">
            <div class="form-label">Password</div>
            <input id="user-password-input" type="password" class="form-input-medium" name="password" form="input-form" style="width:150px;" value="$userInfo->password" $disabled />
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
      <div class="flex-vertical" style="align-items: flex-start;">
         <div class="form-section-header">Permissions</div>
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
         <label for="$id" class="form-input-medium">$description</label>
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