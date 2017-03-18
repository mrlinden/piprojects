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
        return $this->db->query('SELECT visits, date, EXTRACT(YEAR FROM date) AS y, EXTRACT(MONTH FROM date) AS m, EXTRACT(DAY FROM date) AS d FROM daytable');
    }

	/* Get a list of all visits per minute reported for given date */    
    public function getVisitsPerMinuteOld($date) {
    	$sqlQuery = "SELECT 
    			PreAgg.intervalStop,
    			TIME(PreAgg.intervalStop) AS time,
    			EXTRACT(HOUR FROM PreAgg.intervalStop) AS h,
    			EXTRACT(MINUTE FROM PreAgg.intervalStop) AS m,
    			EXTRACT(SECOND FROM PreAgg.intervalStop) AS s,
    			PreAgg.doorA, 
    			PreAgg.doorB, 
    			PreAgg.doorC, 
    			PreAgg.doorD,
    			@SumABCD  := PreAgg.doorA + PreAgg.doorB + PreAgg.doorC + PreAgg.doorD AS visits,
    			@PrevSumA := @PrevSumA + PreAgg.doorA AS doorAtot,
		    	@PrevSumB := @PrevSumB + PreAgg.doorB AS doorBtot,
		    	@PrevSumC := @PrevSumC + PreAgg.doorC AS doorCtot,
		    	@PrevSumD := @PrevSumD + PreAgg.doorD AS doorDtot,
		    	@PrevSumVisits := @PrevSumVisits + PreAgg.doorA + PreAgg.doorB + PreAgg.doorC + PreAgg.doorD AS visitsSum
		    	FROM ( SELECT
    					MT.intervalStop,
	    				MT.doorA,
	    				MT.doorB,
	    				MT.doorC,
	    				MT.doorD
	    				FROM `minutetable` AS MT
	    				WHERE DATE(MT.intervalStop)='".$date."'
	    				ORDER BY MT.intervalStop
	    			) AS PreAgg,
	    		( select @SumABCD := 0, @PrevSumA := 0, @PrevSumB := 0, @PrevSumC := 0, @PrevSumD := 0, @PrevSumVisits := 0) as SqlVars";
    	return $this->db->query($sqlQuery);
    }

    /* Get difference in years between last and first visit */
    public function getNrOfYearsFromFirstToLastVisit() {
    	$list = $this->db->query("SELECT MIN(date) least, MAX(date) max FROM daytable");
    	$nrYears = 0;
    	foreach ($list as $row) {
    		$nrYears = 1 + ($row['max'] - $row['least']);
    	}
    	return $nrYears;
    }
    
    /* Get the sum of all visits for given date */
    public function getSumOfVisits($date) {
    	$sqlQuery = "SELECT
    			@Sum  := @Sum + PreAgg.doorA + PreAgg.doorB + PreAgg.doorC + PreAgg.doorD AS visits
		    	FROM ( SELECT
    					MT.intervalStop,
	    				MT.doorA,
	    				MT.doorB,
	    				MT.doorC,
	    				MT.doorD
	    				FROM `minutetable` AS MT
	    				WHERE DATE(MT.intervalStop)='".$date."'
	    			) AS PreAgg,
	    		( select @Sum := 0) as SqlVars";
    	
    	$res = $this->db->query($sqlQuery);
    	
    	$nrVisits = 0;
    	foreach ($res as $row) {
    		$nrVisits = $row['visits'];
    	}
    	return $nrVisits;
    }
    
    

    /* Get a list of all visits per minute reported for given date */
    public function getVisitsPerMinute($date) {
    	$sqlQuery = "SELECT
						MT2.date,
						TIME(MT2.date) AS time,
						EXTRACT(HOUR FROM MT2.date) AS h,
						EXTRACT(MINUTE FROM MT2.date) AS m,
						EXTRACT(SECOND FROM MT2.date) AS s,
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
    						MT.date,
					    	sum(CASE WHEN MT.id = 1 THEN MT.count ELSE 0 END) as 'doorA',
    						sum(CASE WHEN MT.id = 2 THEN MT.count ELSE 0 END) as 'doorB',
    						sum(CASE WHEN MT.id = 3 THEN MT.count ELSE 0 END) as 'doorC',
    						sum(CASE WHEN MT.id = 4 THEN MT.count ELSE 0 END) as 'doorD'
   	    					FROM `sensordata` AS MT
	    					WHERE DATE(MT.date)='".$date."'
                        	GROUP BY MT.date
                        	ORDER BY MT.date
                      	) AS MT2,
						( select @SumABCD := 0, @PrevSumA := 0, @PrevSumB := 0, @PrevSumC := 0, @PrevSumD := 0, @PrevSumVisits := 0) as SqlVars";
    	return $this->db->query($sqlQuery);
    }   
    
    
}
?>
