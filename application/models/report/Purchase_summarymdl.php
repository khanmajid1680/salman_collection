<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Purchase_summarymdl extends CI_model{
		public function __construct(){
			parent::__construct();

			$this->load->model('master/Accountmdl');
			$this->load->model('purchase/Purchasemdl');
		}
		public function get_data(){ 
			$subsql 	= '';
			$date_start = date('Y-m-01');
			$date_end 	= date('Y-m-t');
			if(isset($_GET['pm_entry_no']) && !empty($_GET['pm_entry_no'])){
				$subsql .=" AND pm.pm_id = ".$_GET['pm_entry_no'];
				$record['search']['pm_entry_no'] = $this->Purchasemdl->get_entry_no(['pm_id' => $_GET['pm_entry_no']]);
			}
			if(isset($_GET['from_entry_date']) && !empty($_GET['from_entry_date'])){
				$from_date = date('Y-m-d', strtotime($_GET['from_entry_date']));
				$subsql .= " AND pm.pm_entry_date >= '".$from_date."'";
			}
			if(isset($_GET['to_entry_date']) && !empty($_GET['to_entry_date'])){
				$to_date = date('Y-m-d', strtotime($_GET['to_entry_date']));
				$subsql .= " AND pm.pm_entry_date <= '".$to_date."'";
			}
			if(isset($_GET['pm_bill_no']) && !empty($_GET['pm_bill_no'])){
				$subsql .=" AND pm.pm_id = ".$_GET['pm_bill_no'];
				$record['search']['pm_bill_no'] = $this->Purchasemdl->get_bill_no(['pm_id' => $_GET['pm_bill_no']]);
			}
			if(isset($_GET['from_bill_date']) && !empty($_GET['from_bill_date'])){
				$from_date = date('Y-m-d', strtotime($_GET['from_bill_date']));
				$subsql .= " AND pm.pm_bill_date >= '".$from_date."'";
			}
			if(isset($_GET['to_bill_date']) && !empty($_GET['to_bill_date'])){
				$to_date = date('Y-m-d', strtotime($_GET['to_bill_date']));
				$subsql .= " AND pm.pm_bill_date <= '".$to_date."'";
			}
			if(isset($_GET['pm_acc_id']) && !empty($_GET['pm_acc_id'])){
				$subsql .=" AND pm.pm_acc_id = ".$_GET['pm_acc_id'];
				$record['search']['pm_acc_id'] = $this->Accountmdl->get_search(['account_id' => $_GET['pm_acc_id']]);
			}
			if(isset($_GET['from_qty'])){
				if($_GET['from_qty'] != ''){
					$subsql .=" AND pm.pm_total_qty >= ".$_GET['from_qty'];
				}
			}
			if(isset($_GET['to_qty'])){
				if($_GET['to_qty'] != ''){
					$subsql .=" AND pm.pm_total_qty <= ".$_GET['to_qty'];
				}
			}
			if(isset($_GET['from_bill_amt'])){
				if($_GET['from_bill_amt'] != ''){
					$subsql .=" AND pm.pm_final_amt >= ".$_GET['from_bill_amt'];
				}
			}
			if(isset($_GET['to_bill_amt'])){
				if($_GET['to_bill_amt'] != ''){
					$subsql .=" AND pm.pm_final_amt <= ".$_GET['to_bill_amt'];
				}
			}
			$query ="
						SELECT pm.*, 
						(pm.pm_sgst_amt + pm.pm_cgst_amt + pm.pm_igst_amt) as gst_amt,
						acc.account_name
						FROM purchase_master pm
						INNER JOIN account_master acc ON(acc.account_id = pm.pm_acc_id)
						WHERE pm.pm_branch_id = ".$_SESSION['user_branch_id']."
						AND pm.pm_fin_year = '".$_SESSION['fin_year']."'
						$subsql
						ORDER BY pm.pm_id DESC
					";
			// echo "<pre>"; print_r($query); exit;
			$record['data'] = $this->db->query($query)->result_array();
			$total_qty 	= 0;
			$sub_amt 	= 0;
			$disc_amt 	= 0;
			$off_amt 	= 0;
			$bdisc_amt 	= 0;
			$taxable_amt = 0;
			$gst_amt 	= 0;
			$total_amt 	= 0;
			if(!empty($record['data'])){
				foreach ($record['data'] as $key => $value) {
					$total_qty 	= $total_qty + $value['pm_total_qty'];
					$sub_amt 	= $sub_amt + $value['pm_sub_total'];
					$disc_amt 	= $disc_amt + $value['pm_total_disc'];
					$off_amt 	= $off_amt + $value['pm_round_off'];
					$bdisc_amt 	= $bdisc_amt + $value['pm_bill_disc'];
					$taxable_amt = $taxable_amt + $value['pm_taxable_amt'];
					$total_amt 	= $total_amt + $value['pm_final_amt'];
					$gst_amt 	= $gst_amt + $value['gst_amt'];
				}
			}

			$record['totals']['total_qty'] 	= $total_qty;
			$record['totals']['sub_amt'] 	= $sub_amt;
			$record['totals']['disc_amt'] 	= $disc_amt;
			$record['totals']['off_amt'] 	= $off_amt;
			$record['totals']['bdisc_amt'] 	= $bdisc_amt;
			$record['totals']['taxable_amt'] = $taxable_amt;
			$record['totals']['gst_amt'] 	= $gst_amt;
			$record['totals']['total_amt'] 	= $total_amt;
			// echo "<pre>"; print_r($record); exit;
			return $record;
		}
		
	}
?>