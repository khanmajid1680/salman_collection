<?php defined('BASEPATH') OR exit('No direct script access allowed');

	class Accountmdl extends CI_model{
		protected $table;
		protected $term;
		public function __construct(){
			parent::__construct();

			$this->table = 'account_master';
			$this->term  = 'account';
			$this->load->model('master/Commonmdl');
		}
		public function get_record($condition, $wantDropDown = false){
			$record = [];
			$data 	= $this->db->get_where($this->table,$condition)->result_array();
			if(!$wantDropDown) return $data;
			if(empty($data)){
				$record[0] = 'NO ACCOUNT ADDED';
			}else{
				$record[0] = 'SELECT';
				foreach ($data as $key => $value) {
					$record[$value['account_id']] = strtoupper($value['account_name']);
				}
			}
			return $record;
		}		
		public function isExist($id){
			$data = $this->db->query("SELECT pm_id FROM purchase_master WHERE pm_acc_id = $id LIMIT 1")->result_array();
			if(!empty($data)) return true;

			$data = $this->db->query("SELECT sm_id FROM sales_master WHERE sm_acc_id = $id LIMIT 1")->result_array();
			if(!empty($data)) return true;

			$data = $this->db->query("SELECT vm_id FROM voucher_master WHERE vm_acc_id = $id AND vm_group != 'GENERAL' LIMIT 1")->result_array();
			if(!empty($data)) return true;

			$data = $this->db->query("SELECT vm_id FROM voucher_master WHERE vm_party_id = $id AND vm_group = 'GENERAL' LIMIT 1")->result_array();
			if(!empty($data)) return true;

			return false;
		}
		public function get_search($condition){
			$data 	= $this->db->get_where($this->table,$condition)->result_array();
			if(empty($data)) return ['value' => '', 'text' => ''];
			$value 	= $data[0][$this->term.'_id'];
			$text 	= $data[0][$this->term.'_code'].' - '.$data[0][$this->term.'_name'].' - '.$data[0][$this->term.'_mobile'];
			return ['value' => $value, 'text' => $text];
		}
		public function get_search_supplier($condition){
			$data 	= $this->db->get_where($this->table,$condition)->result_array();
			if(empty($data)) return ['value' => '', 'text' => ''];
			$value 	= $data[0][$this->term.'_id'];
			$text 	= $data[0][$this->term.'_code'].' - '.$data[0][$this->term.'_name'];
			return ['value' => $value, 'text' => $text];
		}
		public function get_code($condition){
			$data 	= $this->db->get_where($this->table,$condition)->result_array();
			if(empty($data)) return ['value' => '', 'text' => ''];
			$value 	= $data[0][$this->term.'_id'];
			$text 	= $data[0][$this->term.'_code'];
			return ['value' => $value, 'text' => $text];
		}
		public function get_name($condition){
			$data 	= $this->db->get_where($this->table,$condition)->result_array();
			if(empty($data)) return ['value' => '', 'text' => ''];
			$value 	= $data[0][$this->term.'_id'];
			$text 	= $data[0][$this->term.'_name'];
			return ['value' => $value, 'text' => $text];
		}
		public function get_master($wantCount, $type, $per_page = PER_PAGE, $offset = OFFSET){
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
			if(isset($_GET['drcr']) && !empty($_GET['drcr'])){
				$subsql .=" AND ".$this->term."_drcr = '".$_GET['drcr']."'";
				$record['search']['drcr'] = $this->Commonmdl->get_drcr($_GET['drcr']);
			}
			if((isset($_GET['from_amt']) && !empty($_GET['from_amt'])) && (isset($_GET['to_amt']) && !empty($_GET['to_amt']))){
				$subsql .=" AND account_closing_bal >= ".$_GET['from_amt'];
				$subsql .=" AND account_closing_bal <= ".$_GET['to_amt'];
			}
			if($type == 'CUSTOMER'){
				$subsql .=" AND acc.account_type = '".$type."'";
			}else{
				$subsql .=" AND acc.account_type = '".$type."'";
				$subsql .=" AND acc.account_branch_id = ".$_SESSION['user_branch_id'];
			}
			$query ="
						SELECT acc.account_id, UPPER(acc.account_name) as account_name, acc.account_status, acc.account_mobile,
						acc.account_drcr, acc.account_open_bal,
						IF(acc.account_drcr = 'DR', ((acc.account_open_bal + acc.account_amt_debited) - acc.account_amt_credited), ((acc.account_open_bal + acc.account_amt_credited) - acc.account_amt_debited)) as account_closing_bal, acc.account_constant
						FROM account_master acc
						WHERE 1
						$subsql
						ORDER BY acc.account_id DESC
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
					$record['data'][$key]['isExist'] = $this->isExist($value['account_id']);
					// if($value['account_drcr'] == 'CR'){
					// 	if($value['account_closing_bal'] < 0){
					// 		$record['data'][$key]['account_closing_bal'] = round(abs($value['account_closing_bal']), 2).' TO RECEIVE';
					// 	}else{
					// 		$record['data'][$key]['account_closing_bal'] = round($value['account_closing_bal'], 2).' TO PAY';
					// 	}
					// }else{
					// 	if($value['account_closing_bal'] < 0){
					// 		$record['data'][$key]['account_closing_bal'] = round(abs($value['account_closing_bal']), 2).' TO PAY';
					// 	}else{
					// 		$record['data'][$key]['account_closing_bal'] = round($value['account_closing_bal'], 2).' TO RECEIVE';
					// 	}
					// }
				}
			}
			return $record;
		}
		public function get_customer_data_with_loyalty($acc_id){
            $acc_query ="
                            SELECT acc.*,
                            IF(acc.account_state_id>1,1,0) as gst_type
                            FROM account_master acc
                            WHERE acc.account_id = $acc_id
                            AND acc.account_type = 'CUSTOMER'
                            AND acc.account_status = 1";
            $record['acc_data'] = $this->db->query($acc_query)->result_array();

            $loyalty_point_query ="
                                    SELECT SUM(lpm.lpm_point - lpm.lpm_point_used) as lpm_point 
                                    FROM loyalty_point_master lpm 
                                    WHERE lpm.lpm_acc_id = $acc_id 
                                    AND (lpm.lpm_point - lpm.lpm_point_used) > 0 
                                    AND lpm.lpm_exp_date >= '".date('Y-m-d')."' 
                                    GROUP BY lpm.lpm_acc_id 
                                    HAVING lpm_point > 199
                                ";
            $record['loyalty_point_data'] = $this->db->query($loyalty_point_query)->result_array();

            $sales_query="
            				SELECT sm_bill_date
            				FROM sales_master
            				WHERE sm_acc_id = $acc_id
            				ORDER BY sm_bill_date DESC
            				LIMIT 1
            			 ";
            $sales_data = $this->db->query($sales_query)->result_array();
            $allow_disc = true;
            if(!empty($sales_data)){
            	$sm_bill_date = $sales_data[0]['sm_bill_date'];
            	$record['sm_bill_date'] = ($sm_bill_date);
            	$sales_ret_query="
            				SELECT srm_entry_date
            				FROM sales_return_master
            				WHERE srm_acc_id = $acc_id
            				ORDER BY srm_entry_date DESC
            				LIMIT 1
            			 ";
            	$sales_ret_data = $this->db->query($sales_ret_query)->result_array();
            	if(!empty($sales_ret_data)){
            		$srm_entry_date = $sales_ret_data[0]['srm_entry_date'];
            		$record['srm_entry_date'] = ($srm_entry_date);
            		$allow_disc = strtotime($sm_bill_date) > strtotime($srm_entry_date);
            	}
            }
            $record['allow_disc'] = $allow_disc;
            return $record;
        }
		public function get_account_balance($id){
			$amt = 0;
			$type= '';
			$query ="
						SELECT acc.*
						FROM ".$this->table." acc
						WHERE acc.account_id = $id
					";
			$data  = $this->db->query($query)->result_array();
			// echo "<pre>"; print_r($data);die;
			if(!empty($data)){
				$opening_bal 		= $data[0]['account_open_bal'];
				$amt_to_credit 		= $data[0]['account_amt_to_credit'];
				$amt_credited 		= $data[0]['account_amt_credited'];
				$amt_to_debit 		= $data[0]['account_amt_to_debit'];
				$amt_debited 		= $data[0]['account_amt_debited'];
				$pay_amt 			= $data[0]['account_type'] != 'GENERAL' ? $amt_to_credit - $amt_credited : $amt_credited;
				$receive_amt 		= $data[0]['account_type'] != 'GENERAL' ? $amt_to_debit - $amt_debited : $amt_debited;
				if($data[0]['account_drcr'] == 'CR'){
					$closing_bal= $data[0]['account_type'] != 'GENERAL' ? ($opening_bal + $pay_amt) - $receive_amt : ($opening_bal + $receive_amt) - $pay_amt;
					$amt 	 	= $closing_bal;
					$type 		= TO_PAY;
					if($amt < 0){
						$amt = abs($amt);
						$type= TO_RECEIVE;
					}
				}else{
					$closing_bal= $data[0]['account_type'] != 'GENERAL' ? ($opening_bal + $receive_amt) - $pay_amt : ($opening_bal + $pay_amt) - $receive_amt;
					$amt 	 	= $closing_bal;
					$type 		= TO_RECEIVE;
					if($amt < 0){
						$amt = abs($amt);
						$type= TO_PAY;
					}
				}
			}
			return ['closing_bal' 	=> $closing_bal,
					'amt' 			=> $amt,
					'type' 			=> $type];
		}
		public function get_opening_balance($type, $id, $date){
			$subsql = '';
			if(!empty($id)){
				$subsql .=" AND acc.account_id = $id";
			}
			$query ="
						SELECT IFNULL(SUM(acc.account_open_bal), 0) as amt
						FROM account_master acc
						WHERE acc.account_created_at < '$date 00:00:01'
						AND acc.account_type = '$type'
						$subsql
					";
			// echo "<pre>"; print_r($query); exit;
			$data = $this->db->query($query)->result_array();
			// echo "<pre>"; print_r($data); exit;

			if(empty($data)) return 0;
			return $data[0]['amt'];
		}
		public function get_select2_supplier(){
			$subsql = "";

			if(isset($_GET['name']) && !empty($_GET['name'])){
				$name 	= $_GET['name'];
				$subsql .= " AND (acc.account_name LIKE '%".$name."%' OR acc.account_code LIKE '%".$name."%' OR acc.account_mobile LIKE '%".$name."%') ";
			}
			$query ="
						SELECT acc.account_id, CONCAT(UPPER(acc.account_code), ' - ', UPPER(acc.account_name)) as account_name
						FROM account_master acc
						WHERE acc.account_status = 1
						AND acc.account_type = 'SUPPLIER'
						AND acc.account_branch_id = ".$_SESSION['user_branch_id']."
						$subsql
						LIMIT 10
					";
			// echo $query; exit();
			return $this->db->query($query)->result_array();
		}
		public function get_select2_customer(){
			$subsql = "";

			if(isset($_GET['name']) && !empty($_GET['name'])){
				$name 	= $_GET['name'];
				$subsql .= " AND (acc.account_name LIKE '%".$name."%' OR acc.account_mobile LIKE '%".$name."%') ";
			}
			$query ="
						SELECT acc.account_id, CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as account_name, acc.account_branch_id, acc.account_constant
						FROM account_master acc
						WHERE acc.account_status = 1
						AND acc.account_type = 'CUSTOMER'
						$subsql
						LIMIT 10
					";
			// echo $query; exit();
			$data = $this->db->query($query)->result_array();
			$record = [];
			if(!empty($data)){
				foreach ($data as $key => $value) {
					if($value['account_constant'] == 'WALKIN'){
						if($value['account_branch_id'] == $_SESSION['user_branch_id']){
							$record[$key]['account_id'] 	= $value['account_id'];
							$record[$key]['account_name'] 	= $value['account_name'];
						}
					}else{
						$record[$key]['account_id'] 	= $value['account_id'];
						$record[$key]['account_name'] 	= $value['account_name'];
					}
				}
			}
			return $record;
		}
		public function get_subsql(){
			$subsql = " AND acc.account_status = 1";
			$select = "acc.account_id as id, CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as name";
			if(isset($_GET['name']) && !empty($_GET['name'])){
				$name 	= $_GET['name'];
				$subsql .= " AND (acc.account_name LIKE '%".$name."%' OR acc.account_mobile LIKE '%".$name."%') ";
			}
			if(isset($_GET['param']) && !empty($_GET['param'])){
				$type 	= $_GET['param'];
				$subsql .= " AND acc.account_type = '".$type."'";

				if($type == 'SUPPLIER'){
					$select = "acc.account_id as id, CONCAT(UPPER(acc.account_name), ' - ',UPPER(acc.account_code), ' - ', acc.account_mobile) as name";
					$subsql .= " AND acc.account_branch_id = ".$_SESSION['user_branch_id'];
				}
				if($type == 'GENERAL'){
					$select = "acc.account_id as id, UPPER(acc.account_name) as name";
					$subsql .= " AND acc.account_branch_id = ".$_SESSION['user_branch_id'];
				}
			}
			return ['subsql' => $subsql, 'select' => $select];
		}
		public function get_account_select2(){
			// echo "<pre>"; print_r($_GET); exit();
			$select_subsql = $this->get_subsql();
			$select = $select_subsql['select'];
			$subsql = $select_subsql['subsql'];
			$query ="
						SELECT $select, account_constant, account_branch_id
						FROM account_master acc
						WHERE 1
						$subsql
						LIMIT 10
					";
			// echo $query; exit();
			$data = $this->db->query($query)->result_array();
			$record = [];
			if(!empty($data)){
				foreach ($data as $key => $value) {
					if($value['account_constant'] == 'WALKIN'){
						if($value['account_branch_id'] == $_SESSION['user_branch_id']){
							$record[$key]['id'] 	= $value['id'];
							$record[$key]['name'] 	= $value['name'];
						}
					}else{
						$record[$key]['id'] 	= $value['id'];
						$record[$key]['name'] 	= $value['name'];
					}
				}
			}
			return $record;
		}
		public function get_select2(){
			$subsql = "";

			if(isset($_GET['name']) && !empty($_GET['name'])){
				$name 	= $_GET['name'];
				$subsql .= " AND (".$this->term."_name LIKE '%".$name."%' OR ".$this->term."_mobile LIKE '%".$name."%') ";
			}
			if(isset($_GET['param']) && !empty($_GET['param'])){
				$type 	= $_GET['param'];
				$subsql .= " AND ".$this->term."_type = '".$type."'";
				if($type != 'CUSTOMER'){
					$subsql .= " AND account_branch_id = ".$_SESSION['user_branch_id'];
				}
			}
			$query ="
						SELECT ".$this->term."_id as id, CONCAT(UPPER(".$this->term."_name), '-', ".$this->term."_mobile) as name
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