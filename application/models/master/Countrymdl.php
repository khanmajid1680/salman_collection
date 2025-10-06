<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Countrymdl extends CI_model{
		protected $table;
		protected $term;
		public function __construct(){
			parent::__construct();

			$this->term  = 'country';
			$this->table = 'country_master';
			$this->load->model('master/Commonmdl');
		}
		public function get_record($condition, $wantDropDown = false){
			$record = [];
			$data 	= $this->db->get_where($this->table,$condition)->result_array();
			if(!$wantDropDown) return $data;
			if(empty($data)){
				$record[0] = 'NO '.strtoupper($this->term).' ADDED';

			}else{
				$record[0] = 'SELECT';
				foreach ($data as $key => $value) {
					$record[$value[$this->term.'_id']] = strtoupper($value[$this->term.'_name']);
				}
			}
			return $record;
		}
		public function get_search($condition){
			$data 	= $this->db->get_where($this->table,$condition)->result_array();
			if(empty($data)) return ['value' => '', 'text' => ''];
			$value 	= $data[0][$this->term.'_id'];
			$text 	= $data[0][$this->term.'_name'];
			return ['value' => $value, 'text' => $text];
		}		
		public function isExist($id){
			$data = $this->db->query("SELECT account_id FROM account_master WHERE account_country_id = $id LIMIT 1")->result_array();
			if(!empty($data)) return true;
			
			return false;
		}
		public function get_master($wantCount, $per_page = 20, $offset = 0){
			$record 	= [];
			$subsql 	= '';
			$limit  	= '';
			$ofset  	= '';
			
			if(!$wantCount){
				$limit .= " LIMIT $per_page";
				$ofset .= " OFFSET $offset";
			}
			
			if(isset($_GET['id']) && !empty($_GET['id'])){
				$subsql .=" AND ".$this->term."_id = ".$_GET['id'];
				$record['search']['id'] = $this->get_search([$this->term.'_id' => $_GET['id']]);
			}
			if(isset($_GET['status'])){
				$status = $_GET['status'] == 2 ? 0 : $_GET['status'];
				$subsql .=" AND ".$this->term."_status = ".$status;
				$record['search']['status'] = $this->Commonmdl->get_status($_GET['status']);
			}
			$query ="
						SELECT ".$this->term."_id as id, UPPER(".$this->term."_name) as name, ".$this->term."_status as status
						FROM ".$this->table."
						WHERE 1
						$subsql
						ORDER BY ".$this->term."_id DESC
						$limit
						$ofset
					";
			// echo "<pre>"; print_r($query); exit;
			if($wantCount){
				return $this->db->query($query)->num_rows();
			}
			$record['data'] = $this->db->query($query)->result_array();
			if(!empty($record['data'])){
				foreach ($record['data'] as $key => $value) {
					$record['data'][$key]['isExist'] = $this->isExist($value['id']);
				}
			}
			return $record;
		}
		public function get_select2(){
			$subsql = "";

			if(isset($_GET['name']) && !empty($_GET['name'])){
				$name 	= $_GET['name'];
				$subsql .= " AND (".$this->term."_name LIKE '%".$name."%') ";
			}
			$query ="
						SELECT ".$this->term."_id as id, UPPER(".$this->term."_name) as name
						FROM ".$this->table."
						WHERE 1
						$subsql
						ORDER BY ".$this->term."_name ASC
						LIMIT 10
					";
			// echo $query; exit();
			return $this->db->query($query)->result_array();
		}
	}
?>