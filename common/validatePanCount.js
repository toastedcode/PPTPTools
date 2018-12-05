function PanCountValidator(inputId, jobId)
{
   this.inputId = inputId;
   this.jobId = jobId;
   
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
      
         requestURl = "../common/validatePanCount.php?jobId=" + this.jobId + "&panCount=" + element.value;  // TODO: Figure out correct way to do relative paths.
         
         var xhttp = new XMLHttpRequest();
         xhttp.validator = this;
         xhttp.onreadystatechange = function()
         {
            if (this.readyState == 4 && this.status == 200)
            {
               var response = JSON.parse(this.responseText);
                              
               validator.onValidationReply(response.isValidPanCount);
            }
         };
         
         xhttp.open("GET", requestURl, true);
         xhttp.send(); 
      }
   }
   
   TimeCardIdValidator.prototype.onValidationReply = function(isValidPanCount)
   {
      if (isValidPanCount)
      {
         this.color("#000000");
      }
      else
      {
         this.color("#FF0000");
      }
   }
}