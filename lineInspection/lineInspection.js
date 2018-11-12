function onDeleteLineInspectionEntry(entryId)
{
   if (confirm("Are you sure you want to delete this line inspection?"))
   {
      form = document.createElement('form');
      form.setAttribute('method', 'POST');
      form.setAttribute('action', 'lineInspection.php');
      
      input = document.createElement('input');
      input.setAttribute('name', 'action');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', 'delete_line_inspection');
      form.appendChild(input);
      
      input = document.createElement('input');
      input.setAttribute('name', 'entryId');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', entryId);
      form.appendChild(input);
      
      document.body.appendChild(form);
      form.submit();
   }
}

function onNewLineInspection()
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'lineInspection.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'new_line_inspection');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'new_line_inspection');
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();  	
}

function onCancel()
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'lineInspection.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'view_line_inspections');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'cancel_line_inspection');
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();  	
}

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