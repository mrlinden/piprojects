<?php
class Visits
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

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