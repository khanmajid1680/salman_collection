<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Usermdl extends CI_model{
		protected $table;
		protected $term;
		public function __construct(){
			parent::__construct();

			$this->term  = 'user';
			$this->table = 'user_master';

			$this->load->model('master/Commonmdl');
			$this->load->model('master/Branchmdl');
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
		public function isExist($id){
			if($id == 1) return true;
			
			$data = $this->db->query("SELECT user_id FROM user_master WHERE user_created_by = $id LIMIT 1")->result_array();
			if(!empty($data)) return true;

			$data = $this->db->query("SELECT pm_id FROM purchase_master WHERE pm_created_by = $id LIMIT 1")->result_array();
			if(!empty($data)) return true;

			$data = $this->db->query("SELECT sm_id FROM sales_master WHERE sm_created_by = $id LIMIT 1")->result_array();
			if(!empty($data)) return true;

			$data = $this->db->query("SELECT vm_id FROM voucher_master WHERE vm_created_by = $id LIMIT 1")->result_array();
			if(!empty($data)) return true;
			
			return false;
		}
		public function get_search($condition){
			$data 	= $this->db->get_where($this->table,$condition)->result_array();
			if(empty($data)) return ['value' => '', 'text' => ''];
			$value 	= $data[0][$this->term.'_id'];
			$text 	= $data[0][$this->term.'_fullname'].' - '.$data[0][$this->term.'_mobile'];
			return ['value' => $value, 'text' => $text];
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
			if(isset($_GET['branch']) && !empty($_GET['branch'])){
				$subsql .=" AND ".$this->term."_branch_id = ".$_GET['branch'];
				$record['search']['branch'] = $this->Branchmdl->get_search(['branch_id' => $_GET['branch']]);
			}
			if(isset($_GET['role']) && !empty($_GET['role'])){
				$subsql .=" AND ".$this->term."_role = '".$_GET['role']."'";
				$record['search']['role'] = $this->Commonmdl->get_role($_GET['role']);
			}
			if(isset($_GET['status'])){
				$status = $_GET['status'] == 2 ? 0 : $_GET['status'];
				$subsql .=" AND ".$this->term."_status = ".$status;
				$record['search']['status'] = $this->Commonmdl->get_status($_GET['status']);
			}
			$query ="
						SELECT user.user_id as id, UPPER(user.user_fullname) as name, user.user_mobile as mobile, 
						user.user_email as email, user.user_role as role, user.user_status as status,
						UPPER(branch.branch_name) as branch_name
						FROM ".$this->table." user
						LEFT JOIN branch_master branch ON(branch.branch_id = user.user_branch_id)
						WHERE user.user_type = 2
						AND user.user_role != 'OTHER'
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
			// echo "<pre>"; print_r($record); exit;

			return $record;
		}
		public function get_select2(){
			$subsql = "";

			if(isset($_GET['name']) && !empty($_GET['name'])){
				$name 	= $_GET['name'];
				$subsql .= " AND (".$this->term."_fullname LIKE '%".$name."%' OR ".$this->term."_mobile LIKE '%".$name."%') ";
			}
			$query ="
						SELECT ".$this->term."_id as id, CONCAT(UPPER(".$this->term."_fullname), '-', ".$this->term."_mobile) as name
						FROM ".$this->table."
						WHERE user_type = 2
						$subsql
						ORDER BY ".$this->term."_fullname ASC
						LIMIT 10
					";
			// echo $query; exit();
			return $this->db->query($query)->result_array();
		}
		public function get_select2_user(){
			$subsql = "";

			if(isset($_GET['name']) && !empty($_GET['name'])){
				$name 	= $_GET['name'];
				$subsql .= " AND (user.user_fullname LIKE '%".$name."%') ";
			}
			$query ="
						SELECT user.user_id as id, UPPER(user.user_fullname) as name
						FROM user_master user
						WHERE user.user_status = 1
						AND user.user_role = 'SALES'
						AND user.user_branch_id = ".$_SESSION['user_branch_id']."
						$subsql
						LIMIT 10
					";
			// echo $query; exit();
			return $this->db->query($query)->result_array();
		}
	}
?>