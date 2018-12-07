function PanCountValidator(page, jobId, panCount, onReply)
{
   this.page = page;
   this.jobId = jobId;
   this.panCount = panCount;
   this.onReply = onReply;
   
   PanCountValidator.prototype.init = function()
   {
      var element = document.getElementById(this.inputId);
   }
   
   PanCountValidator.prototype.color = function(color)
   {
      var element = document.getElementById(this.inputId);
      
      if (element)
      {
         element.style.color = color;
      }
   }
   
   PanCountValidator.prototype.validate = function()
   {
      requestURl = "../common/validatePanCount.php?jobId=" + this.jobId + "&panCount=" + this.panCount + "&page=" + this.page;  // TODO: Figure out correct way to do relative paths.
      
      var xhttp = new XMLHttpRequest();
      xhttp.validator = this;
      xhttp.onreadystatechange = function()
      {
         if (this.readyState == 4 && this.status == 200)
         {
            //alert(this.responseText);
            var response = JSON.parse(this.responseText);
                           
            this.validator.onValidationReply(response.isValidPanCount, response.otherPanCount);
         }
      };
      
      xhttp.open("GET", requestURl, true);
      xhttp.send(); 
   }
   
   PanCountValidator.prototype.onValidationReply = function(isValidPanCount, otherPanCount)
   {
      if (this.onReply)
      {
         onReply(isValidPanCount, otherPanCount);
      }
   }
}