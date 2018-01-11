function doPageNav(page)
{
   form = document.getElementById("panTicketForm");
   
   input = document.createElement('input');
   input.setAttribute('name', 'page');
   input.setAttribute('type', 'hidden');
   input.setAttribute('value', page);
   
   form.appendChild(input);
   form.submit();
}