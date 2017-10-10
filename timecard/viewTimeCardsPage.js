function onDelete(timeCardId)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'timeCard.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'delete_time_card');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'timeCardId');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', timeCardId);
   form.appendChild(input);
   
   employeeNumber = document.getElementById('employeeNumberInput').value;
   input = document.createElement('input');
   input.setAttribute('name', 'employeeNumber');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', employeeNumber);
   form.appendChild(input);
   
   startDate = document.getElementById('startDateInput').value;
   input = document.createElement('input');
   input.setAttribute('name', 'startDate');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', startDate);
   form.appendChild(input);
   
   endDate = document.getElementById('endDateInput').value;
   input = document.createElement('input');
   input.setAttribute('name', 'endDate');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', endDate);
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();   
}

function onEdit(timeCardId)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'timeCard.php');
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'edit_time_card');
   form.appendChild(input);
   input = document.createElement('input');
   input.setAttribute('name', 'timeCardId');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', timeCardId);
   form.appendChild(input);
   document.body.appendChild(form);
   form.submit();  	
}

function onNewTimeCard()
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'timeCard.php');
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'select_operator');
   form.appendChild(input);
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'new_time_card');
   form.appendChild(input);
   document.body.appendChild(form);
   form.submit();  	
}