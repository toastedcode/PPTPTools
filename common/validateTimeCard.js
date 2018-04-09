function TimeCardIdValidator(inputId)
{
   this.inputId = inputId;
   
   TimeCardIdValidator.prototype.init = function()
   {
      var element = document.getElementById(this.inputId);
      
      if (element)
      {
         element.validator = this;
      }
   }
   
   TimeCardIdValidator.prototype.color = function(color)
   {
      var element = document.getElementById(this.inputId);
      
      if (element)
      {
         element.style.color = color;
      }
   }
   
   TimeCardIdValidator.prototype.validate = function()
   {
      var element = document.getElementById(this.inputId);
      
      if (element)
      {
         var timeCardId = element.value;
      
         requestURl = "../common/validateTimeCard.php?timeCardId=" + timeCardId;  // TODO: Figure out correct way to do relative paths.
         
         var xhttp = new XMLHttpRequest();
         xhttp.validator = this;
         xhttp.onreadystatechange = function()
         {
            if (this.readyState == 4 && this.status == 200)
            {
               var response = JSON.parse(this.responseText);
                              
               validator.onValidationReply(response.timeCardId, response.isValidTimeCard, response.timeCardDiv);
            }
         };
         
         xhttp.open("GET", requestURl, true);
         xhttp.send(); 
      }
   }
   
   TimeCardIdValidator.prototype.onValidationReply = function(timeCardId, isValidTimeCard, timeCardDiv)
   {
      if (isValidTimeCard)
      {
         this.color("#000000");
         
         var element = document.getElementById("time-card-div");
         
         if (element)
         {
            element.innerHTML = timeCardDiv;
         }
      }
      else
      {
         this.color("#FF0000");
         
         var element = document.getElementById("time-card-div");
         
         if (element)
         {
            element.innerHTML = "";
         }
      }
   }
}