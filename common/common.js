function submitForm(form, page, view, action)
{
   //alert(form + ", " + page + ", " + view + ", " + action);
   
   if (!form)
   {
      form = document.createElement('form');
      form.setAttribute('method', 'POST');
      document.body.appendChild(form);
   }
   else
   {
      form = document.getElementById(form);
   }
   
   form.setAttribute('action', page);
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', view);
   form.appendChild(input);

   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', action);
   form.appendChild(input);
   
   form.submit();
}

function hide(elementId)
{
   document.getElementById(elementId).style.display = "none";
}

function show(elementId, display)
{
   document.getElementById(elementId).style.display = display;
}

function set(elementId, value)
{
   document.getElementById(elementId).value = value;
}

function clear(elementId)
{
   document.getElementById(elementId).value = null;
}

function enable(elementId)
{
   document.getElementById(elementId).disabled = false;
}

function disable(elementId)
{
   document.getElementById(elementId).disabled = true;
}

function isEnabled(elementId)
{
   return (document.getElementById(elementId).disabled == false);
}

function validate(elementId)
{
   return (document.getElementById(elementId).validator.validate());
}

function preserveSession()
{
   setInterval(function(){ 
      // AJAX call to populate WC numbers based on selected job number.
      requestUrl = "../api/ping/";
   
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
                  console.log("Session preserved.");
               }
               else
               {
                  console.log("API call to preserve session failed.");
               }
            }
            catch (exception)
            {
               console.log("JSON syntax error");
               console.log(this.responseText);
            }
         }
      };
      xhttp.open("GET", requestUrl, true);
      xhttp.send();
   }, 60000);
}

function copyToClipboard(elementId)
{
   var element = document.getElementById(elementId);
   
   element.focus();
   element.select();
   element.setSelectionRange(0, 99999);  // For mobile devices

   document.execCommand("copy");
   
   alert("Copied " + element.value);
}