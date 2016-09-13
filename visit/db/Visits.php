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

// SELECT visits, date, EXTRACT(YEAR FROM date) AS y, EXTRACT(MONTH FROM date) AS m, EXTRACT(DAY FROM date) AS d


    public function getVisitsPerDay() {
        return $this->db->query('SELECT * FROM daytable');
    }
/*
    public function getVisitsPerDay($year) {
    	$dateStart;
    	return $this->db->query('SELECT * FROM daytable WHERE date="$year"');
    }
*/
    public function getVisitsPerMinute($date) {
    	return $this->db->query('SELECT * FROM minutetable WHERE date="$date"');
    }
    
}

?>
