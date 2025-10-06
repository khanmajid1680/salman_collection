<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Sales_summarymdl extends CI_model{
		public function __construct(){
			parent::__construct();

			$this->load->model('master/Accountmdl');
			$this->load->model('master/Usermdl');
			$this->load->model('sales/Salesmdl');
		}
		public function get_data(){ 
			$subsql 	= '';
			if(isset($_GET['sm_bill_no']) && !empty($_GET['sm_bill_no'])){
                $subsql .=" AND sm.sm_id = ".$_GET['sm_bill_no'];
                $record['search']['sm_bill_no'] = $this->Salesmdl->get_bill_no(['sm_id' => $_GET['sm_bill_no']]);
            }
            if(isset($_GET['from_bill_date']) && !empty($_GET['from_bill_date'])){
				$from_date = date('Y-m-d', strtotime($_GET['from_bill_date']));
				$subsql .= " AND sm.sm_bill_date >= '".$from_date."'";
			}
			if(isset($_GET['to_bill_date']) && !empty($_GET['to_bill_date'])){
				$to_date = date('Y-m-d', strtotime($_GET['to_bill_date']));
				$subsql .= " AND sm.sm_bill_date <= '".$to_date."'";
			}
            if(isset($_GET['sm_acc_id']) && !empty($_GET['sm_acc_id'])){
                $subsql .=" AND sm.sm_acc_id = ".$_GET['sm_acc_id'];
                $record['search']['sm_acc_id'] = $this->Accountmdl->get_search(['account_id' => $_GET['sm_acc_id']]);
            }
            if(isset($_GET['sm_user_id']) && !empty($_GET['sm_user_id'])){
                $subsql .=" AND sm.sm_user_id = ".$_GET['sm_user_id'];
                $record['search']['sm_user_id'] = $this->Usermdl->get_search(['user_id' => $_GET['sm_user_id']]);
            }
            if(isset($_GET['from_qty'])){
            	if($_GET['from_qty'] != ''){
                	$subsql .=" AND sm.sm_total_qty >= ".$_GET['from_qty'];
            	}
            }
            if(isset($_GET['to_qty'])){
            	if($_GET['to_qty'] != ''){
                	$subsql .=" AND sm.sm_total_qty <= ".$_GET['to_qty'];
            	}
            }
            if(isset($_GET['from_bill_amt'])){
            	if($_GET['from_bill_amt'] != ''){
                	$subsql .=" AND sm.sm_final_amt >= ".$_GET['from_bill_amt'];
            	}
            }
            if(isset($_GET['to_bill_amt'])){
            	if($_GET['to_bill_amt'] != ''){
                	$subsql .=" AND sm.sm_final_amt <= ".$_GET['to_bill_amt'];
            	}
            }
            if(isset($_GET['sm_payment_mode']) && !empty($_GET['sm_payment_mode'])){
                $subsql .=" AND sm.sm_payment_mode = '".$_GET['sm_payment_mode']."'";
                $record['search']['sm_payment_mode'] = $this->Commonmdl->get_mode($_GET['sm_payment_mode']);
            }
			$query ="
						SELECT sm.*, 
						(sm.sm_sgst_amt + sm.sm_cgst_amt + sm.sm_igst_amt) as gst_amt,
						CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as account_name, user.user_fullname
						FROM sales_master sm
						INNER JOIN account_master acc ON(acc.account_id = sm.sm_acc_id)
						INNER JOIN user_master user ON(user.user_id = sm.sm_user_id)
						WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
						AND sm.sm_fin_year = '".$_SESSION['fin_year']."'
						$subsql
						ORDER BY sm.sm_id DESC
					";
			// echo "<pre>"; print_r($query); exit;
			$record['data'] = $this->db->query($query)->result_array();
			$total_qty 	= 0;
			$sub_amt 	= 0;
			$disc_amt 	= 0;
			$taxable_amt = 0;
			$gst_amt 	= 0;
			$promo_amt 	= 0;
			$point_amt 	= 0;
			$off_amt 	= 0;
			$total_amt 	= 0;
			if(!empty($record['data'])){ 
				foreach ($record['data'] as $key => $value) {
					$total_qty 	= $total_qty + $value['sm_total_qty'];
					$sub_amt 	= $sub_amt + $value['sm_sub_total'];
					$disc_amt 	= $disc_amt + $value['sm_total_disc'];
					$taxable_amt = $taxable_amt + $value['sm_taxable_amt'];
					$gst_amt 	= $gst_amt 	+ $value['gst_amt'];
					$promo_amt 	= $promo_amt + $value['sm_promo_disc'];
					$point_amt 	= $point_amt + $value['sm_point_used'];
					$off_amt 	= $off_amt + $value['sm_round_off'];
					$total_amt 	= $total_amt + $value['sm_final_amt'];
				}
			}
			$record['totals']['total_qty'] 	= $total_qty;
			$record['totals']['sub_amt'] 	= $sub_amt;
			$record['totals']['disc_amt'] 	= $disc_amt;
			$record['totals']['taxable_amt'] = $taxable_amt;
			$record['totals']['gst_amt'] 	= $gst_amt;

			$record['totals']['promo_amt'] 	= $promo_amt;
			$record['totals']['point_amt'] 	= $point_amt;
			$record['totals']['off_amt'] 	= $off_amt;
			$record['totals']['total_amt'] 	= $total_amt;
			return $record;
		}
	}
?>