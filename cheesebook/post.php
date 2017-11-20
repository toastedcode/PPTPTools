<?php
require_once 'database.php';

function getUserName($userId)
{
   $userName = "";
   
   $database = new CheeseBookDatabase("localhost", "root", "", "cheesebook");
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $row = $database->getUser($userId);
      $userName= $row["firstName"] . " " . $row["lastName"];
   }
   
   return ($userName);
}

function getUserImage($userId)
{
   $userImage = "";
   
   $database = new CheeseBookDatabase("localhost", "root", "", "cheesebook");
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $row = $database->getUser($userId);
      $userImage = $row["imageFile"];
   }
   
   return ($userImage);
}

function getLikeText($postId)
{
   $likeText = "";
   
   $database = new CheeseBookDatabase("localhost", "root", "", "cheesebook");
   
   $database->connect();
   
   if ($database->isConnected())
   {
      $likes = $database->getLikes($postId);
      $likeCount = mysqli_num_rows($likes);
      
      if ($likeCount > 0)
      {
         // Jason Tost
         if ($likeCount == 1)
         {
            $row = mysqli_fetch_assoc($likes);
            $userId = $row["userId"];
            $likeText = getUserName($userId);
         }
         // Jason Tost and Michael Crowe
         else if ($likeCount == 1)
         {
            $row = mysqli_fetch_assoc($likes);
            $userId = $row["userId"];
            
            $likeText .= getUserName($userId) . " and ";
            
            $row = mysqli_fetch_assoc($likes);
            $userId = $row["userId"];
            
            $likeText .= getUserName($userId);
         }
         // Jason Tost, Michael Crowe, and 3 others.
         else
         {
            $row = mysqli_fetch_assoc($likes);
            $userId = $row["userId"];
            
            $likeText .= getUserName($userId) . ", ";
            
            $row = mysqli_fetch_assoc($likes);
            $userId = $row["userId"];
            
            $likeText .= getUserName($userId) . ", and " . $likeCount . " others.";
         }
      }
   }
   
   return ($likeText);
}

function getPostHtml($postId)
{
   $htmlString = "";
   
   $userId = $_GET["userId"];
   $userImage = getUserImage($userId);
   
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
      $likeText = getLikeText($postId);
      
      $comments = $database->getComments($postId);
      $commentCount = mysqli_num_rows($comments);
      
      $likeCountDiv = "";
      if ($likeCount > 0)
      {
         $likeCountDiv = 
<<<HEREDOC
         <div class="horizontal-flex like-count-div">
            <div style="margin-right:10px;"><img src="./images/cheddar-small.png" width="30"/></div>
            <div>$likeText</div>
         </div>
HEREDOC;
      }
         
      $commentsDivs = "";
      if ($commentCount > 0)
      {
         while ($row = mysqli_fetch_assoc($comments))
         {
            $commentUserId = $row["userId"];
            $commentUserName = getUserName($commentUserId);
            $commentUserImage = getUserImage($commentUserId);
            $commentContent = $row["content"];
         
            $commentsDivs .=
<<<HEREDOC
            <div class="horizontal-flex comment-div">
               <div style="margin:5px"><img width="30" src="./images/$commentUserImage"/></div>
               <div><b>$commentUserName</b> $commentContent</div>
            </div>
HEREDOC;
         }
      }
      
      $htmlString =
<<<HEREDOC
         <div class="post-div vertical-flex" id="post-$postId-div">
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
            $commentsDivs
            <div class="horizontal-flex comment-div">
               <div style="margin:5px"><img width="30" src="./images/$userImage"/></div>
               <input class="comment-input" type="text" id="comment-$postId-input" placeholder="Leave a comment." onkeypress="if (checkEnter(event) == true) comment($userId, $postId, 'comment-$postId-input')"/>
            </div>
         </div>
HEREDOC;
   }
      
   return ($htmlString);
}

// Add post
if (isset($_GET["action"]))
{
   $action = $_GET["action"];
   
   $database = new CheeseBookDatabase("localhost", "root", "", "cheesebook");
   
   $database->connect();
   
   if ($database->isConnected())
   {
      switch ($action)
      {
         case "post":
         {
            if (isset($_GET["userId"]) && isset($_GET["content"]))
            {
               $userId = $_GET["userId"];
               $content = $_GET["content"];
               
               $result = $database->addPost($userId, $content);
               
               if ($result)
               {
                  echo getPostHtml($result["id"]);
               }
            }
            break;
         }
         
         case "comment":
         {
            if (isset($_GET["userId"]) && isset($_GET["postId"]) && isset($_GET["content"]))
            {
               $userId = $_GET["userId"];
               $postId = $_GET["postId"];
               $content = $_GET["content"];
               
               $result = $database->addComment($userId, $postId, $content);
               
               if ($result)
               {
                  echo getPostHtml($postId);
               }
            }
            break;
         }
         
         case "like":
         {
            $userId = $_GET["userId"];
            $postId = $_GET["postId"];
            
            if (isset($_GET["userId"]) &&  isset($_GET["postId"]))
            {
               $isLiked = $database->isLiked($userId, $postId);
               
               if (!$isLiked)
               {
                  $result = $database->addLike($userId, $postId);
               }
               
               echo getPostHtml($postId);
            }
            break;
         }
         
         default:
         {
            break;         
         }
      }
   }
}
?>