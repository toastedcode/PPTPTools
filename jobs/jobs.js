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
   valid = false;

   if (!((document.getElementById("job-number-prefix-input").validator.validate()) &&
         (document.getElementById("job-number-suffix-input").validator.validate())))
   {
      alert("Please enter a valid job number.  (Format: Mxxxx-xxxx)");      
   }
   else if (!(document.getElementById("cycle-time-input").validator.validate()))
   {
      alert("Please enter a valid cycle time.");
   }
   else if (!(document.getElementById("net-parts-per-hour-input").validator.validate()))
   {
      alert("Please enter a valid net parts per hour.");
   }
   else
   {
      valid = true;
   }
   
   return (valid);
}

function autoFillPartNumber()
{
   jobNumberPrefixInput = document.getElementById('job-number-prefix-input');
   partNumberInput = document.getElementById('part-number-input');
   partNumberDisplayInput = document.getElementById('part-number-display-input');
   
   if (jobNumberPrefixInput && partNumberInput && partNumberDisplayInput)
   {
      partNumberInput.value = jobNumberPrefixInput.value;
      partNumberDisplayInput.value = jobNumberPrefixInput.value;
   }
   
   autoFillJobNumber();
}

function autoFillJobNumber()
{
   jobNumberPrefixInput = document.getElementById('job-number-prefix-input');
   jobNumberSuffixInput = document.getElementById('job-number-suffix-input');
   jobNumberInput = document.getElementById('job-number-input');
   
   if (jobNumberPrefixInput && jobNumberSuffixInput && jobNumberInput)
   {
      jobNumberInput.value = jobNumberPrefixInput.value + "-" + jobNumberSuffixInput.value;
   }
}

function PartNumberValidator(inputId, maxLength, minValue, maxValue, allowNull)
{
   this.inputId = inputId;
   this.minValue = minValue;
   this.maxValue = maxValue;
   this.maxLength = maxLength;
   this.allowNull = allowNull;
   
   PartNumberValidator.prototype.init = function()
   {
      var element = document.getElementById(this.inputId);
      
      if (element)
      {
         element.maxLength = this.maxLength;
         
         element.validator = this;
      }
   }
   
   PartNumberValidator.prototype.isValid = function()
   {
      var valid = false;
   
      var element = document.getElementById(this.inputId);
      
      if (element)
      {
         var value = element.value;
         
         if ((value == null) || (value == "")) 
         {
            valid = this.allowNull;
         }
         else
         {
            var firstChar = element.value.charAt(0);
            
            var remainingChar = element.value.substring(1);
            
            valid = ((firstChar == 'M') &&
                     (!isNaN(remainingChar)) && 
                     (parseInt(remainingChar) >= this.minValue) && 
                     (parseInt(remainingChar) <= this.maxValue));
         }
      }
      
      return (valid);
   }
   
   PartNumberValidator.prototype.color = function(color)
   {
      var element = document.getElementById(this.inputId);
      
      if (element)
      {
         element.style.color = color;
      }
   }
   
   PartNumberValidator.prototype.validate = function()
   {
      var valid = this.isValid();
      
      if (valid)
      {
         this.color("#000000");
      }
      else
      {
         this.color("#FF0000");
      }

      return (valid);
   }
}