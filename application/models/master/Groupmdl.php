<?php defined('BASEPATH') OR exit('No direct script access allowed');

	class Groupmdl extends CI_model{
		protected $table;
		protected $term;
		public function __construct(){
			parent::__construct();

			$this->table = 'group_master';
		}
		public function get_record($condition, $wantDropDown = false){
			$record = [];
			$data 	= $this->db->get_where($this->table,$condition)->result_array();
			if(!$wantDropDown) return $data;
			if(empty($data)){
				$record[0] = 'NO GROUP ADDED';
			}else{
				$record[0] = 'SELECT';
				foreach ($data as $key => $value) {
					$record[$value['grp_id']] = strtoupper($value['grp_name']);
				}
			}
			return $record;
		}
	}
?>