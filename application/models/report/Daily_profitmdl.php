<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Daily_profitmdl extends CI_model{
		protected $start_date;
		protected $end_date;
		public function __construct(){
			parent::__construct();
			$this->load->model('master/Stylemdl');
			
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
						INNER JOIN account_master acc ON(acc.account_id = vm.vm_acc_id)
						INNER JOIN account_master party ON(party.account_id = vm.vm_party_id)
						WHERE vm.vm_branch_id = ".$_SESSION['user_branch_id']."
						AND  vm.vm_created_at <= '".$this->end_date."'
						AND vm_type = 'PAYMENT'
						AND acc.account_constant NOT IN('DEBIT_NOTE', 'CREDIT_NOTE')
						AND party.account_constant NOT IN('CASH', 'BANK', 'DEBIT_NOTE', 'CREDIT_NOTE')
						AND vm.vm_entry_date >= '".$from_date."'
						AND vm.vm_entry_date <= '".$to_date."'
						AND party.account_group_id = 22
					";
			// echo "<pre>"; print_r($query); exit;
			$data = $this->db->query($query)->result_array();
			if(!empty($data)) return $data[0]['amt'];
			return 0;
		}
		public function get_data($flag = false, $date_from = '', $date_to = ''){
			$subsql 	= '';
			$having 	= '';
			$from_date 	= date('Y-m-d') < $_SESSION['start_year'] ? date('Y-m-d', strtotime($_SESSION['start_year'])) : date('Y-m-d');
			$to_date 	= date('Y-m-d') < $_SESSION['start_year'] ? date('Y-m-d', strtotime($_SESSION['start_year'])) : date('Y-m-d');

			if($flag){
				$from_date 	= $date_from;
				$to_date 	= $date_to;
			}
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
			
			if(isset($_GET['_bill_no']) && !empty($_GET['_bill_no'])){
				$subsql .= " AND sm.sm_bill_no  = '".$_GET['_bill_no']."'";
			}

			$sort = " ORDER BY sm.sm_bill_no"; 
			if(isset($_GET['_sort_by']) && !empty($_GET['_sort_by'])){
				$sort = " ORDER BY ".$_GET['_sort_by'].""; 
			}

			$order_by ='DESC';
			if(isset($_GET['_order_by']) && !empty($_GET['_order_by'])){
				$order_by = $_GET['_order_by'];
			}

			$query ="
						SELECT DATE_FORMAT(sm.sm_bill_date, '%d-%m-%Y') as entry_date,
						sm.sm_bill_no as entry_no, 
						UPPER(DAYNAME(sm.sm_bill_date)) as entry_day,
						UPPER(user.user_fullname) as user_fullname,
						UPPER(style.style_name) as style_name,
						SUM(sm.sm_point_used) as points,
						SUM(st.st_qty) as st_qty, 
						SUM(st.st_pt_rate) as pt_amt, 
						SUM(st.st_rate) st_rate, SUM(st.st_disc_amt) as st_disc, 
						SUM(st.st_sub_total_amt) as st_amt, SUM(st.st_sub_total_amt - st.st_pt_rate) as profit
						FROM sales_master sm 
						INNER JOIN sales_trans st ON(st.st_sm_id = sm.sm_id)
						LEFT JOIN style_master style ON(style.style_id=st.st_style_id)
						LEFT JOIN user_master user ON(user.user_id = sm.sm_user_id)
						WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
						AND sm.sm_created_at <= '".$this->end_date."'
						AND sm.sm_sales_type=0
						$subsql
						GROUP BY sm.sm_id DESC
						HAVING 1
						$having
						$sort $order_by
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

			return $flag ? $record['totals'] : $record;
		}
	
	}
?>