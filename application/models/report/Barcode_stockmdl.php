<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Barcode_stockmdl extends CI_model{
		protected $start_date;
		protected $end_date;
		public function __construct(){
			parent::__construct();

			$this->start_date 	= isset($_SESSION['start_year']) ? $_SESSION['start_year']." 00:00:01" : date('Y-m-d H:i:s');
			$this->end_date 	= isset($_SESSION['end_year']) ? $_SESSION['end_year']." 23:59:59" : date('Y-m-d H:i:s');

			$this->load->model('master/Accountmdl');
			$this->load->model('master/Barcodemdl');
			$this->load->model('master/Stylemdl');
			$this->load->model('master/designmdl');
			$this->load->model('master/Brandmdl');
			$this->load->model('master/Agemdl');
		}	
		public function get_data(){
				$record 	= [];
				$subsql 	= "";
				$having 	= "";
				$per_page 	= isset($_GET['per_page']) && !empty($_GET['per_page']) ? $_GET['per_page'] : PER_PAGE;
				$offset 	= isset($_GET['offset']) && !empty($_GET['offset']) ? $_GET['offset'] : OFFSET;
				$limit  	= " LIMIT $per_page";
				$ofset  	= " OFFSET $offset";
				if(isset($_GET['bm_acc_id']) && !empty($_GET['bm_acc_id'])){
					$subsql .=" AND bm.bm_acc_id = ".$_GET['bm_acc_id'];
					$record['search']['bm_acc_id'] = $this->Accountmdl->get_name(['account_id' => $_GET['bm_acc_id']]);
				}
				if(isset($_GET['bm_id']) && !empty($_GET['bm_id'])){
					$subsql .=" AND bm.bm_id = ".$_GET['bm_id'];
					$record['search']['bm_id'] = $this->Barcodemdl->get_search(['bm_id' => $_GET['bm_id']]);
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
				if(isset($_GET['bm_age_id']) && !empty($_GET['bm_age_id'])){
					$subsql .=" AND bm.bm_age_id = ".$_GET['bm_age_id'];
					$record['search']['bm_age_id'] = $this->Agemdl->get_search(['age_id' => $_GET['bm_age_id']]);
				}
				if(isset($_GET['pt_amt_frm'])){
					if($_GET['pt_amt_frm'] != ''){
						$having .=" AND pt_amt >= ".$_GET['pt_amt_frm'];
					}
				}
				if(isset($_GET['pt_amt_to'])){
					if($_GET['pt_amt_to'] != ''){
						$having .=" AND pt_amt <= ".$_GET['pt_amt_to'];
					}
				}
				if(isset($_GET['st_rate_frm'])){
					if($_GET['st_rate_frm'] != ''){
						$having .=" AND st_rate >= ".$_GET['st_rate_frm'];
					}
				}
				if(isset($_GET['st_rate_to'])){
					if($_GET['st_rate_to'] != ''){
						$having .=" AND st_rate <= ".$_GET['st_rate_to'];
					}
				}
				if(isset($_GET['st_amt_frm'])){
					if($_GET['st_amt_frm'] != ''){
						$having .=" AND st_amt >= ".$_GET['st_amt_frm'];
					}
				}
				if(isset($_GET['st_amt_to'])){
					if($_GET['st_amt_to'] != ''){
						$having .=" AND st_amt <= ".$_GET['st_amt_to'];
					}
				}
				if(isset($_GET['bal_qty_frm'])){
					if($_GET['bal_qty_frm'] != ''){
						$having .=" AND bal_qty >= ".$_GET['bal_qty_frm'];
					}
				}
				if(isset($_GET['bal_qty_to'])){
					if($_GET['bal_qty_to'] != ''){
						$having .=" AND bal_qty <= ".$_GET['bal_qty_to'];
					}
				}
				if(isset($_GET['bal_amt_frm'])){
					if($_GET['bal_amt_frm'] != ''){
						$having .=" AND bal_amt >= ".$_GET['bal_amt_frm'];
					}
				}
				if(isset($_GET['bal_amt_to'])){
					if($_GET['bal_amt_to'] != ''){
						$having .=" AND bal_amt <= ".$_GET['bal_amt_to'];
					}
				}
				if(isset($_GET['profit_frm'])){
					if($_GET['profit_frm'] != ''){
						$having .=" AND profit_amt >= ".$_GET['profit_frm'];
					}
				}
				if(isset($_GET['profit_to'])){
					if($_GET['profit_to'] != ''){
						$having .=" AND profit_amt <= ".$_GET['profit_to'];
					}
				}
				$extra = empty($subsql) && empty($having) ? " AND bm.bm_id = 0" : "";
				$query 	="
							SELECT bm.bm_id, pm.pm_id, bm.bm_item_code, CONCAT(UPPER(acc.account_code), ' - ', UPPER(acc.account_name)) as account_code, 
							UPPER(style.style_name) as style_name, 
							UPPER(design.design_name) as design_name,
							UPPER(brand.brand_name) as brand_name,
							UPPER(age.age_name) as age_name,
							bm.bm_pt_qty as pt_qty, (bm.bm_pt_rate - bm.bm_pt_disc) as pt_rate, 
							(bm.bm_pt_qty * (bm.bm_pt_rate - bm.bm_pt_disc)) as pt_amt, 
							bm.bm_prt_qty as prt_qty, 
							bm.bm_st_qty as st_qty, (bm.bm_sp_amt - bm.bm_st_disc) as st_rate, 
							(bm.bm_st_qty * (bm.bm_st_rate - bm.bm_st_disc)) as st_amt,  
							bm.bm_srt_qty as srt_qty,
							((bm.bm_pt_qty + bm.bm_srt_qty) - (bm.bm_st_qty + bm.bm_prt_qty)) as bal_qty,
							(((bm.bm_pt_qty + bm.bm_srt_qty) - (bm.bm_st_qty + bm.bm_prt_qty)) * (bm.bm_pt_rate - bm.bm_pt_disc)) as bal_amt,
							((bm.bm_st_qty - bm.bm_srt_qty) * (bm.bm_st_rate - bm.bm_st_disc)) - ((bm.bm_st_qty - bm.bm_srt_qty) * (bm.bm_pt_rate - bm.bm_pt_disc)) as profit_amt
							FROM barcode_master bm
							INNER JOIN purchase_master pm ON(pm.pm_id = bm.bm_pm_id)
							INNER JOIN account_master acc ON(acc.account_id = bm.bm_acc_id)
							INNER JOIN style_master style ON(style.style_id = bm.bm_style_id)
							INNER JOIN design_master design ON(design.design_id = bm.bm_design_id)
							INNER JOIN brand_master brand ON(brand.brand_id = bm.bm_brand_id)
							LEFT JOIN age_master age ON(age.age_id = bm.bm_age_id)
							WHERE bm.bm_branch_id = ".$_SESSION['user_branch_id']."
							AND pm.pm_created_at <= '".$this->end_date."' 
							AND bm.bm_delete_status = 0
							AND bm.bm_pm_id != 0
							$subsql
							$extra
							HAVING 1
							$having
							ORDER BY bm.bm_id DESC
						 ";
				// echo $query;exit;
				$record['data'] = $this->db->query($query)->result_array();
				$pt_qty  		= 0;
				$pt_amt  		= 0;
				$prt_qty  		= 0;
				$st_qty  		= 0;
				$st_amt  		= 0;
				$srt_qty   		= 0;
				$bal_qty  		= 0;
				$bal_amt  		= 0;
				$profit_amt  	= 0;
				
				if(!empty($record['data'])){
					foreach ($record['data'] as $key => $value) {
						$pt_qty 		= $pt_qty + $value['pt_qty'];
						$pt_amt 		= $pt_amt + $value['pt_amt'];
						$prt_qty 		= $prt_qty + $value['prt_qty'];
						$st_qty 		= $st_qty + $value['st_qty'];
						$st_amt 		= $st_amt + $value['st_amt'];
						$srt_qty 		= $srt_qty + $value['srt_qty'];
						$bal_qty 		= $bal_qty + $value['bal_qty'];
						$bal_amt 		= $bal_amt + $value['bal_amt'];
						$profit_amt		= $profit_amt + $value['profit_amt'];
					}
				}
				$record['totals']['pt_qty'] 		= $pt_qty;
				$record['totals']['pt_amt'] 		= $pt_amt;
				$record['totals']['prt_qty'] 		= $prt_qty;
				$record['totals']['st_qty'] 		= $st_qty;
				$record['totals']['st_amt'] 		= $st_amt;
				$record['totals']['srt_qty'] 		= $srt_qty;
				$record['totals']['bal_qty'] 		= $bal_qty;
				$record['totals']['bal_amt'] 		= $bal_amt;
				$record['totals']['profit_amt'] 	= $profit_amt;

				return $record;
		}
	}
?>