<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Monthly_profitmdl extends CI_model{
		protected $start_date;
		protected $end_date;
		public function __construct(){
			parent::__construct();

			$this->start_date 	= isset($_SESSION['start_year']) ? $_SESSION['start_year']." 00:00:01" : date('Y-m-d H:i:s');
			$this->end_date 	= isset($_SESSION['end_year']) ? $_SESSION['end_year']." 23:59:59" : date('Y-m-d H:i:s');
		} 
		public function get_data_for_return($from_date, $to_date){
			$query ="
						SELECT SUM(srt.srt_total_amt - srt.srt_pt_rate - srm.srm_bill_disc) as amt
						FROM sales_return_master srm
						INNER JOIN sales_return_trans srt ON(srt.srt_srm_id = srm.srm_id)
						INNER JOIN sales_trans st ON(st.st_id = srt.srt_st_id)
						INNER JOIN sales_master sm ON(sm.sm_id = srt.srt_sm_id)
						WHERE srm.srm_branch_id = ".$_SESSION['user_branch_id']."
						AND srm.srm_created_at <= '".$this->end_date."' 
						AND srm.srm_entry_date >= '".$from_date."'
						AND srm.srm_entry_date <= '".$to_date."'
						AND sm.sm_sales_type=0
						GROUP BY srm.srm_branch_id
					";
			// echo "<pre>"; print_r($query); exit;
			$data = $this->db->query($query)->result_array();
			if(!empty($data)) return $data[0]['amt'];
			return 0;
		}
		public function get_data_for_voucher($from_date, $to_date){
			$query ="
						SELECT SUM(vm.vm_total_amt) as amt
						FROM voucher_master vm
						INNER JOIN account_master acc ON(acc.account_id = vm.vm_party_id)
						WHERE vm.vm_branch_id = ".$_SESSION['user_branch_id']."
						AND  vm.vm_created_at <= '".$this->end_date."'
						AND vm_type = 'PAYMENT'
						AND vm.vm_acc_id NOT IN(3, 4)
						AND vm.vm_party_id NOT IN(1, 2, 3, 4)
						AND vm.vm_entry_date >= '".$from_date."'
						AND vm.vm_entry_date <= '".$to_date."'
						AND acc.account_group_id = 22
					";
			// echo "<pre>"; print_r($query); exit;
			$data = $this->db->query($query)->result_array();
			if(!empty($data)) return $data[0]['amt'];
			return 0;
		}
		public function get_data($flag = true){
			$subsql 	= '';
			$having 	= '';
			$from_date 	= date('Y-m-d') < $_SESSION['start_year'] ? date('Y-m-d') : date('Y-m-d', strtotime($_SESSION['start_year']));
			$to_date 	= date('Y-m-d') < $_SESSION['start_year'] ? date('Y-m-d', strtotime($_SESSION['end_year'])) : date('Y-m-d');
			if(isset($_GET['from_date']) && !empty($_GET['from_date'])){
				$from_date = date('Y-m-d', strtotime($_GET['from_date']));
				$subsql .= " AND sm.sm_bill_date >= '".$from_date."'";
			}else{
				$subsql .= " AND sm.sm_bill_date >= '".$from_date."'";;
			}
			if(isset($_GET['to_date']) && !empty($_GET['to_date'])){
				$to_date = date('Y-m-d', strtotime($_GET['to_date']));
				$subsql .= " AND sm.sm_bill_date <= '".$to_date."'";
			}else{
				$subsql .= " AND sm.sm_bill_date <= '".$to_date."'";
			}
			if(isset($_GET['sale_qty_from'])){
				if($_GET['sale_qty_from'] != ''){
					$having .= " AND st_qty >= ".$_GET['sale_qty_from'];
				}
			}
			if(isset($_GET['sale_qty_to'])){
				if($_GET['sale_qty_to'] != ''){
					$having .= " AND st_qty <= ".$_GET['sale_qty_to'];
				}
			}
			if(isset($_GET['st_amt_from'])){
				if($_GET['st_amt_from'] != ''){
					$having .= " AND st_amt >= ".$_GET['st_amt_from'];
				}
			}
			if(isset($_GET['st_amt_to'])){
				if($_GET['st_amt_to'] != ''){
					$having .= " AND st_amt <= ".$_GET['st_amt_to'];
				}
			}
			if(isset($_GET['profit_amt_from'])){
				if($_GET['profit_amt_from'] != ''){
					$having .= " AND profit >= ".$_GET['profit_amt_from'];
				}
			}
			if(isset($_GET['profit_amt_to'])){
				if($_GET['profit_amt_to'] != ''){
					$having .= " AND profit <= ".$_GET['profit_amt_to'];
				}
			}
			$query ="
						SELECT DATE_FORMAT(sm.sm_bill_date, '%M-%Y') as entry_date, UPPER(DAYNAME(sm.sm_bill_date)) as entry_day, SUM(sm.sm_point_used) as points,
						SUM(st.st_qty) as st_qty, SUM(st.st_pt_rate) as pt_amt,
						SUM(st.st_rate) st_rate, SUM(st.st_disc_amt) as st_disc, 
						SUM(st.st_sub_total_amt) as st_amt, SUM(st.st_sub_total_amt - st.st_pt_rate) as profit
						FROM sales_master sm
						INNER JOIN sales_trans st ON(st.st_sm_id = sm.sm_id)
						WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
						AND sm.sm_created_at <= '".$this->end_date."'
						AND sm.sm_sales_type=0
						$subsql
						GROUP BY YEAR(sm.sm_bill_date),MONTH(sm.sm_bill_date) DESC
						HAVING 1
						$having
						
					";
			// echo "<pre>"; print_r($query); exit;
			$record['data'] = $this->db->query($query)->result_array();
			// echo "<pre>"; print_r($record['data']); exit;
			
			$st_qty 	= 0;
			$pt_amt 	= 0;
			$st_rate 	= 0;
			$st_disc 	= 0;
			$st_amt 	= 0;
			$profit 	= 0;
			$points 	= 0;
			$expense 	= $this->get_data_for_voucher($from_date, $to_date);
			$srt_amt 	= $this->get_data_for_return($from_date, $to_date);
			if(!empty($record['data'])){
				foreach ($record['data'] as $key => $value) {
						$st_qty 	= $st_qty + $value['st_qty'];
						$pt_amt 	= $pt_amt + $value['pt_amt'];
						$st_rate 	= $st_rate + $value['st_rate'];
						$st_disc 	= $st_disc + $value['st_disc'];
						$st_amt 	= $st_amt + $value['st_amt'];
						$profit 	= $profit + $value['profit'];
						$points 	= $points + $value['points'];
	            }
			}
			// echo "<pre>"; print_r($record); exit;
			$record['totals']['st_qty'] 	= $st_qty;
			$record['totals']['pt_amt'] 	= $pt_amt;
			$record['totals']['st_rate'] 	= $st_rate;
			$record['totals']['st_disc'] 	= $st_disc;
			$record['totals']['st_amt'] 	= $st_amt;
			$record['totals']['profit'] 	= $profit;
			$record['totals']['points'] 	= $points;
			$record['totals']['expense'] 	= $expense;
			$record['totals']['srt_amt'] 	= $srt_amt;
			$record['totals']['profit_loss']= $profit - ($points + $expense + $srt_amt);
			// echo "<pre>"; print_r($record); exit;
			return $record;
		}
	
	}
?>