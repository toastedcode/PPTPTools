function onDeletePartWeightEntry(partWeightEntryId)
{
   if (confirm("Are you sure you want to delete this log entry?"))
   {
      form = document.createElement('form');
      form.setAttribute('method', 'POST');
      form.setAttribute('action', 'partWeightLog.php');
      
      input = document.createElement('input');
      input.setAttribute('name', 'action');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', 'delete_part_weight_entry');
      form.appendChild(input);
      
      input = document.createElement('input');
      input.setAttribute('name', 'partWeightEntryId');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', partWeightEntryId);
      form.appendChild(input);
      
      document.body.appendChild(form);
      form.submit();
   }
}

function onNewPartWeightEntry()
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'partWeightLog.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'select_entry_method');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'new_part_weight_entry');
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();  	
}

function onCancel()
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'partWeightLog.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'view_part_weight_log');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'cancel_part_weight_entry');
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
   
   if (radioButtons.length == 0)
   {
      valid = true;
   }
   else
   {
      for (var i = 0; i < radioButtons.length; i++)
      {
         valid |= radioButtons[i].checked;
      }
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
   
   if (radioButtons.length == 0)
   {
      valid = true;
   }
   else
   {
      for (var i = 0; i < radioButtons.length; i++)
      {
         valid |= radioButtons[i].checked;
      }
   }
   
   if (!valid)
   {
      alert("Please select a job.")
   }
   
   return (valid);
}

function validatePanCount()
{
   var valid = false;

   if (!(document.getElementById("panCount-input").validator.validate()))
   {
      alert("Please enter a valid pan count.")
   }
   else
   {
      valid = true;
   }

   return (valid);
}

function validateWeight()
{
   var valid = false;

   if (!(document.getElementById("weight-input").validator.validate()))
   {
      alert("Please enter a valid weight.")
   }
   else
   {
      valid = true;
   }

   return (valid);
}