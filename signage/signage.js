function onSaveSign()
{
   if (validateSign())
   {
      var form = document.querySelector('#input-form');
      
      var xhttp = new XMLHttpRequest();
   
      // Bind the form data.
      var formData = new FormData(form);
   
      // Define what happens on successful data submission.
      xhttp.addEventListener("load", function(event) {
         try
         {
            var json = JSON.parse(event.target.responseText);
   
            if (json.success == true)
            {
               location.href = "viewSigns.php";
            }
            else
            {
               alert(json.error);
            }
         }
         catch (expection)
         {
            console.log("JSON syntax error");
            console.log(this.responseText);
         }
      });
   
      // Define what happens on successful data submission.
      xhttp.addEventListener("error", function(event) {
         console.log("saveSign: Failed to contact server.");
      });
   
      // Set up our request
      requestUrl = "../api/saveSign/"
      xhttp.open("POST", requestUrl, true);
   
      // The data sent is what the user provided in the form
      xhttp.send(formData);
   }
}

function onDeleteSign(signId)
{
   if (confirm("Are you sure you want to delete this sign?"))
   {
      // AJAX call to delete part weight entry.
      requestUrl = "../api/deleteSign/?signId=" + signId;
      
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function()
      {
         if (this.readyState == 4 && this.status == 200)
         {         
            try
            {
               var json = JSON.parse(this.responseText);
               
               if (json.success == true)
               {
                  location.href = "viewSigns.php";
               }
               else
               {
                  console.log("API call to delete sign failed.");
                  alert(json.error);
               }
            }
            catch (expection)
            {
               console.log("JSON syntax error");
               console.log(this.responseText);
            }
         }
      };
      xhttp.open("GET", requestUrl, true);
      xhttp.send(); 
   }
}

function validateSign()
{
   valid = false;

   if (document.getElementById("sign-name-input").value == "")
   {
      alert("Please enter a valid sign name.");
   }
   else if (document.getElementById("sign-description-input").value == "")
   {
      alert("Please enter a valid sign description.");
   }
   else if (document.getElementById("sign-url-input").value == "")
   {
      alert("Please enter a valid sign URL.");
   }
   else
   {
      valid = true;
   }
   
   return (valid);
}
