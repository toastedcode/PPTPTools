<?php

require_once 'database.php';

class User
{
   static public function getUser($employeeId)
   {
      $user = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $row = $database->getOperator($employeeNumber);
         
         if ($row)
         {
            $user = new User($row['EmployeeId'], $row['FirstName'],  $row['LastName'], $row['Permissions']);
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
         $result = $database->getOperatorByPermissions($permissionMask);
         
         if ($result)
         {
            while ($row = $result->fetch_assoc())
            {
               $users[] = new User($row['EmployeeId'], $row['Username'], $row['FirstName'],  $row['LastName'], $row['Permissions']);
            }
         }
      }
      
      return ($users);
   }
   
   public function getFullName()
   {
      return (this.firstName . " " . this.lastName);
   }
   
   public function isSuperUser()
   {
      return (this.permissions & SUPER_USER);
   }
   
   public function isAdmin()
   {
      return ((this.permissions & ADMIN) || isSuperUser());
   }
   
   public function isOperator()
   {
      return ((this.permissions & OPERATOR) || isSuperUser());
   }
   
   public function isPartWasher()
   {
      return ((this.permissions & OPERATOR) || isSuperUser());
   }
   
   public const SUPER_USER  = 0x0001;
   public const ADMIN       = 0x0002;
   public const OPERATOR    = 0x0004;
   public const PART_WASHER = 0x0008;
   
   public $employeeId;
   
   public $username;
      
   public $firstName;
   
   public $lastName;
   
   public $permissions;
   
   private function __construct($employeeId)
   {
      this.$employeeId = $employeeId;
   }
   
   private function __construct($employeeId, $username, $firstName, $lastName, $permissions)
   {
      this.$employeeId = $employeeId;
      this.$username = $username;
      this.$firstName = $firstName;
      this.$lastName = $lastName;
      this.$permissions = $permissions;
   }
}

$user = User::getUser($employeeId)

?>