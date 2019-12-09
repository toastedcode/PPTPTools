class Cookie
{
   static keyExists(key)
   {
      
   }
   
   static set(key, value)
   {
      
   }
   
   static get(key)
   {
      
   }
   
   static getInt(key)
   {
      
   }
   
   static getBool(key)
   {
      
   }
   
   static save()
   {
      
   }
   
   static load()
   {
      var decodedCookie = decodeURIComponent(document.cookie);
      
      var cookies = decodedCookie.split(';');
      
      for (cookie in cookies)
      {
         var tokens = cookie.split('=');
         
         if (tokens.length == 2)
         {
            
         }
      }
   }
}