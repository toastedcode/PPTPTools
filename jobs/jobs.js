function onNewJob()
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'jobs.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'new_job');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'new_job');
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();    
}

function onDeleteJob(jobNumber)
{
   if (confirm("Are you sure you want to delete this job?"))
   {
      form = document.createElement('form');
      form.setAttribute('method', 'POST');
      form.setAttribute('action', 'jobs.php');
      
      input = document.createElement('input');
      input.setAttribute('name', 'action');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', 'delete_job');
      form.appendChild(input);
      
      input = document.createElement('input');
      input.setAttribute('name', 'jobNumber');
      input.setAttribute('type', 'hidden');
      input.setAttribute('value', jobNumber);
      form.appendChild(input);
      
      document.body.appendChild(form);
      form.submit();
   }
}

function onViewJob(jobNumber)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'jobs.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'view_job');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'jobNumber');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', jobNumber);
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();
}

function onEditJob(jobNumber)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'jobs.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'edit_job');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'edit_job');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'jobNumber');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', jobNumber);
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();
}

function validateJob()
{
   return (true);
}

function autoFillPartNumber()
{
   jobNumberInput = document.getElementById('job-number-prefix-input');
   partNumberInput = document.getElementById('part-number-input');
   
   if (jobNumberInput && partNumberInput)
   {
      partNumberInput.value = jobNumberInput.value;
   }
}