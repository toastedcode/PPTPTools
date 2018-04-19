<?php

require_once 'database.php';

class UserInfo
{
   public $employeeNumber;
   
   public $username;
   
   public $firstName;
   
   public $lastName;
   
   public $permissions;
   
   public $email;
   
   public static function load($employeeNumber)
   {
      $user = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getUser($employeeNumber);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $user = new UserInfo($row);
         }
      }
      
      return ($user);
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
            $userInfo = new UserInfo($row);
         }
      }
      
      return ($userInfo);
   }
   
   static public function getUsers($permissionMask)
   {
      $users = array();
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getUsersByPermissions($permissionMask);

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
   
   private function __construct($userData)
   {
      $this->employeeNumber = intval($userData['employeeNumber']);
      $this->username = $userData['username'];
      $this->password = $userData['password'];
      $this->permissions = intval($userData['permissions']);
      $this->firstName = $userData['firstName'];
      $this->lastName = $userData['lastName'];
      $this->email = $userData['email'];
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