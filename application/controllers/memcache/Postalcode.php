<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	require_once(dirname(__FILE__)."\../MasterController.php");
	
	class Postalcode extends MasterController {
		
		function __construct()
		{
			parent::__construct();
			
			
			$this -> load -> model('memcache/PostalcodeModel','postalcode');
		}
	
		public function index()
		{
			$time_start 			= microtime(true); 

			$key		= "postalcode";
			$type		= $this -> input -> get('type');
			$search		= $this -> input -> get('search');
			
			if($type != ""){
				$key	= $key. "_" .$type. "_" .preg_replace('/[ ,]+/', '_', trim($search));
			}

			$data					= array();
			$data['generate_by']	= "Memcache ($key)";

			// Lets try to get the key
			$results = $this->memcached_library->get($key);
	
			// If the key does not exist it could mean the key was never set or expired
			if (!$results) {
				$data['generate_by'] = "Query";

				// Modify this Query to your liking!
				$results = $this->get_postalcode($type, $search);
	
				// Lets store the results
				// $this->memcached_library->add($key, $results, );
				$this->memcached_library->set($key, $results);
	
			}

			$data['data'] 			= $results;
			$data['type'] 			= $type;
			$data['search'] 		= $search;
			$data['execute_time']	= (microtime(true) - $time_start);

			$this->load->view('memcache/postalcode', $data);
		}

		public function get_postalcode($type, $search)
		{
			$where = "";

			if($type != ""){
				$where = "WHERE LOWER($type) like '%". strtolower($search) ."%'";
			}
			
			return $this->postalcode->get_postalcode($where);
		}

    }
?>