function getHTTPObject(id)
{
  var xmlhttp = false;

  /* Compilation conditionnelle d'IE */
  /*@cc_on
  @if (@_jscript_version >= 5)
     try
     {
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
     }
     catch (e)
     {
        try
        {
           xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        catch (E)
        {
           xmlhttp = false;
        }
     }
  @else
     xmlhttp = false;
  @end @*/

  /* on essaie de créer l'objet si ce n'est pas déjà fait */
  if (!xmlhttp && typeof XMLHttpRequest != 'undefined')
  {
     try
     {
        xmlhttp = new XMLHttpRequest();
     }
     catch (e)
     {
        xmlhttp = false;
     }
  }

  if (xmlhttp)
  {
     /* on définit ce qui doit se passer quand la page répondra */
     xmlhttp.onreadystatechange=function()
     {
        if (xmlhttp.readyState == 4) /* 4 : état "complete" */
        {
           if (xmlhttp.status == 200) /* 200 : code HTTP pour OK */
           {
							document.getElementById(id).innerHTML=xmlhttp.responseText
           }
        }
     }
  }
  return xmlhttp;
}



/**
  * Envoie des données à l'aide d'XmlHttpRequest?
  * @param string methode d'envoi ['GET'|'POST']
  * @param string url
  * @param string données à envoyer sous la forme var1=value1&var2=value2...
  */
function sendData(id, method, url, data)
{
  var xmlhttp = getHTTPObject(id);

  if (!xmlhttp)
  {
      return false;
  }

  if(method == "GET")
   {
   if(data == 'null' || data == '')
   {
          xmlhttp.open("GET", url, true); //ouverture asynchrone
   }
   else
   {
   	      if (url.indexOf("?")>=0)
                 xmlhttp.open("GET", url+"&"+data, true);
          else
   	             xmlhttp.open("GET", url+"?"+data, true);
   }
      xmlhttp.send(null);
   }
   else if(method == "POST")
   {
      xmlhttp.open("POST", url, true); //ouverture asynchrone
      xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
      xmlhttp.send(data);
   }
  return true;
}





