<?php

require_once 'database.php';

abstract class Permissions
{
   const SUPER_USER  = 0x0001;
   const ADMIN       = 0x0002;
   const OPERATOR    = 0x0004;
   const PART_WASHER = 0x0008;
   const LABORER     = 0x0010;
}

class User
{
   static public function getUser($employeeNumber)
   {
      $user = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getUser($employeeNumber);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $user = new User($row);
         }
      }
      
      return ($user);
   }
   
   static public function getUserByName($username)
   {
      $user = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getUserByName($username);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $user = new User($row);
         }
      }
      
      return ($user);
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
               $users[] = new User($row);
            }
         }
      }
      
      return ($users);
   }
   
   public function getFullName()
   {
      return ($this->firstName . " " . $this->lastName);
   }
   
   public function isSuperUser()
   {
      return (this.permissions & Permissions::SUPER_USER);
   }
   
   public function isAdmin()
   {
      return ((this.permissions & Permissions::ADMIN) || isSuperUser());
   }
   
   public function isOperator()
   {
      return ((this.permissions & Permissions::OPERATOR) || isSuperUser());
   }
   
   public function isPartWasher()
   {
      return ((this.permissions & Permissions::OPERATOR) || isSuperUser());
   }
     
   public $employeeNumber;
   
   public $username;
      
   public $firstName;
   
   public $lastName;
   
   public $permissions;
   
   public $email;
   
   private function __construct($userData)
   {
      $this->employeeNumber = $userData['employeeNumber'];
      $this->username = $userData['username'];
      $this->password = $userData['password'];
      $this->permissions = $userData['permissions'];
      $this->firstName = $userData['firstName'];
      $this->lastName = $userData['lastName'];
      $this->email = $userData['email'];
   }
}

if (isset($_GET['permissions']))
{
   $users = User::getUsers($_GET['permissions']);
   for ($i = 0; $i < count($users); $i++)
   {
      echo $users[$i]->getFullName() . "<br/>";
   }
}
else if (isset($_GET['employeeNumber']))
{
   $user = User::getUser($_GET['employeeNumber']);
   echo $user->getFullName() . "<br/>";
}
else if (isset($_GET['username']))
{
   $user = User::getUserByName($_GET['username']);
   echo $user->getFullName() . "<br/>";
}
?>