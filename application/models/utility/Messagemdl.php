<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Messagemdl extends CI_model{
		protected $master;
		protected $trans;
		public function __construct(){
			parent::__construct();

			$this->master = 'message_master';
			$this->trans  = 'message_trans';
			$this->load->model('master/Usermdl');
		}
		public function isExist($id){
			
			return false;
		}
		public function get_record($condition, $wantDropDown = false){
			$record = [];
			$data 	= $this->db->get_where($this->master,$condition)->result_array();
			if(!$wantDropDown) return $data;
			if(empty($data)){
				$record[0] = 'NO ENTRY ADDED';
			}else{
				$record[0] = 'SELECT';
				foreach ($data as $key => $value) {
					$record[$value['om_id']] = strtoupper($value['om_entry_no']);
				}
			}
			return $record;
		}
		public function get_data($wantCount, $per_page = PER_PAGE, $offset = OFFSET){
			$record 	= [];
			$subsql 	= '';
			$limit  	= '';
			$ofset  	= '';
			
			if(!$wantCount){
				$limit .= " LIMIT $per_page";
				$ofset .= " OFFSET $offset";
			}
			
			if(isset($_GET['id']) && !empty($_GET['id'])){
				$subsql .= " AND om.om_id = ". $_GET['id'];
			}
			$query ="
						SELECT sms.*
						FROM ".$this->master." sms
						WHERE 1
						$subsql
						ORDER BY sms.mm_id ASC
						$limit
						$ofset
					";
			// echo "<pre>"; print_r($query); exit;
			if($wantCount){
				return $this->db->query($query)->num_rows();
			}
			$data = $this->db->query($query)->result_array();

			if(!empty($data)){
				foreach ($data as $key => $value) {
					$data[$key]['isExist'] = $this->isExist($value['mm_id']);
				}
			}
			return $data;
		}
		public function get_data_for_add(){
			$record['mm_entry_no'] = $this->db_operations->get_fin_year_branch_max_id($this->master, 'mm_entry_no', 'mm_fin_year', $_SESSION['fin_year'], 'mm_branch_id', $_SESSION['user_branch_id']);
			$data = $this->Usermdl->get_record(['user_id' => $_SESSION['user_id']]);
			$trial_mobile = '';
			if(!empty($data)){
				$trial_mobile = $data[0]['user_mobile'];
			}
			$record['trial_mobile'] 		= $trial_mobile;
			$record['groups'][''] 			= 'SELECT';
			$record['groups']['CUSTOMER'] 	= 'CUSTOMER';
			$record['groups']['SUPPLIER'] 	= 'SUPPLIER';
			return $record;
		}
		public function get_data_for_edit($mm_id){
			$master_query = "
                                SELECT sms.*
                                FROM ".$this->master." sms
                                WHERE sms.mm_id = $mm_id
                            ";
            $record['master_data'] = $this->db->query($master_query)->result_array();

            $trans_query = "
                                SELECT mt.*
                                FROM ".$this->trans." mt
                                WHERE mt.mt_mm_id = $mm_id
                            ";
            $record['trans_data'] = $this->db->query($trans_query)->result_array();

			$record['groups'][''] 			= 'SELECT';
			$record['groups']['CUSTOMER'] 	= 'CUSTOMER';
			$record['groups']['SUPPLIER'] 	= 'SUPPLIER';
            
            return $record; 
		}
	}
?>