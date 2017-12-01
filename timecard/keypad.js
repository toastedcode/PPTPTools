function initKeypad()
{
   var keys = document.getElementsByClassName('keypadKey');
   for (var i = 0; i < keys.length; i++)
   {
      var key = keys[i];

      // Set onclick function to handle keypad presses.
      key.onclick = function()
      {
         onKeypadPressed(this)
      };

      // Set onmousedown function to keep it from stealing focus when pressed.
      key.onmousedown = function(event)
      {
    	  event.preventDefault();
      }
   }
}

function onKeypadPressed(key)
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
	      else
	      {
	         ae.value += keyValue;
	      }
	   }
   }
}