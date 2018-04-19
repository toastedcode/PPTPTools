<?php
require_once 'userInfo.php';

abstract class AuthenticationResult
{
   const AUTHENTICATED = 0;
   const INVALID_USERNAME = 1;
   const INVALID_PASSWORD = 2;
}

class Authentication
{
   static public function isAuthenticated()
   {
      return (isset($_SESSION["authenticated"]) && ($_SESSION["authenticated"] == true));
   }
   
   static public function getAuthenticatedUser()
   {
      $authenticatedUser = null;
      
      if (isset($_SESSION["authenticatedUser"]) && ($_SESSION["authenticatedUser"] == true))
      {
         $authenticatedUser = UserInfo::loadByName($_SESSION["authenticatedUser"]);
      }
      
      return ($authenticatedUser);
   }
   
   static public function getPermissions()
   {
      $permissions = 0;
      
      if (isset($_SESSION["permissions"]))
      {
         $permissions= $_SESSION["permissions"];
      }
      
      return ($permissions);
   }
   
   static public function authenticate($username, $password)
   {
      $result = AuthenticationResult::INVALID_USERNAME;
      
      $user = UserInfo::loadByName($username);
      
      if ($user == null)
      {
         $result = AuthenticationResult::INVALID_USERNAME;
      }
      else if ($password != $user->password)
      {
         $result = AuthenticationResult::INVALID_PASSWORD;
      }
      else
      {
         $result = AuthenticationResult::AUTHENTICATED;
         
         // Record authentication status and user name.
         $_SESSION['authenticated'] = true;
         $_SESSION['authenticatedUser'] = $username;
         $_SESSION["permissions"] = $user->permissions;
      }
      
      return ($result);
   }
   
   static public function deauthenticate()
   {
      $_SESSION['authenticated'] = false;
      unset($_SESSION['authenticatedUser']);
   }
}
?>