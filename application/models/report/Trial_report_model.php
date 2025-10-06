<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class trial_report_model extends CI_model{
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
           
			$query ="	SELECT sm.*,
						IF(st.st_trial>0,'YES','NO') as trial,  
						st.*,
						(st.st_sgst_amt + st.st_cgst_amt + st.st_igst_amt) as gst_amt,
						bm.bm_item_code,	 
						CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as account_name, 
						user.user_fullname,
						UPPER(design.design_name) as design_name,
						UPPER(style.style_name) as style_name,
						UPPER(brand.brand_name) as brand_name
						FROM sales_master sm
						INNER JOIN account_master acc ON(acc.account_id = sm.sm_acc_id)
						INNER JOIN user_master user ON(user.user_id = sm.sm_user_id)
						INNER JOIN sales_trans st ON(st.st_sm_id=sm.sm_id)
						INNER JOIN barcode_master bm ON(st.st_bm_id=bm.bm_id) 
						INNER JOIN design_master design ON(bm.bm_design_id=design.design_id) 
						LEFT JOIN style_master style ON(bm.bm_style_id=style.style_id) 
						LEFT JOIN brand_master brand ON(bm.bm_brand_id=brand.brand_id) 
						WHERE (style.style_name ='READYMADE' OR style.style_name ='KURTI' )
						AND sm.sm_branch_id = ".$_SESSION['user_branch_id']."
						AND sm.sm_fin_year = '".$_SESSION['fin_year']."'
						$subsql
						GROUP BY st.st_id
						ORDER BY sm.sm_id DESC";
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
					$sub_amt 	= $sub_amt + $value['st_sub_total'];
					$disc_amt 	= $disc_amt + $value['st_disc_amt'];
					$taxable_amt = $taxable_amt + $value['st_taxable_amt'];
					$gst_amt 	= $gst_amt 	+ $value['gst_amt'];
					$total_amt 	= $total_amt + $value['st_sub_total_amt'];
				}
			}
			$record['totals']['total_qty'] 	= $total_qty;
			$record['totals']['sub_amt'] 	= $sub_amt;
			$record['totals']['disc_amt'] 	= $disc_amt;
			$record['totals']['taxable_amt'] = $taxable_amt;
			$record['totals']['gst_amt'] 	= $gst_amt;
			$record['totals']['total_amt'] 	= $total_amt;
			return $record;
		}
	}
?>