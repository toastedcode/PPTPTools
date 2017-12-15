<?php

require_once '../database.php';

if (isset($_GET["wcNumber"]))
{
   $wcNumber = $_GET["wcNumber"];
   
   $database = new PPTPDatabase();
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $result = $database->getSensorForWorkcenter($wcNumber);

      if ($result)
      {
         $row = $result->fetch_assoc();
         
         echo (json_encode($row));
      }
   }
}