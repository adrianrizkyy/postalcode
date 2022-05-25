<?php
	
	class PostalcodeModel extends CI_Model {
		
		function __construct()
		{
			// Call the Model constructor
			parent::__construct();

		}
		
		function get_postalcode($where = '', $skip = '0', $limit = '1000')
		{
			$sql   = "SELECT * FROM postalcode $where LIMIT $skip, $limit";
            $query = $this->db->query($sql);;

            return $query->result_array();
        }
    }
?>