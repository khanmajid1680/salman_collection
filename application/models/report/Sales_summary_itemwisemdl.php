<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class sales_summary_itemwisemdl extends CI_model{
		public function __construct(){
			parent::__construct();
			$this->load->model('master/Accountmdl');
			$this->load->model('master/designmdl');
			$this->load->model('master/Stylemdl');
			$this->load->model('sales/salesmdl');
		}

		public function get_data(){ 
			$subsql 	= '';
			$date_start = date('Y-m-01');
			$date_end 	= date('Y-m-t');
			if(isset($_GET['sm_entry_no']) && !empty($_GET['sm_entry_no'])){
				$subsql .=" AND sm.sm_id = ".$_GET['sm_entry_no'];
				$record['search']['sm_entry_no'] = $this->salesmdl->get_entry_no(['sm_id' => $_GET['sm_entry_no']]);
			}
			if(isset($_GET['from_entry_date']) && !empty($_GET['from_entry_date'])){
				$from_date = date('Y-m-d', strtotime($_GET['from_entry_date']));
				$subsql .= " AND sm.sm_entry_date >= '".$from_date."'";
			}
			if(isset($_GET['to_entry_date']) && !empty($_GET['to_entry_date'])){
				$to_date = date('Y-m-d', strtotime($_GET['to_entry_date']));
				$subsql .= " AND sm.sm_entry_date <= '".$to_date."'";
			}
			if(isset($_GET['sm_bill_no']) && !empty($_GET['sm_bill_no'])){
				$subsql .=" AND sm.sm_id = ".$_GET['sm_bill_no'];
				$record['search']['sm_bill_no'] = $this->salesmdl->get_bill_no(['sm_id' => $_GET['sm_bill_no']]);
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

			if(isset($_GET['_design_name']) && !empty($_GET['_design_name'])){
				$subsql .=" AND design.design_id = ".$_GET['_design_name'];
				$record['search']['_design_name'] = $this->designmdl->get_search(['design_id' => $_GET['_design_name']]);
			}
			if(isset($_GET['_style_name']) && !empty($_GET['_style_name'])){
				$subsql .=" AND style.style_id = ".$_GET['_style_name'];
				$record['search']['_style_name'] = $this->Stylemdl->get_search(['style_id' => $_GET['_style_name']]);
			}

			if(isset($_GET['_token']) && !empty($_GET['_token'])){
				if($_GET['_token']=='YES'){
					$subsql .=" AND st.st_token_amt > 0";
				}else{
					$subsql .=" AND st.st_token_amt <1";
				}
			}
			
			$query ="
						SELECT sm.*, 
						CONCAT(IF(sm.sm_with_gst=1,'INV','EST'),'-',sm.sm_bill_no) as sm_bill_no,
						UPPER(design.design_name) design_name,
						UPPER(style.style_name) style_name,
						UPPER(brand.brand_name) brand_name,
						UPPER(acc.account_name) as account_name,
						st.st_qty,
						st.st_rate,
						st.st_sub_total,
						st.st_disc_amt,
						st.st_taxable_amt,
						st.st_sgst_per,st.st_sgst_amt,
						st.st_cgst_per,st.st_cgst_amt,
						st.st_igst_per,st.st_igst_amt,
						st.st_sub_total_amt
						FROM sales_master sm
						INNER JOIN account_master acc ON(acc.account_id = sm.sm_acc_id)
						INNER JOIN sales_trans st ON(st.st_sm_id=sm.sm_id)
						INNER JOIN barcode_master bm ON(st.st_bm_id=bm.bm_id)
						INNER JOIN design_master design ON(bm.bm_design_id=design.design_id)
						LEFT JOIN style_master style ON(st.st_style_id=style.style_id)
						LEFT JOIN brand_master brand ON(st.st_brand_id=brand.brand_id)
						WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
						AND sm.sm_fin_year = '".$_SESSION['fin_year']."'
						AND sm.sm_sales_type=0
						$subsql
						GROUP BY st.st_id
						ORDER BY sm.sm_id DESC , design.design_name ASC
					";
			// echo "<pre>"; print_r($query); exit;
			$record['data'] = $this->db->query($query)->result_array();
			$total_qty 	= 0;
			$sub_amt 	= 0;
			$disc_amt 	= 0;
			$taxable_amt = 0;
			$sgst_amt 	= 0;
			$cgst_amt = 0;
			$igst_amt 	= 0;
			$total_amt 	= 0;
			if(!empty($record['data'])){
				foreach ($record['data'] as $key => $value) {
					$total_qty 	= $total_qty + $value['st_qty'];
					$sub_amt 	= $sub_amt + $value['st_sub_total'];
					$disc_amt 	= $disc_amt + $value['st_disc_amt'];
					$taxable_amt 	= $taxable_amt + $value['st_taxable_amt'];
					$sgst_amt 	= $sgst_amt + $value['st_sgst_amt'];
					$cgst_amt = $cgst_amt + $value['st_cgst_amt'];
					$igst_amt 	= $igst_amt + $value['st_igst_amt'];
					$total_amt 	= $total_amt + $value['st_sub_total_amt'];
				}
			}

			$record['totals']['total_qty'] 	= $total_qty;
			$record['totals']['sub_amt'] 	= $sub_amt;
			$record['totals']['disc_amt'] 	= $disc_amt;
			$record['totals']['taxable_amt'] 	= $taxable_amt;
			$record['totals']['sgst_amt'] 	= $sgst_amt;
			$record['totals']['cgst_amt'] = $cgst_amt;
			$record['totals']['igst_amt'] 	= $igst_amt;
			$record['totals']['total_amt'] 	= $total_amt;

			// echo "<pre>"; print_r($record); exit;
			return $record;
		}
		
	}
?>