<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Temp extends CI_Controller {
		public function __construct(){
			parent::__construct();

			$this->load->model('purchase/Purchasemdl');
		}
		public function sales_cash(){
			$query = "
					SELECT SUM(sm_final_amt) as amt_to_credit, SUM(sm_collected_amt - sm_to_pay) as amt_credited 
					FROM sales_master 
					WHERE sm_payment_mode = 'CASH'";
			$cash = $this->db->query($query)->result_array();	
			echo "<pre>"; print_r($cash); exit;
			if(!empty($cash)){
				$this->db_operations->data_update('account_master', ['account_amt_to_credit' => $cash[0]['amt_to_credit'], 'account_amt_credited' => $cash[0]['amt_credited']], 'account_id', 1);
			}
		}
		public function sales_bank(){
			$query = "
					SELECT SUM(sm_final_amt) as amt_to_credit, SUM(sm_collected_amt - sm_to_pay) as amt_credited 
					FROM sales_master 
					WHERE sm_payment_mode != 'CASH'";
			$cash = $this->db->query($query)->result_array();	
			echo "<pre>"; print_r($cash); exit;
			
			if(!empty($cash)){
				$this->db_operations->data_update('account_master', ['account_amt_to_credit' => $cash[0]['amt_to_credit'], 'account_amt_credited' => $cash[0]['amt_credited']], 'account_id', 2);
			}
			// echo "<pre>"; print_r($cash); exit;
		}
		public function purchase_rate_in_sales(){
			$query ="
						SELECT st_id, st_bm_id
						FROM sales_trans
						WHERE 1
						ORDER BY st_id ASC
					";
			$data = $this->db->query($query)->result_array();
			if(!empty($data)){
				foreach ($data as $key => $value) {
					$pt_rate = $this->Purchasemdl->get_purchase_rate($value['st_bm_id']);
					$this->db_operations->data_update('sales_trans', ['st_pt_rate' => $pt_rate], 'st_id', $value['st_id']);
				}
			}
		}
		public function purchase_rate_in_sales_return(){
			$query ="
						SELECT srt_id, srt_bm_id
						FROM sales_return_trans
						WHERE 1
						ORDER BY srt_id ASC
					";
			$data = $this->db->query($query)->result_array();
			if(!empty($data)){
				foreach ($data as $key => $value) {
					$pt_rate = $this->Purchasemdl->get_purchase_rate($value['srt_bm_id']);
					$this->db_operations->data_update('sales_return_trans', ['srt_pt_rate' => $pt_rate], 'srt_id', $value['srt_id']);
				}
			}
		}
	}
?>