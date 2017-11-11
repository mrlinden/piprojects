<?php
namespace Cupolen;
use PDO;

class Visits
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

	/* Get a list of all days and the number of visits for each day */    
    public function getVisitsPerDay() {
        return $this->db->query('SELECT count, date, EXTRACT(YEAR FROM date) AS y, EXTRACT(MONTH FROM date) AS m, EXTRACT(DAY FROM date) AS d FROM overview');
    }

    /* Get difference in years between last and first visit */
    public function getNrOfYearsFromFirstToLastVisit() {
    	$list = $this->db->query("SELECT MIN(date) least, MAX(date) max FROM overview");
    	$nrYears = 0;
    	foreach ($list as $row) {
    		$nrYears = 1 + ($row['max'] - $row['least']);
    	}
    	return $nrYears;
    }

    /* Get a list of all visits per minute reported for given date */
    public function getVisitsPerMinute($date) {
    	$sqlQuery = "SELECT
						MT2.timestamp,
						TIME(MT2.timestamp) AS time,
						EXTRACT(HOUR FROM MT2.timestamp) AS h,
						EXTRACT(MINUTE FROM MT2.timestamp) AS m,
						EXTRACT(SECOND FROM MT2.timestamp) AS s,
						MT2.doorA,
						MT2.doorB,
						MT2.doorC,
						MT2.doorD,
						@SumABCD  := MT2.doorA + MT2.doorB + MT2.doorC + MT2.doorD AS visits,
						@PrevSumA := @PrevSumA + MT2.doorA AS doorAtot,
						@PrevSumB := @PrevSumB + MT2.doorB AS doorBtot,
						@PrevSumC := @PrevSumC + MT2.doorC AS doorCtot,
						@PrevSumD := @PrevSumD + MT2.doorD AS doorDtot,
						@PrevSumVisits := @PrevSumVisits + MT2.doorA + MT2.doorB + MT2.doorC + MT2.doorD AS visitsSum
						FROM (SELECT
    						MT.timestamp,
					    	sum(CASE WHEN MT.sensorId = 1 THEN MT.count ELSE 0 END) as 'doorA',
    						sum(CASE WHEN MT.sensorId = 2 THEN MT.count ELSE 0 END) as 'doorB',
    						sum(CASE WHEN MT.sensorId = 3 THEN MT.count ELSE 0 END) as 'doorC',
    						sum(CASE WHEN MT.sensorId = 4 THEN MT.count ELSE 0 END) as 'doorD'
   	    					FROM `sensordata` AS MT
	    					WHERE DATE(MT.timestamp)='".$date."'
                        	GROUP BY MT.timestamp
                        	ORDER BY MT.timestamp
                      	) AS MT2,
						( select @SumABCD := 0, @PrevSumA := 0, @PrevSumB := 0, @PrevSumC := 0, @PrevSumD := 0, @PrevSumVisits := 0) as SqlVars";
    	return $this->db->query($sqlQuery);
    }   
    
    
}
?>
