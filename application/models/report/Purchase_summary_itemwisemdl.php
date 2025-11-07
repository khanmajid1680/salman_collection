<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Purchase_summary_itemwisemdl extends CI_model{
		public function __construct(){
			parent::__construct();
			$this->load->model('master/Accountmdl');
			$this->load->model('master/designmdl');
			$this->load->model('master/Stylemdl');
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
					$subsql .=" AND pt.pt_token_check= 1";
				}else{
					$subsql .=" AND pt.pt_token_check=0";
				}
			}
			
			$query ="
						SELECT pm.*, 
						UPPER(design.design_name) design_name,
						UPPER(style.style_name) style_name,
						UPPER(brand.brand_name) brand_name,
						UPPER(acc.account_name) as account_name,
						pt.pt_qty,
						pt.pt_rate,
						pt.pt_sub_total,
						pt.pt_disc_amt,
						pt.pt_taxable_amt,
						pt.pt_sgst_per,pt.pt_sgst_amt,
						pt.pt_cgst_per,pt.pt_cgst_amt,
						pt.pt_igst_per,pt.pt_igst_amt,
						pt.pt_sub_total_amt,
						pt.pt_sp_amt,
						pt.pt_mrp,
						pt.pt_token_amt
						FROM purchase_master pm
						INNER JOIN account_master acc ON(acc.account_id = pm.pm_acc_id)
						INNER JOIN purchase_trans pt ON(pt.pt_pm_id=pm.pm_id)
						INNER JOIN design_master design ON(pt.pt_design_id=design.design_id)
						LEFT JOIN style_master style ON(pt.pt_style_id=style.style_id)
						LEFT JOIN brand_master brand ON(pt.pt_brand_id=brand.brand_id)
						WHERE pm.pm_branch_id = ".$_SESSION['user_branch_id']."
						AND pm.pm_fin_year = '".$_SESSION['fin_year']."'
						$subsql
						GROUP BY pt.pt_id
						ORDER BY pm.pm_id DESC , design.design_name
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
			$token_amt 	= 0;

			if(!empty($record['data'])){
				foreach ($record['data'] as $key => $value) {
					$total_qty 	= $total_qty + $value['pt_qty'];
					$sub_amt 	= $sub_amt + $value['pt_sub_total'];
					$disc_amt 	= $disc_amt + $value['pt_disc_amt'];
					$taxable_amt 	= $taxable_amt + $value['pt_taxable_amt'];
					$sgst_amt 	= $sgst_amt + $value['pt_sgst_amt'];
					$cgst_amt = $cgst_amt + $value['pt_cgst_amt'];
					$igst_amt 	= $igst_amt + $value['pt_igst_amt'];
					$total_amt 	= $total_amt + $value['pt_sub_total_amt'];
					$token_amt 	= $token_amt + $value['pt_token_amt'];

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
			$record['totals']['token_amt'] 	= $token_amt;

			// echo "<pre>"; print_r($record); exit;
			return $record;
		}
		
	}
?>