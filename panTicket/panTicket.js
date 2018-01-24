function onDeletePanTicket(panTicketId)
{
   if (confirm("Are you sure you want to delete this pan ticket?"))
   {
      form = document.createElement('form');
      form.setAttribute('method', 'POST');
      form.setAttribute('action', 'panTicket.php');
      
      input = document.createElement('input');
      input.setAttribute('name', 'action');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', 'delete_pan_ticket');
      form.appendChild(input);
      
      input = document.createElement('input');
      input.setAttribute('name', 'panTicketId');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', panTicketId);
      form.appendChild(input);
      
      document.body.appendChild(form);
      form.submit();
   }
}

function onViewPanTicket(panTicketId)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'panTicket.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'view_pan_ticket');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'panTicketId');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', panTicketId);
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();
}

function onEditPanTicket(panTicketId)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'panTicket.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'edit_pan_ticket');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'edit_pan_ticket');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'panTicketId');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', panTicketId);
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();
}

function onPrintPanTicket(panTicketId)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'printPanTicketPage.php');
   form.setAttribute("target", "_blank");
   
   input = document.createElement('input');
   input.setAttribute('name', 'panTicketId');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', panTicketId);
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();    
}

function onNewPanTicket()
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'panTicket.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'select_operator');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'new_pan_ticket');
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();  	
}

function onEnterWeight()
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'panTicket.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'select_pan_ticket');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'no_action');
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();    
}

function onCancel()
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'panTicket.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'show_pan_tickets');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'cancel_pan_ticket');
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

function validateOperator()
{
   radioButtons = document.getElementsByName("employeeNumber"); 
   
   var valid = false;
   
   for (var i = 0; i < radioButtons.length; i++)
   {
      valid |= radioButtons[i].checked;
   }
   
   if (!valid)
   {
      alert("Please select an operator.")
   }
   
   return (valid);
}

function validatePartNumber()
{
   valid = false;

   if (!(document.getElementById("partNumber-input").validator.validate()))
   {
      alert("Please enter a valid part number.")      
   }
   else
   {
      valid = true;
   }
   
   return (valid);   
}

function validateMaterialNumber()
{
   valid = false;

   if (!(document.getElementById("materialNumber-input").validator.validate()))
   {
      alert("Please enter a valid heat number.")      
   }
   else
   {
      valid = true;
   }
   
   return (valid);    
}

function validatePanTicket()
{
   return (validatePartNumber() && validateMaterialNumber());
}

function filterToday()
{
   var startDateInput = document.querySelector('#startDateInput');
   var endDateInput = document.querySelector('#endDateInput');
   
   if ((startDateInput != null) && (endDateInput != null))
   {
      var today = new Date();
      
      startDateInput.value = formattedDate(today); 
      endDateInput.value = formattedDate(today);
   }
}

function filterYesterday()
{
   var startDateInput = document.querySelector('#startDateInput');
   var endDateInput = document.querySelector('#endDateInput');
   
   if ((startDateInput != null) && (endDateInput != null))
   {
      var yesterday = new Date();
      yesterday.setDate(yesterday.getDate() - 1);
      
      startDateInput.value = formattedDate(yesterday); 
      endDateInput.value = formattedDate(yesterday);
   }
}

function filterThisWeek()
{
   var startDateInput = document.querySelector('#startDateInput');
   var endDateInput = document.querySelector('#endDateInput');
   
   if ((startDateInput != null) && (endDateInput != null))
   {
      var today = new Date();
      var startOfWeek = new Date();
      startOfWeek.setDate(today.getDate() - today.getDay());
      
      startDateInput.value = formattedDate(startOfWeek); 
      endDateInput.value = formattedDate(today);
   }
}

function formattedDate(date)
{
   // Convert to Y-M-D format, per HTML5 Date control.
   // https://stackoverflow.com/questions/12346381/set-date-in-input-type-date
   var day = ("0" + date.getDate()).slice(-2);
   var month = ("0" + (date.getMonth() + 1)).slice(-2);
   
   var formattedDate = date.getFullYear() + "-" + (month) + "-" + (day);

   return (formattedDate);
}