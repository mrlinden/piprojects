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
    	$sqlQuery = "SELECT PreAgg.date, PreAgg.doorA, PreAgg.doorB, PreAgg.doorC, PreAgg.doorD,
    	@PrevSumA := @PrevSumA + PreAgg.doorA AS doorAtot
    	FROM
    	( SELECT
    			MT.date,
    			MT.doorA,
    			MT.doorB,
    			MT.doorC,
    			MT.doorD,
    			FROM minutetable MT
    			ORDER BY
    			MT.date ) AS PreAgg,
    			( select @PrevSumA := 0.00 ) as SqlVars";
    	
    	return $this->db->query($sqlQuery);
    	
//    	return $this->db->query("SELECT * FROM minutetable WHERE DATE(intervalStart)='$date'");
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
