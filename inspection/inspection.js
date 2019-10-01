function onDeleteLineInspection(entryId)
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

function onViewLineInspection(entryId)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'lineInspection.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'view_line_inspection');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'entryId');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', entryId);
   form.appendChild(input);
   
   document.body.appendChild(form);
   form.submit();
}

function onEditLineInspection(entryId)
{
   form = document.createElement('form');
   form.setAttribute('method', 'POST');
   form.setAttribute('action', 'lineInspection.php');
   
   input = document.createElement('input');
   input.setAttribute('name', 'view');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'edit_line_inspection');
   form.appendChild(input);
   
   input = document.createElement('input');
   input.setAttribute('name', 'action');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', 'edit_line_inspection');
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

function updateWCNumberInput()
{
   input = document.getElementById("job-number-input");
   
   // Retrieve the selected job number.
   jobNumber = input.options[input.selectedIndex].value; 
   
   // Retrieve the enabled/disabled status of the input.
   isDisabled = input.disabled;
   
   // Build the AJAX query.
   requestURl = "viewLineInspection.php?action=get_wc_number_input&jobNumber=" + jobNumber + "&isDisabled=" + isDisabled;
   
   var xhttp = new XMLHttpRequest();
   xhttp.validator = this;
   xhttp.onreadystatechange = function()
   {
      if (this.readyState == 4 && this.status == 200)
      {
         // Update with the new wc-number-input.
         input = document.getElementById("wc-number-input-div");
         input.innerHTML = this.responseText;
      }
   };
   
   xhttp.open("GET", requestURl, true);
   xhttp.send(); 
}

function updateCustomerPrint()
{
   input = document.getElementById("job-number-input");
   
   // Retrieve the selected job number.
   jobNumber = input.options[input.selectedIndex].value; 
   
   // Build the AJAX query.
   requestURl = "viewLineInspection.php?action=get_customer_print_link&jobNumber=" + jobNumber;
   
   var xhttp = new XMLHttpRequest();
   xhttp.validator = this;
   xhttp.onreadystatechange = function()
   {
      if (this.readyState == 4 && this.status == 200)
      {
         // Update with the customer print link.
         div = document.getElementById("customer-print-div");
         div.innerHTML = this.responseText;
      }
   };
   
   xhttp.open("GET", requestURl, true);
   xhttp.send(); 
}