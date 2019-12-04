<?php

require_once 'jobInfo.php';
require_once 'timeCardInfo.php';
require_once 'userInfo.php';

require_once '../printer/printJob.php';

abstract class PanTicketLabelFields
{
   const FIRST = 0;
   const PAN_TICKET_ID = PanTicketLabelFields::FIRST;
   const JOB_NUMBER = 1;
   const WC_NUMBER = 2;
   const OPERATOR = 3;
   const MFG_DATE = 4;
   const HEAT_NUMBER = 5;
   const PAN_COUNT = 6;
   const LAST = 7;
   const COUNT = PanTicketLabelFields::LAST - PanTicketLabelFields::FIRST;
   
   public static function getKeyword($panTicketLabelField)
   {
      $keywords = array("%panTicketId", 
                        "%jobNumber", 
                        "%wcNumber", 
                        "%operator", 
                        "%mfgDate",
                        "%heatNumber",
                        "%panCount");
      
      return ($keywords[$panTicketLabelField]);
   }
}

class PanTicket
{
   const UNKNOWN_PAN_TICKET_ID = TimeCardInfo::UNKNOWN_TIME_CARD_ID;
   
   const LABEL_TEMPLATE_FILENAME = "../panTicket/PanTicketTemplate.label";
   
   public $panTicketId = PanTicket::UNKNOWN_PAN_TICKET_ID;
   
   public $labelXML = "";
   
   public function __construct($panTicketId)
   {
      // A pan ticket id is the same as a time card id.
      $this->panTicketId = $panTicketId;
      
      $this->labelXML = PanTicket::generateLabelXml($this->panTicketId);
   }

   private static function generateLabelXml($timeCardId)
   {
      $xml = "";
      
      $timeCardInfo = TimeCardInfo::load($timeCardId);
      
      $operator = "";
      if ($timeCardInfo)
      {
         $userInfo = UserInfo::load($timeCardInfo->employeeNumber);
         $operator = $userInfo->username . " ($timeCardInfo->employeeNumber)";
      }
      
      $jobNumber = "";
      $wcNumber = "";
      $jobInfo = JobInfo::load($timeCardInfo->jobId);
      if ($jobInfo)
      {
         $jobNumber = $jobInfo->jobNumber;
         $wcNumber = $jobInfo->wcNumber;
      }
      
      $dateTime = new DateTime($timeCardInfo->dateTime, new DateTimeZone('America/New_York'));
      $mfgDate = $dateTime->format("m-d-Y");
      
      $file = fopen(PanTicket::LABEL_TEMPLATE_FILENAME, "r");
      
      if ($file)
      {
         $xml = fread($file, filesize(PanTicket::LABEL_TEMPLATE_FILENAME));
         $xml = substr($xml, 3);  // Three odd characters at beginning when reading from file.
   
         fclose($file);

         for ($field = PanTicketLabelFields::FIRST; $field < PanTicketLabelFields::LAST; $field++)
         {
            switch ($field)
            {
               case PanTicketLabelFields::PAN_TICKET_ID:
               {
                  $xml = str_replace(PanTicketLabelFields::getKeyword($field), $timeCardId, $xml);
                  break;
               }
                  
               case PanTicketLabelFields::JOB_NUMBER:
               {
                  $xml = str_replace(PanTicketLabelFields::getKeyword($field), $jobNumber, $xml);
                  break;
               }
               
               case PanTicketLabelFields::WC_NUMBER:
               {
                  $xml = str_replace(PanTicketLabelFields::getKeyword($field), $wcNumber, $xml);
                  break;
               }
               
               case PanTicketLabelFields::OPERATOR:
               {
                  $xml = str_replace(PanTicketLabelFields::getKeyword($field), $operator, $xml);
                  break;
               }
               
               case PanTicketLabelFields::MFG_DATE:
               {
                  $xml = str_replace(PanTicketLabelFields::getKeyword($field), $mfgDate, $xml);
                  break;
               }
               
               case PanTicketLabelFields::HEAT_NUMBER:
               {
                  $xml = str_replace(PanTicketLabelFields::getKeyword($field), $timeCardInfo->materialNumber, $xml);
                  break;
               }
               
               case PanTicketLabelFields::PAN_COUNT:
               {
                  $xml = str_replace(PanTicketLabelFields::getKeyword($field), $timeCardInfo->panCount, $xml);
                  break;
               }
               
               default:
               {
                  break;
               }
            }
         }
      }
      
      return ($xml);
   }
}

if (isset($_GET["preview"]) &&
    isset($_GET["panTicketId"]))
{
   $panTicketId = $_GET["panTicketId"];
   
   echo
<<<HEREDOC
   <html>
      <head>
         <script src="http://www.labelwriter.com/software/dls/sdk/js/DYMO.Label.Framework.3.0.js" type="text/javascript" charset="UTF-8"></script>
         <script src="panTicket.js"></script>
      </head>
      <body>
         <img id="pan-ticket-image" src="" alt="pan ticket"/>
      </body>
      <script>
         dymo.label.framework.init(function() {
            var label = new PanTicket($panTicketId, "pan-ticket-image");
         });
      </script>
   </html>
HEREDOC;
}

?>