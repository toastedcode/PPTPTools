<?php

require_once 'database.php';

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
      $userInfo= null;
      
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
            $userInfo= new UserInfo();
            
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
if (isset($_GET["employeeNumber"]))
{
   $employeeNumber = $_GET["employeeNumber"];
   $user = UserInfo::load($employeeNumber);
    
   if ($user)
   {
      echo "employeeNumber: " . $user->employeeNumber . "<br/>";
      echo "username: " .       $user->username .       "<br/>";
      echo "password: " .       $user->password .       "<br/>";
      echo "roles: " .          $user->roles .          "<br/>";
      echo "permissions: " .    $user->permissions .    "<br/>";
      echo "firstName: " .      $user->firstName .      "<br/>";
      echo "lastName: " .       $user->lastName .       "<br/>";
      echo "email: " .          $user->email .          "<br/>";
      
      echo "fullName: " . $user->getFullName() . "<br/>";
   }
   else
   {
      echo "No user found.";
   }
}
*/
?>