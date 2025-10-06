<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Monthly_summarymdl extends CI_model{
		public function __construct(){
			parent::__construct();
		}
		public function get_data_for_sales($date){
			$query ="
						SELECT SUM(sm.sm_total_qty) as qty, SUM(sm.sm_sub_total) as amt, 
						SUM(sm.sm_total_disc + sm.sm_promo_disc + sm.sm_point_used) as disc, SUM(sm.sm_final_amt) as final,
						COUNT(sm.sm_id) as bill
						FROM sales_master sm
						WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
						AND sm.sm_fin_year = '".$_SESSION['fin_year']."' 
						AND sm.sm_bill_date = '".$date."'
						GROUP BY sm.sm_bill_date
					";
			// echo "<pre>"; print_r($query); exit;
			$data = $this->db->query($query)->result_array();
			// echo "<pre>"; print_r($data); exit;
			$qty  = 0;
			$amt  = 0;
			$disc = 0;
			$final= 0;
			$bill = 0;
			if(!empty($data)) {
				$qty  = $data[0]['qty'];
				$amt  = $data[0]['amt'];
				$disc = $data[0]['disc'];
				$final= $data[0]['final'];
				$bill = $data[0]['bill'];
			}
			return ['qty' => $qty, 'amt' => $amt, 'disc' => $disc, 'final' => $final, 'bill' => $bill];
		}
		public function get_data_for_return($date){
			$query ="
						SELECT SUM(srm.srm_total_qty) as qty, SUM(srm.srm_sub_total) as amt, SUM(srm.srm_total_disc + srm.srm_bill_disc) as disc,
						SUM(srm.srm_final_amt) as final
						FROM sales_return_master srm
						WHERE srm.srm_branch_id = ".$_SESSION['user_branch_id']."
						AND srm.srm_fin_year = '".$_SESSION['fin_year']."' 
						AND srm.srm_entry_date = '".$date."'
						GROUP BY srm.srm_entry_date
					";
			// echo "<pre>"; print_r($query); exit;
			$data = $this->db->query($query)->result_array();
			// echo "<pre>"; print_r($data); exit;
			$qty   = 0;
			$amt   = 0;
			$disc  = 0;
			$final = 0;
			if(!empty($data)) {
				$qty   = $data[0]['qty'];
				$amt   = $data[0]['amt'];
				$disc  = $data[0]['disc'];
				$final = $data[0]['final'];
			}
			return ['qty' => $qty, 'amt' => $amt, 'disc' => $disc, 'final' => $final];
		}
		public function get_data(){
			$subsql 	= '';
			$having 	= '';
			$from_date 	= date('Y-m-01');
			$to_date 	= date('Y-m-t');
			if(isset($_GET['from_date']) && !empty($_GET['from_date'])){
				$from_date = date('Y-m-d', strtotime($_GET['from_date']));
				$subsql .= " AND ms.entry_date >= '".$from_date."'";
			}
			if(isset($_GET['to_date']) && !empty($_GET['to_date'])){
				$to_date = date('Y-m-d', strtotime($_GET['to_date']));
				$subsql .= " AND ms.entry_date <= '".$to_date."'";
			}
			$query ="
						SELECT ms.entry_date
						FROM (
								SELECT sm.sm_bill_date as entry_date
								FROM sales_master sm
								WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
								AND sm.sm_fin_year = '".$_SESSION['fin_year']."' 
								GROUP BY sm.sm_bill_date
								UNION
								SELECT srm.srm_entry_date as entry_date
								FROM sales_return_master srm
								WHERE srm.srm_branch_id = ".$_SESSION['user_branch_id']."
								AND srm.srm_fin_year = '".$_SESSION['fin_year']."'
								GROUP BY srm.srm_entry_date
						) as ms 
						WHERE 1
						$subsql
						ORDER BY ms.entry_date DESC
						
					";
			// echo "<pre>"; print_r($query); exit;
			$data = $this->db->query($query)->result_array();
			$st_qty 	= 0;
			$srt_qty 	= 0;
			$sale_qty 	= 0;
			$st_amt 	= 0;
			$st_disc 	= 0;
			$srt_amt 	= 0;
			$sale_amt 	= 0;
			$bill 		= 0;
			if(!empty($data)){
				foreach ($data as $key => $value) {
					$from_qty 	= true; 
					$to_qty 	= true; 
					$from_amt 	= true; 
					$to_amt 	= true; 
					$sales_data = $this->get_data_for_sales($value['entry_date']);
					$return_data= $this->get_data_for_return($value['entry_date']);
					if(isset($_GET['from_qty'])){
						if($_GET['from_qty'] != ''){
		                	if(($sales_data['qty'] - $return_data['qty']) >= $_GET['from_qty']){
		                		$from_qty = true;
		                	}else{
		                		$from_qty = false;
		                	}
						}
		            }
		            if(isset($_GET['to_qty'])){
		            	if($_GET['to_qty'] != ''){
		                	if(($sales_data['qty'] - $return_data['qty']) <= $_GET['to_qty']){
		                		$to_qty = true;
		                	}else{
		                		$to_qty = false;
		                	}
		            	}
		            }
		            if(isset($_GET['from_amt'])){
			            if($_GET['from_amt'] != ''){
		                	if(($sales_data['amt'] - ($sales_data['disc'] + $return_data['amt'])) >= $_GET['from_amt']){
		                		$from_amt = true;
		                	}else{
		                		$from_amt = false;
		                	}
						}
					}
					if(isset($_GET['to_amt'])){
		            	if($_GET['to_amt'] != ''){
		                	if(($sales_data['amt'] - ($sales_data['disc'] + $return_data['amt'])) <= $_GET['to_amt']){
		                		$to_amt = true;
		                	}else{
		                		$to_amt = false;
		                	}
		            	}
		            }

		            if($from_qty && $to_qty && $from_amt && $to_amt){
						$record['data'][$key]['entry_date'] = date('d-m-Y', strtotime($value['entry_date']));
						$record['data'][$key]['day'] 		= date('D', strtotime($value['entry_date']));
						$record['data'][$key]['st_qty'] 	= $sales_data['qty'];
						$record['data'][$key]['srt_qty'] 	= $return_data['qty'];
						$record['data'][$key]['sale_qty'] 	= $sales_data['qty'] - $return_data['qty'];
						$record['data'][$key]['st_amt'] 	= $sales_data['amt'];
						$record['data'][$key]['st_disc'] 	= $sales_data['disc'];
						$record['data'][$key]['srt_amt'] 	= $return_data['amt'];
						$record['data'][$key]['sale_amt'] 	= $sales_data['amt'] - ($sales_data['disc'] + $return_data['amt']);
						$record['data'][$key]['bill'] 		= $sales_data['bill'];

						$st_qty 	= $st_qty + $sales_data['qty'];
						$srt_qty 	= $srt_qty + $return_data['qty'];
						$sale_qty 	= $sale_qty + ($sales_data['qty'] - $return_data['qty']);
						$st_amt 	= $st_amt + $sales_data['amt'];
						$st_disc 	= $st_disc + $sales_data['disc'];
						$srt_amt 	= $srt_amt + $return_data['amt'];
						$sale_amt 	= $sale_amt + ($sales_data['amt'] - ($sales_data['disc'] + $return_data['amt']));
						$bill 		= $bill + $sales_data['bill'];
		            }
				}
			}
			// echo "<pre>"; print_r($record); exit;
			
			$record['totals']['st_qty'] 	= $st_qty;
			$record['totals']['srt_qty'] 	= $srt_qty;
			$record['totals']['sale_qty'] 	= $sale_qty;
			$record['totals']['st_amt'] 	= $st_amt;
			$record['totals']['st_disc'] 	= $st_disc;
			$record['totals']['srt_amt'] 	= $srt_amt;
			$record['totals']['sale_amt'] 	= $sale_amt;
			$record['totals']['bill'] 		= $bill;
			// echo "<pre>"; print_r($record); exit;
			return $record;
		}
	}
?>