<?php

require_once '../common/activity.php';
require_once '../common/commentCodes.php';
require_once '../common/header2.php';
require_once '../common/userInfo.php';
require_once '../common/menu.php';
require_once '../common/params.php';

const ACTIVITY = Activity::USER;
$activity = Activity::getActivity(ACTIVITY);

abstract class UserInputField
{
   const FIRST = 0;
   const EMPLOYEE_NUMBER = UserInputField::FIRST;
   const FIRST_NAME = 1;
   const LAST_NAME = 2;
   const EMAIL = 3;
   const ROLE = 4;
   const USERNAME = 5;
   const PASSWORD = 6;
   const AUTHENTICATION_TOKEN = 7;
   const PERMISSIONS = 8;
   const LAST = 9;
   const COUNT = UserInputField::LAST - UserInputField::FIRST;
}

abstract class View
{
   const NEW_USER = 0;
   const VIEW_USER = 1;
   const EDIT_USER = 2;
}

function getParams()
{
   static $params = null;
   
   if (!$params)
   {
      $params = Params::parse();
   }
   
   return ($params);
}

function getView()
{
   $view = View::VIEW_USER;
   
   if (getEmployeeNumber() == UserInfo::UNKNOWN_EMPLOYEE_NUMBER)
   {
      $view = View::NEW_USER;
   }
   else if (Authentication::checkPermissions(Permission::EDIT_USER))
   {
      $view = View::EDIT_USER;
   }
   
   return ($view);
}

function isEditable($field)
{
   $view = getView();
   
   // Start with the edit mode, as dictated by the view.
   $isEditable = (($view == View::NEW_USER) ||
                  ($view == View::EDIT_USER));
   
   switch ($field)
   {
      case UserInputField::EMPLOYEE_NUMBER:
      {
         $isEditable = ($view == View::NEW_USER);
         break;
      }

      default:
      {
         // Edit status based solely on view.
         break;
      }
   }
   
   return ($isEditable);
}

function getEmployeeNumber()
{
   $employeeNumber = UserInfo::UNKNOWN_EMPLOYEE_NUMBER;
   
   $params = getParams();
   
   if ($params->keyExists("employeeNumber"))
   {
      $employeeNumber = $params->getInt("employeeNumber");
   }
   
   return ($employeeNumber);
}

function getUserInfo()
{
   static $userInfo = null;
   
   if ($userInfo == null)
   {
      $employeeNumber = getEmployeeNumber();
      
      if ($employeeNumber != UserInfo::UNKNOWN_EMPLOYEE_NUMBER)
      {
         $userInfo = UserInfo::load($employeeNumber);
      }
      else
      {
         $userInfo = new UserInfo();
      }
   }
   
   return ($userInfo);
}


function getHeading()
{
   $heading = "";
   
   switch (getView())
   {
      case View::NEW_USER:
      {
         $heading = "Add a New User";
         break;
      }
      
      case View::EDIT_USER:
      {
         $heading = "Edit an Existing User";
         break;
      }
      
      case View::VIEW_USER:
      default:
      {
         $heading = "View User Details";
         break;
      }
   }

   return ($heading);
}

function getDescription()
{
   $description = "";
   
   switch (getView())
   {
      case View::NEW_USER:
      {
         $description = "Users of the PPTP Tools system can be given a variety of roles and permissions.  Here you can set up a new user and give them as much access as their job requires.";
         break;
      }
         
      case View::EDIT_USER:
      {
         $description = "You may revise any of the settings associated with this user and then select save when you're satisfied with the changes.";
         break;
      }
         
      case View::VIEW_USER:
      default:
      {
         $description = "View the settings and access permissions of this user.";
         break;
      }
   }
   
   return ($description);
}

function getRoleOptions()
{
   $options = "<option style=\"display:none\">";
   
   $selectedRole = getUserInfo()->roles;
   
   foreach (Role::getRoles() as $role)
   {
      $selected = ($role->roleId == $selectedRole) ? "selected" : "";
      $options.= "<option $selected value=\"" . $role->roleId . "\">" . $role->roleName . "</option>";
   }
   
   return ($options);
}

function getPermissionInputs()
{
   $html = "";
   
   $userInfo = getUserInfo();
   
   $disabled = isEditable(UserInputField::PERMISSIONS) ? "" : "disabled";

   foreach (Permission::getPermissions() as $permission)
   {
      $id = "permission-" . $permission->permissionId . "-input";
      $name = "permission-" . $permission->permissionId;
      $description = $permission->permissionName;
      $checked = $permission->isSetIn($userInfo->permissions) ? "checked" : "";
      
      $html .=
<<<HEREDOC
      <div class="flex-horizontal flex-v-center">
         <input id="$id" type="checkbox" class="permission-checkbox" form="input-form" name="$name" $checked $disabled/>
         <label for="$id" class="form-input-medium">$description</label>
      </div>
HEREDOC;
   }
   
   return ($html);
}

// ********************************** BEGIN ************************************

Time::init();

session_start();

if (!Authentication::isAuthenticated())
{
   header('Location: ../home.php');
   exit;
}

?>

<!DOCTYPE html>
<html>

<head>

   <meta name="viewport" content="width=device-width, initial-scale=1">

   <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
   
   <link rel="stylesheet" type="text/css" href="../common/theme.css"/>
   <link rel="stylesheet" type="text/css" href="../common/common2.css"/>
   
   <script src="../common/common.js"></script>
   <script src="../common/validate.js"></script>
   <script src="user.js"></script>

