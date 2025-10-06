<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Max_supplier_salemdl extends CI_model{
		protected $start_date;
		protected $end_date;
		public function __construct(){
			parent::__construct();

			$this->start_date 	= isset($_SESSION['start_year']) ? $_SESSION['start_year']." 00:00:01" : date('Y-m-d H:i:s');
			$this->end_date 	= isset($_SESSION['end_year']) ? $_SESSION['end_year']." 23:59:59" : date('Y-m-d H:i:s');

			$this->load->model('master/Accountmdl');
		}
		public function get_data(){
			$record = [];
			$subsql = "";
			$having = "";
			if(isset($_GET['from_date']) && !empty($_GET['from_date'])){
				$from_date = date('Y-m-d', strtotime($_GET['from_date']));
				$subsql .= " AND sm.sm_bill_date >= '".$from_date."'";
			}
			if(isset($_GET['to_date']) && !empty($_GET['to_date'])){
				$to_date = date('Y-m-d', strtotime($_GET['to_date']));
				$subsql .= " AND sm.sm_bill_date <= '".$to_date."'";
			}
			if(isset($_GET['bm_acc_id']) && !empty($_GET['bm_acc_id'])){
				$subsql .=" AND bm.bm_acc_id = ".$_GET['bm_acc_id'];
				$record['search']['bm_acc_id'] = $this->Accountmdl->get_name(['account_id' => $_GET['bm_acc_id']]);
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
			if(isset($_GET['from_sale'])){
				if($_GET['from_sale'] != ''){
					$having .=" AND st_rate >= ".$_GET['from_sale'];
				}
			}
			if(isset($_GET['to_sale'])){
				if($_GET['to_sale'] != ''){
					$having .=" AND st_rate <= ".$_GET['to_sale'];
				}
			}
			if(isset($_GET['from_disc'])){
				if($_GET['from_disc'] != ''){
					$having .=" AND st_disc >= ".$_GET['from_disc'];
				}
			}
			if(isset($_GET['to_disc'])){
				if($_GET['to_disc'] != ''){
					$having .=" AND st_disc <= ".$_GET['to_disc'];
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
						SELECT CONCAT(UPPER(acc.account_name), ' - ', UPPER(acc.account_code)) as account_name, 
						SUM(bm.bm_st_qty) as st_qty, SUM(bm.bm_st_rate) as st_rate, SUM(bm.bm_st_disc) as st_disc,
						(SUM(bm.bm_st_rate) - SUM(bm.bm_st_disc)) as st_amt
						FROM barcode_master bm
						INNER JOIN account_master acc ON(acc.account_id = bm.bm_acc_id)
						INNER JOIN sales_master sm ON(sm.sm_id = bm.bm_sm_id)
						WHERE bm.bm_delete_status = 0
						AND bm.bm_pm_id != 0
						AND bm.bm_branch_id = ".$_SESSION['user_branch_id']."
						AND sm.sm_created_at <= '".$this->end_date."'
						AND bm.bm_pt_qty - bm.bm_prt_qty = 1
						AND bm.bm_st_qty - bm.bm_srt_qty = 1
						$subsql
						GROUP BY acc.account_id
						HAVING 1
						$having
						ORDER BY st_qty DESC
					";
			// echo "<pre>"; print_r($query); exit;
			$record['data'] = $this->db->query($query)->result_array();
			$st_qty  		= 0;
			$st_rate  		= 0;
			$st_disc  		= 0;
			$st_amt  		= 0;
			
			if(!empty($record['data'])){
				foreach ($record['data'] as $key => $value) {
					$st_qty 	= $st_qty + $value['st_qty'];
					$st_rate 	= $st_rate + $value['st_rate'];
					$st_disc 	= $st_disc + $value['st_disc'];
					$st_amt 	= $st_amt + $value['st_amt'];
				}
			}
			$record['totals']['st_qty'] 	= $st_qty;
			$record['totals']['st_rate'] 	= $st_rate;
			$record['totals']['st_disc'] 	= $st_disc;
			$record['totals']['st_amt'] 	= $st_amt;
			// echo "<pre>"; print_r($record); exit;
			return $record;
		}
		
	}
?>