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

    public function getVisitsPerDay() {
        return $this->db->query('SELECT visits, date, EXTRACT(YEAR FROM date) AS y, EXTRACT(MONTH FROM date) AS m, EXTRACT(DAY FROM date) AS d FROM daytable');
    }

    public function getVisitsPerMinute($date) {
    	$sqlQuery = "SELECT 
    			PreAgg.intervalStop,
    			TIME(PreAgg.intervalStop) as time
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
    
    public function getNrOfYears() {
    	$list = $this->db->query("SELECT MIN(date) least, MAX(date) max FROM daytable");
		$nrYears = 0;
    	foreach ($list as $row) {
    		$nrYears = 1 + ($row['max'] - $row['least']);
    	}
    	return $nrYears;
    }
}

?>