</head>

<body class="flex-vertical flex-top flex-left">
        
   <form id="input-form" action="" method="POST">
         <input type="hidden" name="employeeNumber" value="<?php echo getUserInfo()->employeeNumber; ?>">   
   </form>

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="main flex-horizontal flex-top flex-left">
   
      <?php Menu::render(ACTIVITY); ?>
      
      <div class="content flex-vertical flex-top flex-left">
      
         <div class="flex-horizontal flex-v-center flex-h-center">
            <div class="heading"><?php echo getHeading(); ?></div>&nbsp;&nbsp;
            <i id="help-icon" class="material-icons icon-button">help</i>
         </div>
         
         <div id="description" class="description"><?php echo getDescription(); ?></div>
         
         <br>
         
         <div class="flex-horizontal flex-left flex-wrap">

            <div class="flex-vertical flex-top" style="margin-right: 50px;">
      
               <div class="form-section-header">Identity</div>
      
               <div class="form-item">
                  <div class="form-label">Employee #</div>
                  <input id="employee-number-input" type="text" name="employeeNumber" form="input-form" style="width:150px;" value="<?php echo getUserInfo()->employeeNumber; ?>" oninput="this.validator.validate()" <?php echo !isEditable(UserInputField::EMPLOYEE_NUMBER) ? "disabled" : ""; ?>/>
               </div>
      
               <div class="form-item">
                  <div class="form-label">First Name</div>
                  <input id="first-name-input" type="text" name="firstName" form="input-form" style="width:150px;" value="<?php echo getUserInfo()->firstName; ?>" <?php echo !isEditable(UserInputField::FIRST_NAME) ? "disabled" : ""; ?> />
               </div>
      
               <div class="form-item">
                  <div class="form-label">Last Name</div>
                  <input id="last-name-input" type="text" name="lastName" form="input-form" style="width:150px;" value="<?php echo getUserInfo()->lastName; ?>" <?php echo !isEditable(UserInputField::LAST_NAME) ? "disabled" : ""; ?> />
               </div>
      
               <div class="form-item">
                  <div class="form-label">Email</div>
                  <input id="email-input" type="text" name="email" form="input-form" style="width:300px;" value="<?php echo getUserInfo()->email; ?>" <?php echo !isEditable(UserInputField::EMAIL) ? "disabled" : ""; ?> />
               </div>
      
               <div class="form-item">
                  <div class="form-label">Role</div>
                  <div><select id="role-input" name="roles" form="input-form" <?php echo !isEditable(UserInputField::PERMISSIONS) ? "disabled" : ""; ?>><?php echo getRoleOptions(); ?></select></div>
               </div>
      
               <div class="form-section-header">Login</div>
      
               <div class="form-item">
                  <div class="form-label">Username</div>
                  <input id="user-name-input" type="text" name="username" form="input-form" style="width:150px;" value="<?php echo getUserInfo()->username; ?>" <?php echo !isEditable(UserInputField::USERNAME) ? "disabled" : ""; ?> />
               </div>
      
               <div class="form-item">
                  <div class="form-label">Password</div>
                  <input id="user-password-input" type="password" name="password" form="input-form" style="width:150px;" value="<?php echo getUserInfo()->password; ?>" <?php echo !isEditable(UserInputField::PASSWORD) ? "disabled" : ""; ?> />
               </div>
      
               <div class="form-item">
                  <div class="form-label">Authentication token</div>
                  <div class="flex-horizontal">
                     <input id="auth-token-input" type="text" name="authToken" form="input-form" style="width:150px;" value="<?php echo getUserInfo()->authToken; ?>" readonly <?php echo !isEditable(UserInputField::AUTHENTICATION_TOKEN) ? "disabled" : ""; ?>/>
                     &nbsp
                     <button class="small-button <?php echo !isEditable(UserInputField::AUTHENTICATION_TOKEN) ? "disabled" : ""; ?>" onclick="refreshAuthToken()">Refresh</button>
                     &nbsp
                     <button class="small-button" onclick="copyToClipboard('auth-token-input')">Copy</button>
                  </div>
               </div>
      
            </div>
            
            <div class="flex-vertical flex-top">
               <div class="form-section-header">Permissions</div>
               <?php echo getPermissionInputs(); ?>
            </div>
            
         </div>
         
         <div class="flex-horizontal flex-h-center">
            <button id="cancel-button">Cancel</button>&nbsp;&nbsp;&nbsp;
            <button id="save-button" class="accent-button">Save</button>            
         </div>
      
      </div> <!-- content -->
     
   </div> <!-- main -->   
         
   <script>
   
      preserveSession();
      
      var employeeNumberValidator = new IntValidator("employee-number-input", 4, 1, 9999, false);
      employeeNumberValidator.init();

      // Setup event handling on all DOM elements.
      document.getElementById("cancel-button").onclick = function(){window.history.back();};
      document.getElementById("save-button").onclick = function(){onSubmit();};      
      document.getElementById("help-icon").onclick = function(){document.getElementById("description").classList.toggle('shown');};
      document.getElementById("menu-button").onclick = function(){document.getElementById("menu").classList.toggle('shown');};
            
   </script>

</body>

</html>
