<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Purchase_return_summarymdl extends CI_model{
		public function __construct(){
			parent::__construct();

			$this->load->model('master/Accountmdl');
			$this->load->model('purchase/PurchaseReturnmdl');
		}
		public function get_data(){
			$subsql 	= '';
			if(isset($_GET['prm_entry_no']) && !empty($_GET['prm_entry_no'])){
				$subsql .=" AND prm.prm_id = ".$_GET['prm_entry_no'];
				$record['search']['prm_entry_no'] = $this->PurchaseReturnmdl->get_entry_no(['prm_id' => $_GET['prm_entry_no']]);
			}
			if(isset($_GET['from_entry_date']) && !empty($_GET['from_entry_date'])){
				$from_date = date('Y-m-d', strtotime($_GET['from_entry_date']));
				$subsql .= " AND prm.prm_entry_date >= '".$from_date."'";
			}
			if(isset($_GET['to_entry_date']) && !empty($_GET['to_entry_date'])){
				$to_date = date('Y-m-d', strtotime($_GET['to_entry_date']));
				$subsql .= " AND prm.prm_entry_date <= '".$to_date."'";
			}
			if(isset($_GET['prm_acc_id']) && !empty($_GET['prm_acc_id'])){
				$subsql .=" AND prm.prm_acc_id = ".$_GET['prm_acc_id'];
				$record['search']['prm_acc_id'] = $this->Accountmdl->get_search(['account_id' => $_GET['prm_acc_id']]);
			}
			if(isset($_GET['from_qty'])){
				if($_GET['from_qty'] != ''){
					$subsql .=" AND prm.prm_total_qty >= ".$_GET['from_qty'];
				}
			}
			if(isset($_GET['to_qty'])){
				if($_GET['to_qty'] != ''){
					$subsql .=" AND prm.prm_total_qty <= ".$_GET['to_qty'];
				}
			}
			if(isset($_GET['from_bill_amt'])){
				if($_GET['from_bill_amt'] != ''){
					$subsql .=" AND prm.prm_final_amt >= ".$_GET['from_bill_amt'];
				}
			}
			if(isset($_GET['to_bill_amt'])){
			 	if($_GET['to_bill_amt'] != ''){
					$subsql .=" AND prm.prm_final_amt <= ".$_GET['to_bill_amt'];
			 	}
			}
			$query ="
						SELECT prm.*, 
						(prm.prm_sgst_amt + prm.prm_cgst_amt + prm.prm_igst_amt) as prm_gst_amt,
						acc.account_name
						FROM purchase_return_master prm
						INNER JOIN account_master acc ON(acc.account_id = prm.prm_acc_id)
						WHERE prm.prm_branch_id = ".$_SESSION['user_branch_id']."
						AND prm.prm_fin_year = '".$_SESSION['fin_year']."'
						$subsql
						ORDER BY prm.prm_id DESC
					";
			// echo "<pre>"; print_r($query); exit;
			$record['data'] = $this->db->query($query)->result_array();
			$total_qty 	= 0;
			$sub_amt 	= 0;
			$off_amt 	= 0;
			$bdisc_amt 	= 0;
			$gst_amt 	= 0;
			$total_amt 	= 0;
			if(!empty($record['data'])){
				foreach ($record['data'] as $key => $value) {
					$total_qty 	= $total_qty + $value['prm_total_qty'];
					$sub_amt 	= $sub_amt + $value['prm_sub_total'];
					$off_amt 	= $off_amt + $value['prm_round_off'];
					$bdisc_amt 	= $bdisc_amt + $value['prm_bill_disc'];
					$gst_amt 	= $gst_amt + $value['prm_gst_amt'];
					$total_amt 	= $total_amt + $value['prm_final_amt'];
				}
			}
			$record['totals']['total_qty'] 	= $total_qty;
			$record['totals']['sub_amt'] 	= $sub_amt;
			$record['totals']['off_amt'] 	= $off_amt;
			$record['totals']['bdisc_amt'] 	= $bdisc_amt;
			$record['totals']['gst_amt'] 	= $gst_amt;
			$record['totals']['total_amt'] 	= $total_amt;
			// echo "<pre>"; print_r($record); exit;
			return $record;
		}
	}
?>