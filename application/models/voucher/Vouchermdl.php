<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Vouchermdl extends CI_model{
		protected $master;
		protected $trans;
		public function __construct(){
			parent::__construct();

			$this->master = 'voucher_master';
			$this->trans  = 'voucher_trans';

			$this->load->model('master/Accountmdl');
			$this->load->model('voucher/Receiptmdl');
			$this->load->model('voucher/Paymentmdl');
			$this->load->model('sales/Salesmdl');
			$this->load->model('sales/SalesReturnmdl');
			$this->config->load('extra');
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
		public function get_debited_balance($group, $id, $date, $flag = true){
			$receipt_amt = $this->Receiptmdl->get_debited_balance($group, $id, $date);
			$payment_amt = $this->Paymentmdl->get_debited_balance($group, $id, $date);
			$amt 		 = $flag ? $this->Salesmdl->get_debited_balance($id, $date) : 0;
			$amt_debited = $amt + $receipt_amt + $payment_amt;
			return $amt_debited;
        }
        public function get_debited_bal($group, $mode, $acc_id, $party_id, $date, $flag = true){
        	$db_data = $this->db_operations->get_record('account_master', ['account_constant' => 'DEBIT_NOTE', 'account_branch_id' => $_SESSION['user_branch_id']]);
        	$db_id 	 = !empty($db_data) ? $db_data[0]['account_id'] : 0;
			$receipt_amt = $this->Receiptmdl->get_debited_bal($group, $acc_id, $party_id, $date);
			$payment_amt = $this->Paymentmdl->get_debited_bal($group, $db_id, $party_id, $date);
			$amt 		 = $flag ? $this->Salesmdl->get_debited_bal($mode, $party_id, $date) : 0;
			$amt_debited = $amt + $receipt_amt + $payment_amt;
			return $amt_debited;
        }
        public function get_credited_balance($group, $id, $date, $flag = true){
			$receipt_amt = $this->Receiptmdl->get_credited_balance($group, $id, $date);
			$payment_amt = $this->Paymentmdl->get_credited_balance($group, $id, $date);
			$return_amt  = $flag ? $this->SalesReturnmdl->get_credited_balance($id, $date) : 0;
			$amt_credited= $return_amt + $receipt_amt + $payment_amt;
			return $amt_credited;
        }
        public function get_credited_bal($group, $mode, $acc_id, $party_id, $date, $flag = true){
        	$cb_data = $this->db_operations->get_record('account_master', ['account_constant' => 'CREDIT_NOTE', 'account_branch_id' => $_SESSION['user_branch_id']]);
        	$cb_id 	 = !empty($cb_data) ? $cb_data[0]['account_id'] : 0;
			$receipt_amt = $this->Receiptmdl->get_credited_bal($group, $cb_id, $party_id, $date);
			$payment_amt = $this->Paymentmdl->get_credited_bal($group, $acc_id, $party_id, $date);
			$return_amt  = $flag ? $this->SalesReturnmdl->get_credited_bal($mode, $party_id, $date) : 0;
			// echo $return_amt; exit;
			$amt_credited= $return_amt + $receipt_amt + $payment_amt;
			return $amt_credited;
        }
		/************** PAYMENT *****************/
			public function get_payment_data($wantCount, $per_page = PER_PAGE, $offset = OFFSET){
				$record 	= [];
				$subsql 	= '';
				$limit  	= '';
				$ofset  	= '';
				
				if(!$wantCount){
					$limit .= " LIMIT $per_page";
					$ofset .= " OFFSET $offset";
				}
				
				if(isset($_GET['id']) && !empty($_GET['id'])){
					$subsql .= " AND vm.vm_id = ". $_GET['id'];
				}
				$query ="
							SELECT vm.*, acc.account_name, party.account_name as party_name
							FROM ".$this->master." vm
							LEFT JOIN account_master acc ON(acc.account_id = vm.vm_acc_id)
							LEFT JOIN account_master party ON(party.account_id = vm.vm_party_id)
							WHERE vm.vm_branch_id = ".$_SESSION['user_branch_id']."
							AND vm.vm_type = 'PAYMENT'
							$subsql
							ORDER BY vm.vm_id ASC
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
						$data[$key]['isExist'] = $this->isExist($value['vm_id']);
					}
				}
				return $data;
			}
			public function get_payment_data_for_add(){
				$record['vm_entry_no'] = $this->db_operations->get_fin_year_branch_max_id($this->master, 'vm_entry_no', 'vm_fin_year', $_SESSION['fin_year'], 'vm_branch_id', $_SESSION['user_branch_id']);
				$record['accounts'] = $this->Accountmdl->get_record(['account_constant !=' => 'CREDIT_NOTE', 'account_group_id' => 9, 'account_status' => true, 'account_branch_id' => $_SESSION['user_branch_id']], true);
				$record['groups'] 	= $this->config->item('group');
				return $record;
			}
			public function get_payment_data_for_edit($id){
				$master_query="
								SELECT vm.*, party.account_name as party_name
								FROM voucher_master vm
								LEFT JOIN account_master party ON(party.account_id = vm.vm_party_id)
								WHERE vm.vm_id = $id
							  ";
				$trans_query ="
								SELECT vt.*, pm.*
								FROM voucher_trans vt
								LEFT JOIN purchase_master pm ON(pm.pm_id = vt.vt_pm_id)
								WHERE vt.vt_vm_id = $id
							  ";
				$record['master_data'] 	= $this->db->query($master_query)->result_array();
				$record['trans_data'] 	= $this->db->query($trans_query)->result_array();
				$record['accounts'] 	= $this->Accountmdl->get_record(['account_constant !=' => 'CREDIT_NOTE', 'account_group_id' => 9, 'account_status' => true, 'account_branch_id' => $_SESSION['user_branch_id']], true);
				$record['groups'] 		= $this->config->item('group');
				return $record;
			}
		/************** PAYMENT *****************/
		/************** RECEIPT *****************/
			public function get_receipt_data($wantCount, $per_page = PER_PAGE, $offset = OFFSET){
				$record 	= [];
				$subsql 	= '';
				$limit  	= '';
				$ofset  	= '';
				
				if(!$wantCount){
					$limit .= " LIMIT $per_page";
					$ofset .= " OFFSET $offset";
				}
				
				if(isset($_GET['id']) && !empty($_GET['id'])){
					$subsql .= " AND vm.vm_id = ". $_GET['id'];
				}
				$query ="
							SELECT vm.*, acc.account_name, party.account_name as party_name
							FROM ".$this->master." vm
							LEFT JOIN account_master acc ON(acc.account_id = vm.vm_acc_id)
							LEFT JOIN account_master party ON(party.account_id = vm.vm_party_id)
							WHERE vm.vm_branch_id = ".$_SESSION['user_branch_id']."
							AND vm.vm_type = 'RECEIPT'
							$subsql
							ORDER BY vm.vm_id ASC
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
						$data[$key]['isExist'] = $this->isExist($value['vm_id']);
					}
				}
				return $data;
			}
			public function get_receipt_data_for_add(){
				$record['vm_entry_no'] = $this->db_operations->get_fin_year_branch_max_id($this->master, 'vm_entry_no', 'vm_fin_year', $_SESSION['fin_year'], 'vm_branch_id', $_SESSION['user_branch_id']);
				$record['accounts'] = $this->Accountmdl->get_record(['account_constant !=' => 'DEBIT_NOTE', 'account_group_id' => 9, 'account_status' => true, 'account_branch_id' => $_SESSION['user_branch_id']], true);
				$record['groups'] 	= $this->config->item('group');
				return $record;
			}
			public function get_receipt_data_for_edit($id){
				$master_query="
								SELECT vm.*, party.account_name as party_name
								FROM voucher_master vm
								LEFT JOIN account_master party ON(party.account_id = vm.vm_party_id)
								WHERE vm.vm_id = $id
							  ";
				$trans_query ="
								SELECT vt.*, sm.*
								FROM voucher_trans vt
								LEFT JOIN sales_master sm ON(sm.sm_id = vt.vt_sm_id)
								WHERE vt.vt_vm_id = $id
							  ";
				$record['master_data'] 	= $this->db->query($master_query)->result_array();
				$record['trans_data'] 	= $this->db->query($trans_query)->result_array();
				$record['accounts'] = $this->Accountmdl->get_record(['account_constant !=' => 'DEBIT_NOTE', 'account_group_id' => 9, 'account_status' => true, 'account_branch_id' => $_SESSION['user_branch_id']], true);
				$record['groups'] 		= $this->config->item('group');
				return $record;
			}
		/************** RECEIPT *****************/
		
	}
?>