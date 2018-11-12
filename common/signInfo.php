<?php
require_once 'database.php';

class SignInfo
{
   const UNKNOWN_SIGN_ID = 0;
   
   public $signId;
   public $name;
   public $description;
   public $url;
   
   public static function load($signId)
   {
      $signInfo = null;
      
      $database = new PPTPDatabase();
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getSign($signId);
         
         if ($result && ($row = $result->fetch_assoc()))
         {
            $signInfo = new SignInfo();
            
            $signInfo->signId = intval($row['signId']);
            $signInfo->name = $row['name'];
            $signInfo->description = $row['description'];
            $signInfo->url = $row['url'];
         }
      }
      
      return ($signInfo);
   }
}

/*
if (isset($_GET["signId"]))
{
   $signId = $_GET["signId"];
   $signInfo = SignInfo::load($signId);
   
   if ($signInfo)
   {
      echo "signId: " .      $signInfo->signId .      "<br/>";
      echo "name: " .        $signInfo->name .        "<br/>";
      echo "description: " . $signInfo->description . "<br/>";
      echo "url: " .         $signInfo->url .         "<br/>";
   }
   else
   {
      echo "No sign info found.";
   }
}
*/
?>