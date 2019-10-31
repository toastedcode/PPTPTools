function onSubmit()
{
   if (validateInspection())
   {
      var form = document.querySelector('#input-form');
      
      var xhttp = new XMLHttpRequest();
   
      // Bind the form data.
      var formData = new FormData(form);
   
      // Define what happens on successful data submission.
      xhttp.addEventListener("load", function(event) {
         console.log(this.responseText);
         var json = JSON.parse(event.target.responseText);

         if (json.success == true)
         {
            location.href = "inspections.php";
         }
         else
         {
            alert(json.error);
         }
      });
   
      // Define what happens on successful data submission.
      xhttp.addEventListener("error", function(event) {
        alert('Oops! Something went wrong.');
      });
   
      // Set up our request
      requestUrl = "../api/saveInspection/"
      xhttp.open("POST", requestUrl);
   
      // The data sent is what the user provided in the form
      xhttp.send(formData);
   }
}

function onDeleteInspection(inspectionId)
{
   if (confirm("Are you sure you want to delete this inspection?"))
   {
      // AJAX call to delete an ispection.
      requestUrl = "../api/deleteInspection/?inspectionId=" + inspectionId;
      
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function()
      {
         if (this.readyState == 4 && this.status == 200)
         {
            var json = JSON.parse(this.responseText);
            
            if (json.success == true)
            {
               location.href = "inspections.php";            
            }
            else
            {
               alert(json.error);
            }
         }
      };
      xhttp.open("GET", requestUrl, true);
      xhttp.send();  
   }
}

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
   inspectionType = parseInt(document.getElementById("inspection-type-input").value);
   jobNumber = document.getElementById("job-number-input").value;
   wcNumber = parseInt(document.getElementById("wc-number-input").value);
   
   if (inspectionType != 0)
   {
      // AJAX call to populate template id based on selected inspection type, job number, and WC number.
      requestUrl = "../api/inspectionTemplates/?inspectionType=" + inspectionType + "&jobNumber=" + jobNumber + "&wcNumber=" + wcNumber;
      
      var xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function()
      {
         if (this.readyState == 4 && this.status == 200)
         {
            var json = JSON.parse(this.responseText);
            
            if (json.success == true)
            {
               //if (json.templates.length == 1)
               //{
               //   console.log("Selecting template id: " + json.templates[0].templateId);
               //}
               
               updateTemplateIdOptions(json.templates);
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

function updateTemplateIdOptions(templates)
{
   element = document.getElementById("template-id-input");
   
   while (element.firstChild)
   {
      element.removeChild(element.firstChild);
   }
   
   var selectedTemplateId = 0;
   if (templates.length == 1)
   {
      var selectedTemplateId = templates[0].templateId;
   }

   for (var template of templates)
   {
      var option = document.createElement('option');
      option.innerHTML = template.name;
      option.value = template.templateId;
      element.appendChild(option);
   }

   if (templates.length == 1)
   {
      element.value = templates[0].templateId;
   }
   else
   {
      element.value = null;
   }
 
}

function validateInspectionSelection()
{
   valid = false;
   
   if (!(document.getElementById("inspection-type-input").validator.validate()))
   {
      alert("Start by selecting an inspection type.");    
   }
   else if (!(document.getElementById("job-number-input").validator.validate()))
   {
      alert("Please select an active job.");    
   }
   else if (!(document.getElementById("wc-number-input").validator.validate()))
   {
      alert("Please select a work center.");    
   }
   else
   {
      templateId = parseInt(document.getElementById("template-id-input").value);
      
      if (isNaN(templateId) || (templateId == 0))
      {
         alert("No inspection template could be found for the current selection."); 
      }
      else
      {
         valid = true;
      }
   }
   
   return (valid);
}

function validateInspection()
{
   valid = false;
   
   if (!(document.getElementById("operator-input").validator.validate()))
   {
      alert("Select an operator.");    
   }
   else
   {
      valid = true;
   }
   
   return (valid);   
}