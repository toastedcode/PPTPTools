function onNewUser()
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'user.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'new_user');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'new_user');
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();    
}

function onDeleteUser(employeeNumber)
{
   if (confirm("Are you sure you want to delete this user?"))
   {
      form = document.createElement('form');
      form.setAttribute('method', 'POST');
      form.setAttribute('action', 'user.php');
      
      input = document.createElement('input');
      input.setAttribute('name', 'action');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', 'delete_user');
      form.appendChild(input);
      
      input = document.createElement('input');
      input.setAttribute('name', 'employeeNumber');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', employeeNumber);
      form.appendChild(input);
      
      document.body.appendChild(form);
      form.submit();
   }
}

function onViewUser(employeeNumber)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'user.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'view_user');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'employeeNumber');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', employeeNumber);
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();
}

function onEditUser(employeeNumber)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'user.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'edit_user');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'edit_user');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'employeeNumber');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', employeeNumber);
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();
}

function validateUser()
{
   valid = false;

   if (!(document.getElementById("employee-number-input").validator.validate()))
   {
      alert("Please enter a valid employee number.");      
   }
   /*
   else if (!(document.getElementById("username-input").validator.validate()))
   {
      alert("Please enter a valid username.");      
   }
   else if (!(document.getElementById("first-name-input").validator.validate()))
   {
      alert("Please enter a valid first name.");      
   }
   else if (!(document.getElementById("last-name-input").validator.validate()))
   {
      alert("Please enter a valid first name.");      
   }
   */
   else
   {
      valid = true;
   }
   
   return (valid);
}