<?php
require_once 'database.php';

class LoginPage
{
   public static function render()
   {
      LoginPage::renderHeader();
      LoginPage::renderLogin();
      LoginPage::renderBody();
   }
   
   private static function renderHeader()
   {
      echo
<<<HEREDOC
      <div class="header-div">
         <img width="100" src="./images/cheesebooklogo.png"/>
         <span class="page-title">Cheesebook</span>
      </div>
HEREDOC;
   }
   
   private static function renderLogin()
   {
      echo
<<<HEREDOC
      <div class="login-div">
         <span class="login-title">cheesebook</span>
HEREDOC;
      LoginPage::renderSelectUserInput();
      
      echo
<<<HEREDOC
      </div>
HEREDOC;
   }
      
   private static function renderSelectUserInput()
   {
      $database = new CheeseBookDatabase("localhost", "root", "", "cheesebook");
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $result = $database->getUsers();
         
         echo
<<<HEREDOC
         <div class="select-user-dropdown">
            <button class="select-user-button">Login</button>
            <div class="select-user-dropdown-content">
HEREDOC;

         while($row = $result->fetch_assoc())
         {
            $userId= $row["id"];
            $userName= $row["firstName"] . " " . $row["lastName"];
            $imageFile = $row["imageFile"];
          
            if ($userId != 99)
            {
               echo
<<<HEREDOC
               <div class="user-div" onclick="location.href='content.php?userId=$userId'">
                  <img width="50" height="50" src="./images/$imageFile"/>
                  <span>$userName</span>
               </div>
HEREDOC;
            }
         }

         echo
<<<HEREDOC
            </div>
         </div>
HEREDOC;
      }
   }
      
   private static function renderBody()
   {
      echo
<<<HEREDOC
      <div style="display:flex; flex-direction: row;">
         <div class="login-page-body"  style="flex-grow:1;">
            Connect with friends and get all the latest news on cheese, cheese boards, cheese pairings, and all other cheese related happenings.
         </div>
         <div style="display: flex; flex-grow:2; justify-content:center; padding-top:30px;">
            <div class="vertical-flex cheese-board-div">
               <img width="400" src="./images/board.jpg"/>
               <div style="padding:0px 10px 10px 10px;">
                  <h1>Lunchtime Charcuterie Board</h1>
                  <div style="padding-bottom:20px; font-weight:bold; font-size:20px;">Friday Dec. 15 @ 12pm</div>
                  <div style="padding-bottom:20px;">Join us for a tour-de-tastebud!  Bring an artisan cheese or a cured/smoked meat to share with your Compunetix lunchtime friends.</div>
                  <div class="horizontal-flex">
                     <img width="30" src="./images/cheddar-small.png"/>
                     <div style="padding-left:10px; color:#3B5998; font-weight:bold;">Interested in going? Give this event some cheddar on Cheesebook!</div>
                  </div>
               </div>
            </div>
         </div>
      </div>
HEREDOC;
   }
}
?>

<!DOCTYPE html>
<html>

<head>
   <!--  Favicon, generated at  https://www.favicon-generator.org/ -->
   <link rel="apple-touch-icon" sizes="57x57" href="./images/favicon/apple-icon-57x57.png">
   <link rel="apple-touch-icon" sizes="60x60" href="./images/favicon//apple-icon-60x60.png">
   <link rel="apple-touch-icon" sizes="72x72" href="./images/favicon//apple-icon-72x72.png">
   <link rel="apple-touch-icon" sizes="76x76" href="./images/favicon//apple-icon-76x76.png">
   <link rel="apple-touch-icon" sizes="114x114" href="./images/favicon//apple-icon-114x114.png">
   <link rel="apple-touch-icon" sizes="120x120" href="./images/favicon//apple-icon-120x120.png">
   <link rel="apple-touch-icon" sizes="144x144" href="./images/favicon//apple-icon-144x144.png">
   <link rel="apple-touch-icon" sizes="152x152" href="./images/favicon//apple-icon-152x152.png">
   <link rel="apple-touch-icon" sizes="180x180" href="./images/favicon//apple-icon-180x180.png">
   <link rel="icon" type="image/png" sizes="192x192"  href="./images/favicon//android-icon-192x192.png">
   <link rel="icon" type="image/png" sizes="32x32" href="./images/favicon//favicon-32x32.png">
   <link rel="icon" type="image/png" sizes="96x96" href="./images/favicon//favicon-96x96.png">
   <link rel="icon" type="image/png" sizes="16x16" href="./images/favicon//favicon-16x16.png">
   <link rel="manifest" href="./images/favicon//manifest.json">
   <meta name="msapplication-TileColor" content="#ffffff">
   <meta name="msapplication-TileImage" content="./images/favicon//ms-icon-144x144.png">
   <meta name="theme-color" content="#ffffff">
   
   <link rel="stylesheet" type="text/css" href="cheesebook.css"/>
   <link rel="stylesheet" type="text/css" href="login.css"/>
   
   <!--  Google fonts -->
   <link href="https://fonts.googleapis.com/css?family=Shrikhand" rel="stylesheet">
</head>

<body>
   <?php LoginPage::render()?>
</body>
</html>