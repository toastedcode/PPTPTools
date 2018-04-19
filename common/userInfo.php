<?php

require_once 'database.php';
require_once 'permissions.php';
require_once 'roles.php';

class UserInfo
{
   const UNKNOWN_EMPLOYEE_NUMBER = 0;
   
   public $employeeNumber;
   public $username;
   public $password;
   public $firstName;
   public $lastName;
   public $roles = Role::UNKNOWN;
   public $permissions = Permission::NO_PERMISSIONS;
   public $email;
   
   public static function load($employeeNumber)
   {
      $userInfo = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getUser($employeeNumber);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $userInfo = new UserInfo();
            
            $userInfo->employeeNumber = intval($row['employeeNumber']);
            $userInfo->username = $row['username'];
            $userInfo->password = $row['password'];
            $userInfo->roles = intval($row['roles']);
            $userInfo->permissions = intval($row['permissions']);
            $userInfo->firstName = $row['firstName'];
            $userInfo->lastName = $row['lastName'];
            $userInfo->email = $row['email'];
         }
      }
      
      return ($userInfo);
   }
   
   static public function loadByName($username)
   {
      $userInfo = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getUserByName($username);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $userInfo = new UserInfo();
            
            $userInfo->employeeNumber = intval($row['employeeNumber']);
            $userInfo->username = $row['username'];
            $userInfo->password = $row['password'];
            $userInfo->roles = intval($row['roles']);
            $userInfo->permissions = intval($row['permissions']);
            $userInfo->firstName = $row['firstName'];
            $userInfo->lastName = $row['lastName'];
            $userInfo->email = $row['email'];
         }
      }
      
      return ($userInfo);
   }
   
   static public function getUsersByRole($role)
   {
      $users = array();
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getUsersByRole($role);

         if ($result)
         {
            while ($row = $result->fetch_assoc())
            {
               $users[] = new UserInfo($row);
            }
         }
      }
      
      return ($users);
   }
   
   public function getFullName()
   {
      return ($this->firstName . " " . $this->lastName);
   }
}

/*
$userInfo = null;

if (isset($_GET["employeeNumber"]))
{
   $employeeNumber = $_GET["employeeNumber"];
   $userInfo = UserInfo::load($employeeNumber);
}
else if (isset($_GET["username"]))
{
   $username = $_GET["username"];
   $userInfo = UserInfo::loadByName($username);
}
    
if ($userInfo)
{
   echo "employeeNumber: " . $userInfo->employeeNumber . "<br/>";
   echo "username: " .       $userInfo->username .       "<br/>";
   echo "password: " .       $userInfo->password .       "<br/>";
   echo "roles: " .          $userInfo->roles .          "<br/>";
   echo "permissions: " .    $userInfo->permissions .    "<br/>";
   echo "firstName: " .      $userInfo->firstName .      "<br/>";
   echo "lastName: " .       $userInfo->lastName .       "<br/>";
   echo "email: " .          $userInfo->email .          "<br/>";
   
   echo "fullName: " . $userInfo->getFullName() . "<br/>";
}
else
{
   echo "No user found.";
}
*/
?>