<?php
require_once 'database.php';
require_once 'post.php';

class ContentPage
{
   public static function render()
   {
      ContentPage::renderHeader();
      ContentPage::renderBody();
   }
  
   private static function renderHeader()
   {
      $userId = $_GET["userId"];
      
      $database = new CheeseBookDatabase("localhost", "root", "", "cheesebook");
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $row = $database->getUser($userId);
         $userName= $row["firstName"] . " " . $row["lastName"];
         $imageFile = $row["imageFile"];
      
      echo
<<<HEREDOC
      <div class="header-div">
         <div><img width="30" src="./images/cheesebooklogo_white.png"/></div>
         <span class="page-title">Cheesebook</span>
         <div style="display:flex; align-items:center; margin-left: auto; padding-right:50px">
            <div style="margin:5px"><img width="30" src="./images/$imageFile"/></div>
            <span>$userName | <a href="login.php">Logout</a></span>
         </div>
      </div>
HEREDOC;
      }
   }
      
   private static function renderBody()
   {
      echo
<<<HEREDOC
      <div class="body-div">
HEREDOC;

      ContentPage::renderLeftSidebar();
      ContentPage::renderPosts();
      ContentPage::renderRightSidebar();
      echo
<<<HEREDOC
      </div>
HEREDOC;
      
   }
   
   private static function renderLeftSidebar()
   {
      echo
<<<HEREDOC
      <div class="sidebar-div">
         <div class="ad-div">Your ad here</div>
         <div class="ad-div">Your ad here</div>
         <div class="ad-div">Your ad here</div>
      </div>
HEREDOC;
   }
   
   private static function renderRightSidebar()
   {
      echo
<<<HEREDOC
      <div class="sidebar-div">
         <div class="ad-div">Your ad here</div>
         <div class="ad-div">Your ad here</div>
         <div class="ad-div">Your ad here</div>
      </div>
HEREDOC;
   }
   
   private static function renderPosts()
   {
      echo
<<<HEREDOC
      <div class="posts-div">
HEREDOC;

      ContentPage::renderNewPost();
      
      $database = new CheeseBookDatabase("localhost", "root", "", "cheesebook");
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $posts = $database->getPosts();
         
         while ($row = $posts->fetch_assoc())
         {
            $postId = $row['id'];
            echo getPostHtml($postId);
         }
      }

      echo
<<<HEREDOC
      </div>
HEREDOC;
   }
      
   private static function renderNewPost()
   {
      $userId = $_GET["userId"];
      
      $database = new CheeseBookDatabase("localhost", "root", "", "cheesebook");
      
      $database->connect();
      
      if ($database->isConnected())
      {
         $row = $database->getUser($userId);
         $imageFile = $row["imageFile"];
         
         // TODO: Check out this when you want to make the text input area grow.
         
         echo
<<<HEREDOC
         <div class="post-div vertical-flex" id="new-post-div">
            <div class="horizontal-flex">
               <div style="margin:5px"><img width="50" src="./images/$imageFile"/></div>
               <input class="new-post-input" type="text" id="new-post-input" placeholder="What's your cheese?"/>
            </div>
            <div class="horizontal-flex post-button-div" style="justify-content:flex-end;">
               <button class="post-button" onclick="post($userId, 'new-post-input')">Post</button>
            </div>
         </div>
HEREDOC;
      }
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
   <link rel="stylesheet" type="text/css" href="content.css"/>
   <script src="content.js"></script>
</head>

<body>
   <?php ContentPage::render()?>
</body>
</html>