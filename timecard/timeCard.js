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
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'view_time_card');
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

function onCancel()
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'timeCard.php');
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'show_time_cards');
   form.appendChild(input);
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'cancel_time_card');
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

function validateWorkCenter()
{
   radioButtons = document.getElementsByName("wcNumber"); 
   
   var valid = false;
   
   for (var i = 0; i < radioButtons.length; i++)
   {
      valid |= radioButtons[i].checked;
   }
   
   if (!valid)
   {
      alert("Please select a work center.")
   }
   
   return (valid);
}

function validateJob()
{
   return true;  // TODO: Remove!
   var valid = false;

   value = document.getElementById("jobNumber-input").value;
   
   valid = !((value == null) || (value == "") || isNaN(value));
   
   if (!valid)
   {
      alert("Please enter a valid job number.")
   }
   
   return (valid);
}

function validateTime()
{
   return true;  // TODO: Remove!

   var valid = false;
   
   hours = document.getElementById("setupTimeHour-input").value;
   minutes = document.getElementById("setupTimeMinute-input").value;
   
   valid = !((hours == 0) && (minutes == 0));
   
   if (!valid)
   {
      alert("Please enter a valid setup time.")
   }
   else
   {
      hours = document.getElementById("runTimeHour-input").value;
      minutes = document.getElementById("runTimeMinute-input").value;
      
      valid = !((hours == 0) && (minutes == 0));
   
      if (!valid)
      {
         alert("Please enter a valid run time.")
      }
   }
   
   return (valid);
}

function validatePartCount()
{
   var valid = false;

   value = document.getElementById("panCount-input").value;
   
   valid = !((value == null) || (value == "") || (value == 0));
   
   if (!valid)
   {
      alert("Please enter a valid pan count.")
   }
   else
   {
      value = document.getElementById("partsCount-input").value + 
              document.getElementById("scrapCount-input").value;
      
      valid = (value > 0);
      
      if (!valid)
      {
         alert("Please enter a valid parts count.")
      }
   }

   return (valid);
}