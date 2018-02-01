function Keypad()
{
   this.onEnter = null;
   
   Keypad.prototype.init = function()
   {
      var keys = document.getElementsByClassName('keypad-key');
      
      for (var i = 0; i < keys.length; i++)
      {
         var key = keys[i];
         
         if (!key.classList.contains("disabled"))
         {
            // Set onclick function to handle keypad presses.
            key.onclick = function()
            {
               keypad.onKeypadPressed(this);  // TODO: Figure this out! How to access onKeypadPressed() member function.
            }
         }
   
         // Set onmousedown function to keep it from stealing focus when pressed.
         key.onmousedown = function(event)
         {
           event.preventDefault();
         }
      }
   }
      
   Keypad.prototype.onKeypadPressed = function(key)
   {
      var keyValue = key.innerHTML;
   
      var ae = document.activeElement;
      
      if (ae != null)
      {
   	   if (ae && ae.classList.contains("keypadInputCapable"))
   	   {
   	      if (keyValue == "Clr")
   	      {
   	         ae.value = "";
   	      }
   	      else if (keyValue == "Bksp")
   	      {
   	         ae.value = ae.value.substr(0, (ae.value.length - 1))
   	      }
            else if (keyValue == "Enter")
            {
               if (this.onEnter)
               {
                  eval(this.onEnter);
               }
            }
   	      else if ((typeof ae.maxLength === "undefined") || (ae.value.length < ae.maxLength))
   	      {
   	         ae.value += keyValue;
   	      }
   	      
            if (!(typeof ae.validator === "undefined"))
            {
               ae.validator.validate();
            }
   	   }
      }
   }
}