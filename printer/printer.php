<?php

require_once '../common/authentication.php';
require_once '../common/database.php';
require_once '../common/header.php';
require_once 'printJob.php';
require_once 'printQueue.php';

// *********************************** BEGIN ***********************************

Time::init();

session_start();

if (!Authentication::isAuthenticated())
{
   header('Location: ../pptpTools.php');
   exit;
}
?>

<!DOCTYPE html>
<html>

<head>

   <meta name="viewport" content="width=device-width, initial-scale=1">
   
   <link rel="stylesheet" type="text/css" href="../common/flex.css"/>
   <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
   <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css"/>
   <link rel="stylesheet" type="text/css" href="../common/common.css"/>
   <link rel="stylesheet" type="text/css" href="../common/tooltip.css"/>
   
   <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
   <!-- script src = "DYMO.Label.Framework.js" type="text/javascript" charset="UTF-8"> </script-->
   <script src="http://www.labelwriter.com/software/dls/sdk/js/DYMO.Label.Framework.3.0.js" type="text/javascript" charset="UTF-8"></script>
   <script src="printer.js"></script>

</head>

<body>

   <?php Header::render("PPTP Tools"); ?>
   
   <div class="flex-horizontal main">
     
     <div class="flex-horizontal sidebar hide-on-tablet"></div> 
   
     <div class="flex-vertical content">

        <div class="heading">Cloud Print Manager</div>

        <div class="description">Something something cloud.  Something something print.</div>

        <div class="flex-horizontal inner-content">
        
           <div id="print-job-table-container" style="margin-right: 25px;"></div>
           
           <div id="preview-image-container">
              <img id="preview-image" src="" alt="label preview"/>
           </div>
      
        </div>
         
     </div>
     
   </div>
   
   <script>
      var printManager = new PrintManager(document.getElementById("print-job-table-container"),
                                          document.getElementById("preview-image"));
   
      printManager.start();
   </script>

</body>

</html>