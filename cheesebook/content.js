function post(userId, inputId)
{
   input = document.getElementById(inputId);
   if (input)
   {
	   content = input.value;
	   
	   //
	   // Ajax request to create new post.
	   //
	   
	   var xhttp = new XMLHttpRequest();
	   
	   xhttp.onreadystatechange = function() {
	     if (this.readyState == 4 && this.status == 200) {
	       insertNewPost(this.responseText);
	     }
	   };
	   
	   var request = "post.php?action=post&userId=" + userId + "&content=" + content;
	   xhttp.open("GET", request, true);
	   
	   xhttp.send();
   }
}

function comment(userId, postId, inputId)
{
   input = document.getElementById(inputId);
   if (input)
   {
      content = input.value;
      
      //
      // Ajax request to create new post.
      //
      
      var xhttp = new XMLHttpRequest();
      
      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          updatePost(postId, this.responseText);
        }
      };
      
      var request = "post.php?action=comment&userId=" + userId + "&postId=" + postId + "&content=" + content;
      xhttp.open("GET", request, true);
      
      xhttp.send();
   }
}

function htmlToElement(htmlString)
{
   return (document.createRange().createContextualFragment(htmlString));
}

function insertAfter(node, referenceNode)
{
   referenceNode.parentNode.insertBefore(node, referenceNode.nextSibling);
}

function insertNewPost(postHtml)
{
	var newPostDiv = document.getElementById('new-post-div');

	insertAfter(htmlToElement(postHtml), newPostDiv);
}

function updatePost(postId, postHtml)
{
   var divId = "post-" + postId + "-div";
   
   var div = document.getElementById(divId);

   div.parentNode.replaceChild(htmlToElement(postHtml), div);
}

function like(userId, postId)
{
   //
   // Ajax request to like post.
   //
   
   var xhttp = new XMLHttpRequest();
   
   xhttp.onreadystatechange = function() {
     if (this.readyState == 4 && this.status == 200) {
       updatePost(postId, this.responseText);
     }
   };
   
   var request = "post.php?action=like&userId=" + userId + "&postId=" + postId;
   xhttp.open("GET", request, true);
   
   xhttp.send();
}

function checkEnter(event)
{
   var characterCode;
   
   if (event && event.which)
   {
      characterCode = event.which //character code is contained in NN4's which property
   }
   else
   {
      characterCode = event.keyCode //character code is contained in IE's keyCode property
   }

   return (characterCode == 13);
}
