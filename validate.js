function IntValidator(inputId, maxLength, minValue, maxValue, allowNull)
{
   this.inputId = inputId;
   this.maxLength = maxLength;
   this.minValue = minValue;
   this.maxValue = maxValue;
   this.allowNull = allowNull;
   
   IntValidator.prototype.init = function()
   {
      var element = document.getElementById(this.inputId);
      
      if (element)
      {
         element.maxLength = this.maxLength;
         element.min = this.minValue;
         element.max = this.maxValue;
         
         element.validator = this;
      }
   }
   
   IntValidator.prototype.isValid = function()
   {
      var valid = false;
   
      var element = document.getElementById(this.inputId);
      
      if (element)
      {
         var value = element.value;
         
         if ((value == null) || (value == "")) 
         {
            valid = this.allowNull;
         }
         else
         {
            valid = !(isNaN(value) || 
                      (parseInt(value) < this.minValue) || 
                      (parseInt(value) > this.maxValue));
         }
      }
      
      return (valid);
   }
   
   IntValidator.prototype.color = function(color)
   {
      var element = document.getElementById(this.inputId);
      
      if (element)
      {
         element.style.color = color;
      }
   }
   
   IntValidator.prototype.validate = function()
   {
      var valid = this.isValid();
      
      if (valid)
      {
         this.color("#000000");
      }
      else
      {
         this.color("#FF0000");
      }

      return (valid);
   }
}
   