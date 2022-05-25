<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
	class MasterController extends CI_Controller{
		
		public function __construct()
		{
			parent::__construct();		

			// Load library
			$this->load->library('memcached_library');
		}
	}
?>