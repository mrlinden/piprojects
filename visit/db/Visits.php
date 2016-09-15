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
    	return $this->db->query("SELECT * FROM minutetable WHERE DATE(intervalStart)='$date'");
    }
    
    public function getNrOfYears() {
    	//$list = $this->db->query("SELECT MIN(date) AS min FROM daytable UNION SELECT MAX(date) max FROM daytable");
		$list = $this->db->query("SELECT MIN(date) least, MAX(date) max FROM daytable");
    	foreach ($list as $row2) {
    		print " - Time : " . $row2['min'] . " had " . $row2['max'] . " \n" ;
    	}
    	return 4;
    }
}

?>
