<?php

require_once '../common/database.php';
require_once '../common/jobInfo.php';
require_once '../common/report.php';
require_once '../common/time.php';
require_once '../common/userInfo.php';

class JobsReport extends Report
{
    function __construct()
    {
       $this->startDate = Time::now("Y-m-d H:i:s");
       $this->endDate = Time::now("Y-m-d H:i:s");
       $this->onlyActive = false;
        
       if (isset($_POST["startDate"]))
       {
           $this->startDate = $_POST["startDate"];
       }
        
       if (isset($_POST["endDate"]))
       {
           $this->endDate = $_POST["endDate"];
       }
        
       if (isset($_POST["onlyActive"]))
       {
           $this->onlyActive = json_decode($_POST["onlyActive"]);
       }
   }
    
    protected function getTitle()
    {
        return ("Jobs Report");
    }
    
    protected function getDescription()
    {
        $description = "";
        
        $activeJobsDescription = "";
        if ($this->onlyActive)
        {
            $activeJobsDescription = "all active jobs";
        }
        else
        {
            $activeJobsDescription = "all jobs";
        }
        
        $dateString = "";
        if ($this->startDate == $this->endDate)
        {
            $dateTime = new DateTime($this->startDate, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
            
            $dateString = "created on {$dateTime->format("m/d/Y")}";
        }
        else
        {
            $startDateTime = new DateTime($this->startDate, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
            $endDateTime = new DateTime($this->endDate, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
            
            $dateString = "created between {$startDateTime->format("m/d/Y")} to {$endDateTime->format("m/d/Y")}";
        }
        
        $description = "Reporting $activeJobsDescription $dateString.";
        
        return ($description);
    }
    
    protected function getHeaders()
    {
        return (array("Job Number", "Author", "Date", "Part #", "Work Center #", "Cycle Time", "Net Percentage", "Status"));
    }
    
    protected function getData()
    {
        $data = array();
        
        $database = new PPTPDatabase();
        
        $database->connect();
        
        if ($database->isConnected())
        {
            // Start date.
            $startDate = new DateTime($this->startDate, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
            $startDateString = $startDate->format("Y-m-d");
            
            // End date.
            // Increment the end date by a day to make it inclusive.
            $endDate = new DateTime($this->endDate, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
            $endDate->modify('+1 day');
            $endDateString = $endDate->format("Y-m-d");
            
            $result = $database->getJobs($startDateString, $endDateString, $this->onlyActive);
            
            if ($result && ($database->countResults($result) > 0))
            {
                while ($row = $result->fetch_assoc())
                {
                    $jobInfo = JobInfo::load($row["jobId"]);
                    
                    if ($jobInfo)
                    {
                        $creatorName = "unknown";
                        $user = UserInfo::load($jobInfo->creator);
                        if ($user)
                        {
                            $creatorName= $user->getFullName();
                        }
                        
                        $dateTime = new DateTime($jobInfo->dateTime, new DateTimeZone('America/New_York'));  // TODO: Function in Time class
                        $date = $dateTime->format("m-d-Y");
                        
                        $status = JobStatus::getName($jobInfo->status);
                        
                        $dataRow = array($jobInfo->jobNumber, $creatorName, $date, $jobInfo->partNumber, $jobInfo->wcNumber, $jobInfo->cycleTime, $jobInfo->netPercentage, $status);
                        
                        $data[] = $dataRow;
                    }
                }
            }
        }
        
        return ($data);
    }
    
    private $startDate;
    
    private $endDate;
    
    private $onlyActive;
}

$report = new JobsReport();

?>

<!DOCTYPE html>
<html>

<head>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" type="text/css" href="../common/flex.css"/>
<link rel="stylesheet" type="text/css" href="../common/common.css"/>

</head>

<body>

   <?php $report->render(); ?>

</body>

<script>
   javascript:window.print()
</script>

</html>

