<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Sales_return_summarymdl extends CI_model{
		public function __construct(){
			parent::__construct();

			$this->load->model('master/Accountmdl');
			$this->load->model('master/Usermdl');
			$this->load->model('master/Barcodemdl');
			$this->load->model('master/Stylemdl');
			$this->load->model('master/designmdl');
			$this->load->model('master/Brandmdl');
			$this->load->model('master/Agemdl');
			$this->load->model('sales/SalesReturnmdl');
		}
		public function get_data(){ 
			$subsql 	= '';
			if(isset($_GET['srm_entry_no']) && !empty($_GET['srm_entry_no'])){
                $subsql .=" AND srm.srm_id = ".$_GET['srm_entry_no'];
                $record['search']['srm_entry_no'] = $this->SalesReturnmdl->get_entry_no(['srm_id' => $_GET['srm_entry_no']]);
            }
            if(isset($_GET['from_entry_date']) && !empty($_GET['from_entry_date'])){
				$from_date = date('Y-m-d', strtotime($_GET['from_entry_date']));
				$subsql .= " AND srm.srm_entry_date >= '".$from_date."'";
			}
			if(isset($_GET['to_entry_date']) && !empty($_GET['to_entry_date'])){
				$to_date = date('Y-m-d', strtotime($_GET['to_entry_date']));
				$subsql .= " AND srm.srm_entry_date <= '".$to_date."'";
			}
            if(isset($_GET['srm_acc_id']) && !empty($_GET['srm_acc_id'])){
                $subsql .=" AND srm.srm_acc_id = ".$_GET['srm_acc_id'];
                $record['search']['srm_acc_id'] = $this->Accountmdl->get_search(['account_id' => $_GET['srm_acc_id']]);
            }
            if(isset($_GET['srt_user_id']) && !empty($_GET['srt_user_id'])){
                $subsql .=" AND srt.srt_user_id = ".$_GET['srt_user_id'];
                $record['search']['srt_user_id'] = $this->Usermdl->get_search(['user_id' => $_GET['srt_user_id']]);
            }
            if(isset($_GET['bm_id']) && !empty($_GET['bm_id'])){
					$subsql .=" AND bm.bm_id = ".$_GET['bm_id'];
					$record['search']['bm_id'] = $this->Barcodemdl->get_search(['bm_id' => $_GET['bm_id']]);
			}
			if(isset($_GET['style_id']) && !empty($_GET['style_id'])){
				$subsql .=" AND style.style_id = ".$_GET['style_id'];
				$record['search']['style_id'] = $this->Stylemdl->get_search(['style_id' => $_GET['style_id']]);
			}
			if(isset($_GET['brand_id']) && !empty($_GET['brand_id'])){
				$subsql .=" AND brand.brand_id = ".$_GET['brand_id'];
				$record['search']['brand_id'] = $this->Brandmdl->get_search(['brand_id' => $_GET['brand_id']]);
			}
			if(isset($_GET['design_id']) && !empty($_GET['design_id'])){
				$subsql .=" AND design.design_id = ".$_GET['design_id'];
				$record['search']['design_id'] = $this->designmdl->get_search(['design_id' => $_GET['design_id']]);
			}
			if(isset($_GET['age_id']) && !empty($_GET['age_id'])){
				$subsql .=" AND age.age_id = ".$_GET['age_id'];
				$record['search']['age_id'] = $this->Agemdl->get_search(['age_id' => $_GET['age_id']]);
			}
            if(isset($_GET['from_qty'])){
            	if($_GET['from_qty'] != ''){
                	$subsql .=" AND srm.srm_total_qty >= ".$_GET['from_qty'];
            	}
            }
            if(isset($_GET['to_qty'])){
        	 	if($_GET['to_qty'] != ''){
        			$subsql .=" AND srm.srm_total_qty <= ".$_GET['to_qty'];

        	 	}
            }
            if(isset($_GET['from_bill_amt'])){
            	if($_GET['from_bill_amt'] != ''){
                	$subsql .=" AND srt.srt_total_amt >= ".$_GET['from_bill_amt'];
            	}
            }
            if(isset($_GET['to_bill_amt'])){
            	if($_GET['to_bill_amt'] != ''){
                	$subsql .=" AND srt.srt_total_amt <= ".$_GET['to_bill_amt'];
            	}
            }
			$query ="
						SELECT srm.srm_entry_no, 
						srm.srm_entry_date, 
						bm.bm_item_code, 
						srt.srt_rate, 
						srt.srt_disc_amt,
						srt.srt_taxable_amt,
						(srt.srt_sgst_amt+srt.srt_cgst_amt+srt.srt_igst_amt) as gst_amt,
						srt.srt_total_amt,
						CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as account_name,
						UPPER(user.user_fullname) as user_fullname,
						UPPER(design.design_name) as design_name,
						UPPER(style.style_name) as style_name,
						UPPER(brand.brand_name) as brand_name,
						IFNULL(UPPER(age.age_name), '') as age_name
						FROM sales_return_master srm
						INNER JOIN account_master acc ON(acc.account_id = srm.srm_acc_id)
						INNER JOIN sales_return_trans srt ON(srt.srt_srm_id = srm.srm_id)
						INNER JOIN barcode_master bm ON(bm.bm_id = srt.srt_bm_id)
						INNER JOIN user_master user ON(user.user_id = srt.srt_user_id)
						INNER JOIN design_master design ON(design.design_id = bm.bm_design_id)
						INNER JOIN style_master style ON(style.style_id = bm.bm_style_id)
						INNER JOIN brand_master brand ON(brand.brand_id = bm.bm_brand_id)
						LEFT JOIN age_master age ON(age.age_id = bm.bm_age_id)
						WHERE srm.srm_branch_id = ".$_SESSION['user_branch_id']."
						AND srm.srm_fin_year = '".$_SESSION['fin_year']."'
						$subsql
						ORDER BY srm.srm_id DESC
					";
			$record['data'] = $this->db->query($query)->result_array();
			$total_qty 	= 0;
			$disc_amt 	= 0;
			$taxable_amt = 0;
			$gst_amt 	= 0;
			$total_amt 	= 0;
			if(!empty($record['data'])){ 
				$total_qty 	= count($record['data']);
				foreach ($record['data'] as $key => $value) {
					$disc_amt 		= $disc_amt + $value['srt_disc_amt'];
					$taxable_amt 	= $taxable_amt + $value['srt_taxable_amt'];
					$gst_amt 		= $gst_amt + $value['gst_amt'];
					$total_amt 	= $total_amt + $value['srt_total_amt'];
				}
			}

			$record['totals']['total_qty'] 	= $total_qty;
			$record['totals']['disc_amt'] 	= $disc_amt;
			$record['totals']['taxable_amt'] = $taxable_amt;
			$record['totals']['gst_amt'] 	= $gst_amt;
			$record['totals']['total_amt'] 	= $total_amt;
			// echo "<pre>"; print_r($record); exit;
			return $record;
		}
	}
?>