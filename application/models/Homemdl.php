<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Homemdl extends CI_model{
		protected $start_date;
		protected $end_date;
		public function __construct(){
			parent::__construct();
			$this->start_date 	= isset($_SESSION['start_year']) ? $_SESSION['start_year']." 00:00:01" : date('Y-m-d H:i:s');
			$this->end_date 	= isset($_SESSION['end_year']) ? $_SESSION['end_year']." 23:59:59" : date('Y-m-d H:i:s');

			$this->load->model('report/Daily_profitmdl');
		}
		public function get_first(){
			$pur_query ="
							SELECT SUM(pm.pm_total_qty) as qty, SUM(pm.pm_return_qty) as ret_qty
							FROM purchase_master pm
							WHERE pm.pm_created_at <= '".$this->end_date."'
							AND pm.pm_branch_id = ".$_SESSION['user_branch_id']."
						";
			// echo "<pre>"; print_r($pur_query); exit;
			$pur_data = $this->db->query($pur_query)->result_array();
			// echo "<pre>"; print_r($pur_data); exit;

			$sale_query ="
				SELECT SUM(sm.sm_total_qty) as qty, SUM(sm.sm_return_qty) as ret_qty
				FROM sales_master sm
				WHERE sm.sm_created_at <= '".$this->end_date."'
				AND sm.sm_branch_id = ".$_SESSION['user_branch_id']."
			";
			$sale_data = $this->db->query($sale_query)->result_array();
			// echo "<pre>"; print_r($sale_data); exit;

			$out_query ="
				SELECT SUM(om.om_total_qty) as qty
				FROM outward_master om
				WHERE om.om_created_at <= '".$this->end_date."'
				AND om.om_branch_id = ".$_SESSION['user_branch_id']."
			";
			$out_data = $this->db->query($out_query)->result_array();
			// echo "<pre>"; print_r($out_data); exit;

			$grn_query ="
				SELECT SUM(om.om_gm_total_qty) as qty
				FROM outward_master om
				WHERE om.om_created_at <= '".$this->end_date."'
				AND om.om_branch = ".$_SESSION['user_branch_id']."
			";
			$grn_data = $this->db->query($grn_query)->result_array();
			// echo "<pre>"; print_r($sale_data); exit;

			$pur_qty  = 0;
			$pret_qty = 0;
			$sale_qty = 0;
			$sret_qty = 0;
			$out_qty  = 0;
			$grn_qty  = 0;

			if(!empty($pur_data)){
				$pur_qty  = $pur_data[0]['qty'];
				$pret_qty = $pur_data[0]['ret_qty'];
			}

			if(!empty($sale_data)){
				$sale_qty = $sale_data[0]['qty'];
				$sret_qty = $sale_data[0]['ret_qty'];
			}
			if(!empty($out_data)){
				$out_qty = $out_data[0]['qty'];
			}
			if(!empty($grn_data)){
				$grn_qty = $grn_data[0]['qty'];
			}
			$bal_qty = (($pur_qty - $pret_qty - $out_qty) - ($sale_qty - $sret_qty)) + $grn_qty;
			return ['pur_qty' 	=> number_format($pur_qty, 0), 
					'pret_qty' 	=> number_format($pret_qty, 0), 
					'sale_qty' 	=> number_format($sale_qty, 0), 
					'sret_qty' 	=> number_format($sret_qty, 0), 
					'out_qty' 	=> number_format($out_qty, 0), 
					'grn_qty' 	=> number_format($grn_qty, 0), 
					'bal_qty' 	=> number_format($bal_qty, 0)];
		}
		public function get_second(){
			$start_date = date('Y-m-01 00:00:00', strtotime('-5 Months'));
			$record = [];
			$query ="
						SELECT sm.sm_bill_date
						FROM sales_master sm
						WHERE sm.sm_created_at >= '".$start_date."'
						AND sm.sm_created_at <= '".$this->end_date."'
						AND sm.sm_branch_id = ".$_SESSION['user_branch_id']."
						GROUP BY YEAR(sm.sm_bill_date),MONTH(sm.sm_bill_date) ASC
					";
			// echo "<pre>"; print_r($query); exit;
			$data = $this->db->query($query)->result_array();
			$record = [];
			if(!empty($data)){
				foreach ($data as $key => $value) {
					$from_date = date('Y-m-01', strtotime($value['sm_bill_date']));
					$to_date = date('Y-m-t', strtotime($value['sm_bill_date']));
					$record[$key] = $this->Daily_profitmdl->get_data(true, $from_date, $to_date);  
					$record[$key]['month_year'] = date('M-Y', strtotime($value['sm_bill_date']));  
				}
			}
			return $record;
		}
		public function get_third(){
			$record = [];
			$style_query ="
						SELECT COUNT(bm.bm_style_id) as cnt, UPPER(style.style_name) as name
						FROM barcode_master bm
						INNER JOIN style_master style ON(style.style_id = bm.bm_style_id)
						WHERE bm.bm_delete_status = 0
						AND bm.bm_branch_id = ".$_SESSION['user_branch_id']."
						AND bm.bm_pt_qty = 1
						AND bm.bm_st_qty = 1
						AND bm.bm_srt_qty = 0
						GROUP BY bm.bm_style_id 
						ORDER BY cnt DESC
						LIMIT 1
					";
			// echo "<pre>"; print_r($style_query); exit;
			$style_data = $this->db->query($style_query)->result_array();
			if(!empty($style_data)){
				$record['style']['cnt'] = $style_data[0]['cnt'];
				$record['style']['name']= $style_data[0]['name'];
			}

			$brand_query ="
						SELECT COUNT(bm.bm_brand_id) as cnt, UPPER(brand.brand_name) as name
						FROM barcode_master bm
						INNER JOIN brand_master brand ON(brand.brand_id = bm.bm_brand_id)
						WHERE bm.bm_delete_status = 0
						AND bm.bm_branch_id = ".$_SESSION['user_branch_id']."
						AND bm.bm_pt_qty = 1
						AND bm.bm_st_qty = 1
						AND bm.bm_srt_qty = 0
						GROUP BY bm.bm_brand_id 
						ORDER BY cnt DESC
						LIMIT 1
					";
			// echo "<pre>"; print_r($brand_query); exit;
			$brand_data = $this->db->query($brand_query)->result_array();
			if(!empty($brand_data)){
				$record['brand']['cnt'] = $brand_data[0]['cnt'];
				$record['brand']['name']= $brand_data[0]['name'];
			}

			
			$design_query ="
						SELECT COUNT(bm.bm_design_id) as cnt, UPPER(design.design_name) as name
						FROM barcode_master bm
						INNER JOIN design_master design ON(design.design_id = bm.bm_design_id)
						WHERE bm.bm_delete_status = 0
						AND bm.bm_branch_id = ".$_SESSION['user_branch_id']."
						AND bm.bm_pt_qty = 1
						AND bm.bm_st_qty = 1
						AND bm.bm_srt_qty = 0
						GROUP BY bm.bm_design_id 
						ORDER BY cnt DESC
						LIMIT 1
					";
			// echo "<pre>"; print_r($design_query); exit;
			$design_data = $this->db->query($design_query)->result_array();
			if(!empty($design_data)){
				$record['design']['cnt'] = $design_data[0]['cnt'];
				$record['design']['name']= $design_data[0]['name'];
			}

			return $record;
		}
		public function get_fourth(){
			$start_date = date('Y-m-01', strtotime('-5 Months'));
			$record = [];
			$modes 	= $this->config->item('payment_mode'); 
			foreach ($modes as $key => $value) {
				$query ="
							SELECT DATE_FORMAT(sm.sm_bill_date, '%b-%Y') as month_year, SUM(sm.sm_final_amt) as amt
							FROM sales_master sm
							WHERE sm.sm_payment_mode = '".$key."'
							AND sm.sm_branch_id = ".$_SESSION['user_branch_id']."
							AND sm.sm_bill_date >= '".$start_date."'
							AND sm.sm_bill_date <= '".$_SESSION['end_year']."'
							GROUP BY YEAR(sm.sm_bill_date),MONTH(sm.sm_bill_date) ASC
						";
				// echo "<pre>"; print_r($query);
				$data = $this->db->query($query)->result_array();
				// echo "<pre>"; print_r($data);exit();
				if(!empty($data)){
					foreach ($data as $k => $v) {
						$record[$key][$v['month_year']] = $v['amt'];
					}
				}
			}
			// echo "<pre>"; print_r($record);exit();
			return $record;
		}
	}
?>