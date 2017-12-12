<?php
require_once 'database.php';

abstract class AuthenticationResult
{
   const AUTHENTICATED = 0;
   const INVALID_USERNAME = 1;
   const INVALID_PASSWORD = 2;
}

abstract class Permissions
{
   const USER = 0;
   const ADMIN = 1;
   const SUPERUSER = 2;
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
         $authenticatedUser = $_SESSION["authenticatedUser"];
      }
      
      return ($authenticatedUser);
   }
   
   static public function getPermissions()
   {
      $permissions = Permissions::USER;
      
      if (isset($_SESSION["permissions"]))
      {
         $permissions= $_SESSION["permissions"];
      }
      
      return ($permissions);
   }
   
   static public function authenticate($username, $password)
   {
      $result = AuthenticationResult::INVALID_USERNAME;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         if (!Authentication::validUser($username, $database))
         {
            $result = AuthenticationResult::INVALID_USERNAME;
         }
         else if (!Authentication::validPassword($username, $password, $database))
         {
            $result = AuthenticationResult::INVALID_PASSWORD;
         }
         else
         {
            $result = AuthenticationResult::AUTHENTICATED;
            
            // Record authentication status and user name.
            $_SESSION['authenticated'] = true;
            $_SESSION['authenticatedUser'] = $username;

            // Retrieve user record.
            $result = $database->getUser($username);
            $user = $result->fetch_assoc();

            // Record permissions.
            if ($user && isset($user["permissions"]))
            {
               $_SESSION["permissions"] = $user["permissions"];
            }
         }
      }
      
      return ($result);
   }
   
   static public function deauthenticate()
   {
      $_SESSION['authenticated'] = false;
      unset($_SESSION['authenticatedUser']);
   }
      
   static private function validUser($username, $database)
   {
      $validUser = false;
      
      if ($database->isConnected())
      {
         $result = $database->getUser($username);
         
         $user = $result->fetch_assoc();
         
         $validUser = ($user!= null);
      }

      return ($validUser);
   }
   
   static private function validPassword($username, $password, $database)
   {
      $validPassword = false;
      
      if ($database->isConnected())
      {
         $result = $database->getUser($username);
         
         $user = $result->fetch_assoc();
         
         if ($user && isset($user['Password']))
         {
            $validPassword = ($password == $user['Password']);
         }
      }
      
      return ($validPassword);
   }
}
?>