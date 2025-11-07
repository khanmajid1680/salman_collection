<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Today_salemdl extends CI_model{
		public function __construct(){
			parent::__construct();

			$this->load->model('master/Accountmdl');
			$this->load->model('master/Stylemdl');
			$this->load->model('master/designmdl');
			$this->load->model('master/Brandmdl');
		}
		public function get_data(){
			$record = [];
			$subsql 	= '';
			$having 	= '';
			$from_date 	= date('Y-m-d');
			$to_date 	= date('Y-m-d');
			if(isset($_GET['from_date']) && !empty($_GET['from_date'])){
				$from_date = date('Y-m-d', strtotime($_GET['from_date']));
				$subsql .= " AND sm.sm_bill_date >= '".$from_date."'";
			}else{
				$subsql .= " AND sm.sm_bill_date >= '".$from_date."'";
			}
			if(isset($_GET['to_date']) && !empty($_GET['to_date'])){
				$to_date = date('Y-m-d', strtotime($_GET['to_date']));
				$subsql .= " AND sm.sm_bill_date <= '".$to_date."'";
			}else{
				$subsql .= " AND sm.sm_bill_date <= '".$to_date."'";
			}
			if(isset($_GET['bm_acc_id']) && !empty($_GET['bm_acc_id'])){
				$subsql .=" AND bm.bm_acc_id = ".$_GET['bm_acc_id'];
				$record['search']['bm_acc_id'] = $this->Accountmdl->get_name(['account_id' => $_GET['bm_acc_id']]);
			}
			if(isset($_GET['bm_style_id']) && !empty($_GET['bm_style_id'])){
				$subsql .=" AND bm.bm_style_id = ".$_GET['bm_style_id'];
				$record['search']['bm_style_id'] = $this->Stylemdl->get_search(['style_id' => $_GET['bm_style_id']]);
			}
			if(isset($_GET['bm_design_id']) && !empty($_GET['bm_design_id'])){
				$subsql .=" AND bm.bm_design_id = ".$_GET['bm_design_id'];
				$record['search']['bm_design_id'] = $this->designmdl->get_search(['design_id' => $_GET['bm_design_id']]);
			}
			if(isset($_GET['bm_brand_id']) && !empty($_GET['bm_brand_id'])){
				$subsql .=" AND bm.bm_brand_id = ".$_GET['bm_brand_id'];
				$record['search']['bm_brand_id'] = $this->Brandmdl->get_search(['brand_id' => $_GET['bm_brand_id']]);
			}
			if(isset($_GET['from_qty'])){
				if($_GET['from_qty'] != ''){
					$having .=" AND st_qty >= ".$_GET['from_qty'];
				}
			}
			if(isset($_GET['to_qty'])){
				if($_GET['to_qty'] != ''){
					$having .=" AND st_qty <= ".$_GET['to_qty'];
				}
			}
			if(isset($_GET['from_amt'])){
				if($_GET['from_amt'] != ''){
					$having .=" AND st_amt >= ".$_GET['from_amt'];
				}
			}
			if(isset($_GET['to_amt'])){
				if($_GET['to_amt'] != ''){
					$having .=" AND st_amt <= ".$_GET['to_amt'];
				}
			}
			$query ="
						SELECT CONCAT(UPPER(acc.account_code), ' - ', UPPER(acc.account_name)) as account_name,
						UPPER(design.design_name) as design_name, UPPER(brand.brand_name) as brand_name, UPPER(style.style_name) as style_name,
						UPPER(age.age_name) as age_name, SUM(bm.bm_st_qty - bm.bm_srt_qty) as st_qty, 
						SUM((bm.bm_st_qty - bm.bm_srt_qty) * (bm.bm_st_rate - bm.bm_st_disc)) as st_amt
						FROM barcode_master bm 
						INNER JOIN sales_master sm ON(sm.sm_id = bm.bm_sm_id)
						INNER JOIN account_master acc ON(acc.account_id = bm.bm_acc_id)
						INNER JOIN design_master design ON(design.design_id = bm.bm_design_id)
						INNER JOIN brand_master brand ON(brand.brand_id = bm.bm_brand_id)
						INNER JOIN style_master style ON(style.style_id = bm.bm_style_id)
						LEFT JOIN age_master age ON(age.age_id = bm.bm_age_id)
						WHERE bm.bm_branch_id = ".$_SESSION['user_branch_id']."
						AND sm.sm_sales_type=0
						$subsql
						GROUP BY acc.account_id, design.design_id, brand.brand_id, style.style_id
						HAVING 1
						$having
						ORDER BY st_qty DESC, st_amt DESC
					";
			// echo "<pre>"; print_r($query); exit;
			$record['data'] = $this->db->query($query)->result_array();
			$st_qty  		= 0;
			$st_amt  		= 0;
			if(!empty($record['data'])){
				foreach ($record['data'] as $key => $value) {
					$st_qty 		= $st_qty + $value['st_qty'];
					$st_amt 		= $st_amt + $value['st_amt'];
				}
			}
			$record['totals']['st_qty'] 		= $st_qty;
			$record['totals']['st_amt'] 		= $st_amt;
			// echo "<pre>"; print_r($record); exit;
			return $record;
		}
	}
?>