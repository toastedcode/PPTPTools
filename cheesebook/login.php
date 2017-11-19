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
            
            echo
<<<HEREDOC
            <div class="user-div" onclick="location.href='content.php?userId=$userId'">
               <img width="50" height="50" src="./images/$imageFile"/>
               <span>$userName</span>
            </div>
HEREDOC;
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
            <div style="display:flex; flex-direction:column; background:#fefefe; justify-content:center; width:400px; box-shadow:5px 5px 3px #888888;">
               <img width="400" src="./images/board.jpg"/>
               <div style="padding-left:20px;">
                  <h1>Lunchtime Charcuterie Board</h1>
                  <div style="padding-bottom:20px;">Join us for ...</div>
                  <div style="padding-bottom:20px;">
                     <list>
                        <li>item</li>
                        <li>item</li>
                        <li>item</li>
                        <li>item</li>
                     </list>
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
   <link rel="stylesheet" type="text/css" href="cheesebook.css"/>
   <link rel="stylesheet" type="text/css" href="login.css"/>
</head>

<body>
   <?php LoginPage::render()?>
</body>
</html>