<?php
require_once 'database.php';

function getPostHtml($postId)
{
   $htmlString = "";
   
   $userId = $_GET["userId"];
   
   $database = new CheeseBookDatabase("localhost", "root", "", "cheesebook");
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $post = $database->getPost($postId);
      $user = $database->getUser($post["userId"]);
      $userName= $user["firstName"] . " " . $user["lastName"];
      $imageFile = $user["imageFile"];
      $dateTime = $post["dateTime"];
      $content = $post["content"];
      
      $dateString = "3 hours ago";  // TODO
      
      $likes = $database->getLikes($postId);
      $likeCount = mysqli_num_rows($likes);
      
      /*
      if ($likeCount > 2)
      {
         for (i = 0; i < 1; i++)
         {
            $row = mysql_fetch_assoc($result);
            
            $user = $database->getUser($row["userId"]);
            $userName = $user["firstName"] . " " . $user["lastName"];
            
            $likeCountText .= $userName;
            if (i < 1)
            {
               $likeCountText .= ", ";
            }
         }
         $likeCountText = ""
      }
      */
      
      $likeCountDiv = "";
      if ($likeCount > 0)
      {
         $likeCountDiv = 
<<<HEREDOC
         <div class="horizontal-flex like-count-div">
            <div style="margin-right:10px;"><img src="./images/cheddar-small.png" width="30"/></div>
            <div>$likeCount</div>
         </div>
HEREDOC;
      }
      
      $htmlString =
<<<HEREDOC
         <div class="post-div vertical-flex">
            <div class="horizontal-flex">
               <div style="margin:5px"><img width="50" src="./images/$imageFile"/></div>
               <div class="vertical-flex">
                  <span class="post-author">$userName</span>
                  <span class="post-date">$dateString</span>
               </div>
            </div>
            <div class="vertical-flex">$content</div>
            <div class="horizontal-flex like-div" onclick="like($userId, $postId)">
               <div style="margin-right:10px;"><img src="./images/cheddar.png" width="30"/></div>
               <a href="">Give cheddar</a>
            </div>
            $likeCountDiv
         </div>
HEREDOC;
   }
      
   return ($htmlString);
}

// Add post
if (isset($_GET["userId"]) && isset($_GET["content"]))
{
  $userId = $_GET["userId"];
  $content = $_GET["content"];
  
  $database = new CheeseBookDatabase("localhost", "root", "", "cheesebook");
  
  $database->connect();
  
  if ($database->isConnected())
  {
     $result = $database->addPost($userId, $content);
     
     if ($result)
     {
        $postId = $result['id'];
        echo getPostHtml($postId);
     }
  }
}
// Add like
else if (isset($_GET["userId"]) &&  isset($_GET["postId"]))
{
   $userId = $_GET["userId"];
   $postId = $_GET["postId"];
   
   $database = new CheeseBookDatabase("localhost", "root", "", "cheesebook");
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $isLiked = $database->isLiked($userId, $postId);
      
      if (!$isLiked)
      {
         $result = $database->addLike($userId, $postId);
      }
      
      echo getPostHtml($postId);
   }
}
?>