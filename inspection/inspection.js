/*
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
*/

function set(elementId, value)
{
   document.getElementById(elementId).value = value;
}

function clear(elementId)
{
   document.getElementById(elementId).value = null;
}

function enable(elementId)
{
   document.getElementById(elementId).disabled = false;
}

function disable(elementId)
{
   document.getElementById(elementId).disabled = true;
}

function onJobNumberChange()
{
   jobNumber = document.getElementById("job-number-input").value;
   
   if (jobNumber == null)
   {
      disable("wc-number-input");
   }
   else
   {
      enable("wc-number-input");
      
      // Populate WC numbers based on selected job number.
      
      // AJAX call to populate WC numbers based on selected job number.
      requestUrl = "../api/wcNumbers/?jobNumber=" + jobNumber;
      
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function()
      {
         if (this.readyState == 4 && this.status == 200)
         {
            var json = JSON.parse(this.responseText);
            
            if (json.success == true)
            {
               updateWcOptions(json.wcNumbers);               
            }
            else
            {
               console.log("API call to retrieve WC numbers failed.");
            }
         }
      };
      xhttp.open("GET", requestUrl, true);
      xhttp.send();  
   }
}

function updateTemplateId()
{
   console.log("here");
   inspectionType = document.getElementById("inspection-type-input").value;
   jobNumber = document.getElementById("job-number-input").value;
   wcNumber = document.getElementById("wc-number-input").value;
   
   if ((inspectionType != "") && (jobNumber != "") && (wcNumber != ""))
   {
      // AJAX call to populate template id based on selected inspection type, job number, and WC number.
      requestUrl = "../api/inspectionTemplate/?inspectionType=" + inspectionType + "&jobNumber=" + jobNumber + "&wcNumber=" + wcNumber;
      console.log(requestUrl);
      
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function()
      {
         if (this.readyState == 4 && this.status == 200)
         {
            var json = JSON.parse(this.responseText);
            
            if (json.success == true)
            {
               console.log("Selecting template id: " + json.templateId);
               document.getElementById("template-id-input").value = json.templateId;
            }
            else
            {
               console.log("API call to retrieve inspection template id failed.");
            }
         }
      };
      xhttp.open("GET", requestUrl, true);
      xhttp.send();
   }
}

function updateWcOptions(wcNumbers)
{
   element = document.getElementById("wc-number-input");
   
   while (element.firstChild)
   {
      element.removeChild(element.firstChild);
   }

   for (var wcNumber of wcNumbers)
   {
      var option = document.createElement('option');
      option.innerHTML = wcNumber;
      option.value = wcNumber;
      element.appendChild(option);
   }
   
   element.value = null;
}