<?php
require_once 'header.php';
require_once 'navigation.php';
?>

<head>
   <link rel="stylesheet" type="text/css" href="flex.css"/>
   <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
   <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-blue.min.css" />
   
   <style>
   body {
      margin: 0px;
      font-family: "lucida grande",tahoma,verdana,arial,sans-serif;
      background: #eee;
   }
   
   .header-div {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
      
      height: 50px;
      
      background: #484fb0;
      
      padding-left: 50px;
      padding-right: 50px;
   }
   
   .page-title {
      font-size: 25px;
      color: #ffffff;
   }
   
   .nav-link {
   	font-size: 14px;
      color: #ffffff;
      text-decoration: none; /* changed from text-decoration:underline */
   }

   .nav-link:hover {
      text-decoration: underline;
   }
   
   .card-div {
      justify-content: flex-start;
   
   	background: #ffffff;
   	box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
   	align-items: stretch;
   	
   	width: 80%;
      height: 625px;
      
      padding-bottom: 20px;
   }
   
   .card-header-div {
      display: flex;
      flex-direction: row;
      justify-content: flex-start;
      align-items: center;
      
      background: #f2b955;
      font-size: 16px;
      color: #ffffff;
      
      padding-left: 20px;
      
      height: 50px;
   }
   
   .content-div {
      flex-grow: 1;
   }
   
   .nav-div {
      display: flex;
      flex-direction: row;
      justify-content: center;
      align-items: center;
   }
   
   .nav-button {
      border-width: 0;
      outline: none;
      box-shadow: 0 1px 4px rgba(0, 0, 0, .6);
      background-color: #ebebeb;
      
      color: #000000;
      font-family:"lucida grande",tahoma,verdana,arial,sans-serif;
      font-size: 25px;
      
      height: auto;
      width: 150px;

      padding: 20px 20px;
      
      margin-right: 20px;
      margin-left: 20px;
   }
   
    .nav-button-highlight {
      background-color: #484fb0;
      color: #ffffff;
   }
   
   .comments-input {
      width:500px;
      rows: 10;
      
      font-family:"lucida grande",tahoma,verdana,arial,sans-serif;
      font-size: 16px;
   }
   </style>
</head>

<body>
<?php Header::render("Time Cards"); ?>

<div class="flex-horizontal" style="height: 700px;">

   <div class="flex-vertical card-div">
      <div class="card-header-div">Add Comments</div>
      <div class="flex-horizontal content-div" style="height:400px;">
      
         <form id="timeCardForm" action="timeCard.php" method="POST">
         
            <textarea class="comments-input" type="text" name="comments" rows="10" placeholder="Enter comments ..." form-id="timeCardForm"></textarea>
     
         </form>
   
      </div>
      <?php
      Navigation::start();
      Navigation::cancelButton("submitForm('timeCardForm', 'timeCard.php', 'view_time_cards', 'cancel_time_card')");
      Navigation::backButton("if (validatePartCount()){submitForm('timeCardForm', 'timeCard.php', 'enter_parts_count', 'update_time_card_info');};");
      Navigation::nextButton("if (validatePartCount()){submitForm('timeCardForm', 'timeCard.php', 'edit_time_count', 'update_time_card_info');};");
      Navigation::end();
      ?>   
   </div>

</div>

</body>