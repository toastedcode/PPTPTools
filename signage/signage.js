function onNewSign()
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'signage.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'new_sign');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'new_sign');
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();    
}

function onDeleteSign(signId)
{
   if (confirm("Are you sure you want to delete this sign?"))
   {
      form = document.createElement('form');
      form.setAttribute('method', 'POST');
      form.setAttribute('action', 'signage.php');
      
      input = document.createElement('input');
      input.setAttribute('name', 'action');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', 'delete_sign');
      form.appendChild(input);
      
      input = document.createElement('input');
      input.setAttribute('name', 'signId');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', signId);
      form.appendChild(input);
      
      document.body.appendChild(form);
      form.submit();
   }
}

function onViewSign(signId)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'signage.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'view_sign');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'signId');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', signId);
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();
}

function onEditSign(signId)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'signage.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'edit_sign');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'edit_sign');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'signId');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', signId);
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();
}

function validateSign()
{
   valid = false;

   if (!(document.getElementById("sign-name-input").validator.validate()))
   {
      alert("Please enter a valid sign name.");
   }
   else if (!(document.getElementById("sign-description-input").validator.validate()))
   {
      alert("Please enter a valid sign description.");
   }
   else if (!(document.getElementById("sign-url-input").validator.validate()))
   {
      alert("Please enter a valid sign URL.");
   }
   else
   {
      valid = true;
   }
   
   return (valid);
}

function openURL(url)
{
   window.open(url);
}