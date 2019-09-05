function onDeletePartWasherEntry(partWasherEntryId)
{
   if (confirm("Are you sure you want to delete this log entry?"))
   {
      form = document.createElement('form');
      form.setAttribute('method', 'POST');
      form.setAttribute('action', 'partWasherLog.php');
      
      input = document.createElement('input');
      input.setAttribute('name', 'action');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', 'delete_part_washer_entry');
      form.appendChild(input);
      
      input = document.createElement('input');
      input.setAttribute('name', 'partWasherEntryId');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', partWasherEntryId);
      form.appendChild(input);
      
      document.body.appendChild(form);
      form.submit();
   }
}

function onNewPartWasherEntry()
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'partWasherLog.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'select_entry_method');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'new_part_washer_entry');
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();  	
}

function onViewPartWasherEntry(entryId)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'partWasherLog.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'view_part_washer_entry');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'entryId');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', entryId);
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();
}

function onEditPartWasherEntry(entryId)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'partWasherLog.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'edit_part_washer_entry');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'edit_part_washer_entry');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'entryId');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', entryId);
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();
}

function onCancel()
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'partWasherLog.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'view_part_washer_log');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'cancel_part_washer_entry');
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

function validateEmployeeNumber()
{
   radioButtons = document.getElementsByName("employeeNumber"); 
   
   var valid = false;
   
   for (var i = 0; i < radioButtons.length; i++)
   {
      valid |= radioButtons[i].checked;
   }
   
   if (!valid)
   {
      alert("Please select a parts washer.")
   }
   
   return (valid);
}

function validateTimeCardId()
{
   valid = false;

   if (!(document.getElementById("time-card-id-input").style.color == "rgb(0, 0, 0)"))
   {
      alert("Please enter a valid time card id.")      
   }
   else
   {
      valid = true;
   }
   
   return (valid);
}

function validateWorkCenter()
{
   var valid = false;
   
   radioButtons = document.getElementsByName("wcNumber"); 
   
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
   valid = false;

   radioButtons = document.getElementsByName("jobId");
   
   for (var i = 0; i < radioButtons.length; i++)
   {
      valid |= radioButtons[i].checked;
   }
   
   if (!valid)
   {
      alert("Please select a job.")
   }
   
   return (valid);
}

function validateOperator()
{
   radioButtons = document.getElementsByName("operator"); 
   
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

function onPanCountValidationReply(isValid, otherPanCount)
{
   var element = document.getElementById("pan-count-mismatch-warning")

   if (element)
   {
      // Hide/show the warning message.
      element.style.visibility = (isValid ? "hidden" : "visible");
   }
}

function validatePanCountMatch()
{
   if (document.getElementById("panCount-input").validator.validate())
   {
      var panCount = document.getElementById("panCount-input").value;
      var jobId = document.getElementById("jobId-input").value;
   
      var validator = new PanCountValidator("partWasherLog", jobId, panCount, onPanCountValidationReply);
      
      validator.validate();
   }
   else
   {
      onPanCountValidationReply(true, 0);
   }
}

function validatePartCount()
{
   var valid = false;

   if (!(document.getElementById("panCount-input").validator.validate()))
   {
      alert("Please enter a valid pan count.")
   }
   else if (!(document.getElementById("partCount-input").validator.validate()))
   {
         alert("Please enter a valid part count.");
   }
   else
   {
      valid = true;
   }

   return (valid);
}