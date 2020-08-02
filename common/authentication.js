function login(loginFormId, onSuccess, onFailure)
{
   var form = document.getElementById(loginFormId);
   
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
            onSuccess();
         }
         else
         {
            onFailure(json.error);
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
     // TODO.
   });

   // Set up our request
   requestUrl = "../api/login/"
   xhttp.open("POST", requestUrl, true);

   // The data sent is what the user provided in the form
   xhttp.send(formData);
}