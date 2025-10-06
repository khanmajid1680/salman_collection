<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Patch extends CI_Controller {
		public function __construct(){
			parent::__construct();
		}
		public function update_account(){
			$data = $this->db_operations->get_record('purchase_master', ['pm_branch_id' => 2]);
			if(!empty($data)){
				foreach ($data as $key => $value) {
					$account_data = $this->db_operations->get_record('account_master', ['account_id' => $value['pm_acc_id']]);
					if(!empty($account_data)){
						$amt_to_credit = $account_data[0]['account_amt_to_credit'] + $value['pm_final_amt'];
						$this->db_operations->data_update('account_master', ['account_amt_to_credit' => $amt_to_credit], 'account_id',  $account_data[0]['account_id']);
					}
				}
			}
		}
		public function update_grn_trans(){
			$data = $this->db->query("SELECT gt_id, gt_bm_id FROM grn_trans WHERE 1 ORDER BY gt_id ASC")->result_array();
			// echo "<pre>"; print_r($data); exit();
			if(!empty($data)){
				foreach ($data as $key => $value) {
					// echo "<pre>"; print_r($value); exit();
					$outward_trans = $this->db->query("SELECT ot_om_id, ot_id, ot_bm_id FROM outward_trans WHERE ot_bm_id = ".$value['gt_bm_id'])->result_array();
					if(!empty($outward_trans) && count($outward_trans) == 1){
						// echo "<pre>"; print_r($outward_trans); exit;
						$this->db_operations->data_update('grn_trans', ['gt_om_id' => $outward_trans[0]['ot_om_id'], 'gt_ot_id' => $outward_trans[0]['ot_id']], 'gt_id', $value['gt_id']);
					}
				}
			}
		}
		public function update_outward_trans(){
			$data = $this->db->query("SELECT gt_ot_id FROM grn_trans WHERE 1 ORDER BY gt_id ASC")->result_array();
			// echo "<pre>"; print_r($data); exit();
			if(!empty($data)){
				foreach ($data as $key => $value) {
					// echo "<pre>"; print_r($value); exit();
					$outward_trans = $this->db->query("SELECT ot_id FROM outward_trans WHERE ot_id = ".$value['gt_ot_id'])->result_array();
					if(!empty($outward_trans) && count($outward_trans) == 1){
						// echo "<pre>"; print_r($outward_trans); exit;
						$this->db_operations->data_update('outward_trans', ['ot_gt_qty' => 1], 'ot_id', $value['gt_ot_id']);
					}
				}
			}
		}
	}
?>