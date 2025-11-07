<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Daily_transactionmdl extends CI_model{
		protected $start_date;
		protected $end_date;
		public function __construct(){
			parent::__construct();

			$this->start_date 	= isset($_SESSION['start_year']) ? $_SESSION['start_year']." 00:00:01" : date('Y-m-d H:i:s');
			$this->end_date 	= isset($_SESSION['end_year']) ? $_SESSION['end_year']." 23:59:59" : date('Y-m-d H:i:s');

			$this->load->model('master/Accountmdl');
			$this->load->model('sales/Salesmdl');
			$this->load->model('sales/SalesReturnmdl');
			$this->load->model('voucher/Vouchermdl');
		}
		public function get_data($const = ''){ 
			$record 	= [];
			$subsql1 	= '';
			$subsql2 	= '';
			$subsql3 	= '';
			$from_date 	= date('Y-m-d') < $_SESSION['start_year'] ? date('Y-m-d', strtotime($_SESSION['start_year'])) : date('Y-m-d');
			$to_date 	= date('Y-m-d') < $_SESSION['start_year'] ? date('Y-m-d', strtotime($_SESSION['start_year'])) : date('Y-m-d');
			$branch_id 	= isset($_SESSION['user_branch_id']) ? $_SESSION['user_branch_id'] : 0;
			$constant 	= !empty($const) ? $const : 'CASH';
			if(isset($_GET['constant']) && !empty($_GET['constant'])){
				$constant 	= $_GET['constant'];
			}
			$mode = $constant;
			if(isset($_GET['from_date']) && !empty($_GET['from_date'])){
				$from_date = date('Y-m-d', strtotime($_GET['from_date']));
				$subsql1 .= " AND sm.sm_bill_date >= '".$from_date."'";
				$subsql2 .= " AND srm.srm_entry_date >= '".$from_date."'";
				$subsql3 .= " AND vm.vm_entry_date >= '".$from_date."'";
			}else{
				$subsql1 .= " AND sm.sm_bill_date >= '".$from_date."'";
				$subsql2 .= " AND srm.srm_entry_date >= '".$from_date."'";
				$subsql3 .= " AND vm.vm_entry_date >= '".$from_date."'";
			}
			if(isset($_GET['to_date']) && !empty($_GET['to_date'])){
				$to_date = date('Y-m-d', strtotime($_GET['to_date']));
				$subsql1 .= " AND sm.sm_bill_date <= '".$to_date."'";
				$subsql2 .= " AND srm.srm_entry_date <= '".$to_date."'";
				$subsql3 .= " AND vm.vm_entry_date <= '".$to_date."'";
			}else{
				$subsql1 .= " AND sm.sm_bill_date <= '".$to_date."'";
				$subsql2 .= " AND srm.srm_entry_date <= '".$to_date."'";
				$subsql3 .= " AND vm.vm_entry_date <= '".$to_date."'";
			}
			$cash_data 				= $this->db_operations->get_record('account_master', ['account_constant' => 'CASH', 'account_branch_id' => $branch_id]);
			$cash_id 				= !empty($cash_data) ? $cash_data[0]['account_id'] : 0;

			$bank_data 				= $this->db_operations->get_record('account_master', ['account_constant' => 'BANK', 'account_branch_id' => $branch_id]);
			$bank_id 				= !empty($bank_data) ? $bank_data[0]['account_id'] : 0;

			$account_data 			= $this->db_operations->get_record('account_master', ['account_constant' => $constant, 'account_branch_id' => $branch_id]);
			$drcr 					= !empty($account_data) ? $account_data[0]['account_drcr'] : 'CR';
			$account_id 			= !empty($account_data) ? $account_data[0]['account_id'] : 0;

			// ********** OPENING AMOUNT **********
				$debit_amt 				= $drcr == 'DR' ? $this->get_opening_balance($account_id, $from_date) : 0;
				$credit_amt 			= $drcr == 'CR' ? $this->get_opening_balance($account_id, $from_date) : 0;
				$record['drcr']			= $drcr;
				$record['debit_amt']	= $debit_amt;
				$record['credit_amt']	= $credit_amt;
				
				$debited_amt 			= $this->get_debited_amt($account_id, $from_date, $constant); 
				$record['debited_amt']	= $debited_amt;
				// echo "<pre>"; print_r($record); exit;

				$credited_amt 			= $this->get_credited_amt($account_id, $from_date, $constant); 
				$record['credited_amt']	= $credited_amt;
				// echo "<pre>"; print_r($record); exit;

				$open_amt 				= $drcr == 'DR' ? (($debit_amt + $debited_amt) - $credited_amt) : (($credit_amt + $credited_amt) - $debited_amt);
				$open_label 			= '';
				if($drcr == 'DR'){
					$open_label 		= $open_amt < 0 ? TO_PAY : TO_RECEIVE;
				}else{
					$open_label 		= $open_amt < 0 ? TO_RECEIVE : TO_PAY;
				}
				$record['open_bal']		= $open_amt;
				$record['open_label']	= $open_label;
				// echo "<pre>"; print_r($record); exit;
			// ********** OPENING AMOUNT **********

			$sales_amt				= !empty($constant) ? $this->get_sales_amt($constant, $from_date, $to_date) : 0; 
			$record['sales_amt']	= round($sales_amt, 2);
			// echo "<pre>"; print_r($record); exit;

			$return_amt				= !empty($constant) && $constant == 'CASH' ? $this->get_return_amt($from_date, $to_date) : 0; 
			$record['return_amt']	= round($return_amt, 2);
			// echo "<pre>"; print_r($record); exit;

			$receipt_amt			= 0;
			$payment_amt			= 0;
			$voucher_query ="
						SELECT vm.vm_acc_id, 
						vm.vm_party_id, 
						vm.vm_constant,
						vm.vm_entry_no as entry_no, 
						vm.vm_type as action, 
						vm.vm_created_at as created_at, 
						DATE_FORMAT(vm.vm_entry_date, '%d-%m-%Y') as entry_date, 
						IF(vm.vm_type = 'RECEIPT', (vm.vm_total_amt + vm.vm_round_off), 0) as amt_debited, 
						IF(vm.vm_type = 'PAYMENT', (vm.vm_total_amt + vm.vm_round_off), 0) as amt_credited, 
						CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as account_name,
						CONCAT(UPPER(party.account_name), ' - ', party.account_mobile) as party_name
						FROM voucher_master vm
						INNER JOIN account_master acc ON(acc.account_id = vm.vm_acc_id)
						INNER JOIN account_master party ON(party.account_id = vm.vm_party_id)
						WHERE (vm.vm_acc_id = $account_id OR vm.vm_party_id = $account_id)
						AND vm.vm_branch_id = ".$_SESSION['user_branch_id']."
						AND vm.vm_created_at <= '".$this->end_date."'
						$subsql3
						ORDER BY vm.vm_created_at ASC
					";
// 			echo "<pre>"; print_r($voucher_query); exit;
			$voucher_data = $this->db->query($voucher_query)->result_array();
			// echo "<pre>"; print_r($voucher_data); exit;
			if(!empty($voucher_data)){
				foreach ($voucher_data as $key => $value) {
					$amt_credited = 0;
					$amt_debited  = 0;
					$account_name = $value['party_name'];
					$action 	  = $value['action'];
					if($value['vm_constant'] == 'CREDIT_NOTE'){
						$amt_debited 	= $value['amt_credited'];
						$amt_credited 	= $value['amt_debited'];
					}else if($value['vm_constant'] == 'DEBIT_NOTE'){
						$amt_debited 	= $value['amt_credited'];
						$amt_credited 	= $value['amt_debited'];
					}else{
						if($value['vm_party_id'] == $account_id){
							if($value['action'] == RECEIPT){
								$amt_debited 	= $value['amt_credited'];
								$amt_credited 	= $value['amt_debited'];	
								$action 		= PAYMENT;
								$account_name 	= $value['account_name'];
							}else{
								$amt_debited 	= $value['amt_credited'];
								$amt_credited 	= $value['amt_debited'];	
								$action 		= RECEIPT;
								$account_name 	= $value['account_name'];
							}
						}else{
							$amt_debited 	= $value['amt_debited'];
							$amt_credited 	= $value['amt_credited'];
						}
					}
					$record['data'][strtotime($value['created_at'])]['created_at']		= strtotime($value['created_at']);
					$record['data'][strtotime($value['created_at'])]['account_name']	= $account_name;
					$record['data'][strtotime($value['created_at'])]['entry_no'] 		= $value['entry_no'];
					$record['data'][strtotime($value['created_at'])]['entry_date'] 		= $value['entry_date'];
					$record['data'][strtotime($value['created_at'])]['action'] 			= $action;
					$record['data'][strtotime($value['created_at'])]['amt_to_credit']	= 0;
					$record['data'][strtotime($value['created_at'])]['amt_to_debit'] 	= 0;
					$record['data'][strtotime($value['created_at'])]['amt_debited'] 	= round($amt_debited, 2);
					$record['data'][strtotime($value['created_at'])]['amt_credited']	= round($amt_credited, 2);
					$record['data'][strtotime($value['created_at'])]['bal_amt'] 		= 0;
					$record['data'][strtotime($value['created_at'])]['label'] 			= '';

					$receipt_amt = $receipt_amt + $amt_debited;
					$payment_amt = $payment_amt + $amt_credited;
				}
			}
			$record['receipt_amt']	= $receipt_amt;
			$record['payment_amt']	= $payment_amt;
			$close_amt				= ($open_amt + $sales_amt + $receipt_amt) - ($return_amt + $payment_amt);
			$close_label 			= $close_amt < 0 ? TO_RECEIVE : TO_PAY;
			if($drcr == 'DR'){
				$close_label 		= $close_amt < 0 ? TO_PAY : TO_RECEIVE;
			}
			$record['close_bal']	= $close_amt;
			// echo "<pre>"; print_r($record); exit;
			return $record;
		}
		public function get_opening_balance($acc_id, $date){
			$subsql = " AND acc.account_id = $acc_id";
			$query  ="
						SELECT IFNULL(acc.account_open_bal, 0) as amt
						FROM account_master acc
						WHERE acc.account_created_at < '$date 00:00:01'
						$subsql
					";
			// echo "<pre>"; print_r($query); exit;
			$data = $this->db->query($query)->result_array();
			// echo "<pre>"; print_r($data); exit;

			if(empty($data)) return 0;
			return $data[0]['amt'];
		}
		public function get_debited_amt($acc_id, $date, $constant){
			$amt 	 = 0;
			if($constant == 'CREDIT_NOTE'){
            	$query ="
	                        SELECT IFNULL(SUM(vm.vm_total_amt + vm.vm_round_off), 0) as amt
	                        FROM voucher_master vm
	                        WHERE vm.vm_type = 'RECEIPT' 
	                        AND vm.vm_acc_id = $acc_id
	                        AND vm.vm_entry_date < '$date'
	                        AND vm.vm_branch_id = ".$_SESSION['user_branch_id']."
	                    ";
	            // echo "<pre>"; print_r($query); exit;
	            $data = $this->db->query($query)->result_array();
	            // echo "<pre>"; print_r($data); exit;

	            if(!empty($data)){
	            	$amt = $amt + $data[0]['amt'];
	            }
			}else{
            	$query ="
	                        SELECT IFNULL(SUM(vm.vm_total_amt + vm.vm_round_off), 0) as amt
	                        FROM voucher_master vm
	                        WHERE vm.vm_type = 'PAYMENT' 
	                        AND vm.vm_acc_id = $acc_id
	                        AND vm.vm_entry_date < '$date'
	                        AND vm.vm_branch_id = ".$_SESSION['user_branch_id']."
	                    ";
	            // echo "<pre>"; print_r($query); exit;
	            $data = $this->db->query($query)->result_array();
	            // echo "<pre>"; print_r($data); exit;

	            if(!empty($data)){
	            	$amt = $amt + $data[0]['amt'];
	            }
	            $query ="
	                        SELECT IFNULL(SUM(vm.vm_total_amt + vm.vm_round_off), 0) as amt
	                        FROM voucher_master vm
	                        WHERE vm.vm_type = 'RECEIPT' 
	                        AND vm.vm_party_id = $acc_id
	                        AND vm.vm_entry_date < '$date'
	                        AND vm.vm_branch_id = ".$_SESSION['user_branch_id']."
	                    ";
	            // echo "<pre>"; print_r($query); exit;
	            $data = $this->db->query($query)->result_array();
	            // echo "<pre>"; print_r($data); exit;

	            if(!empty($data)){
	            	$amt = $amt + $data[0]['amt'];
	            }
	            if($constant == 'CASH'){
		            $query ="
		                        SELECT IFNULL(SUM(srm.srm_amt_paid), 0) as amt
		                        FROM sales_return_master srm
		                        WHERE srm.srm_branch_id = ".$_SESSION['user_branch_id']."
		                        AND srm.srm_entry_date < '$date'
		                    ";
		            // echo "<pre>"; print_r($query); exit;
		            $data = $this->db->query($query)->result_array();
		            // echo "<pre>"; print_r($data); exit;

		            if(!empty($data)){
		            	$amt = $amt + $data[0]['amt'];
		            }
	            }
			}
            return $amt;
        }
        public function get_credited_amt($acc_id, $date, $constant){
			$amt 	 = 0;
			if($constant == 'DEBIT_NOTE'){
            	$query ="
	                        SELECT IFNULL(SUM(vm.vm_total_amt + vm.vm_round_off), 0) as amt
	                        FROM voucher_master vm
	                        WHERE vm.vm_type = 'PAYMENT' 
	                        AND vm.vm_acc_id = $acc_id
	                        AND vm.vm_entry_date < '$date'
	                        AND vm.vm_branch_id = ".$_SESSION['user_branch_id']."
	                    ";
	            // echo "<pre>"; print_r($query); exit;
	            $data = $this->db->query($query)->result_array();
	            // echo "<pre>"; print_r($data); exit;

	            if(!empty($data)){
	            	$amt = $amt + $data[0]['amt'];
	            }
			}else{
				$query ="
	                        SELECT IFNULL(SUM(vm.vm_total_amt + vm.vm_round_off), 0) as amt
	                        FROM voucher_master vm
	                        WHERE vm.vm_type = 'RECEIPT' 
	                        AND vm.vm_acc_id = $acc_id
	                        AND vm.vm_entry_date < '$date'
	                        AND vm.vm_branch_id = ".$_SESSION['user_branch_id']."
	                    ";
	            // echo "<pre>"; print_r($query); exit;
	            $data = $this->db->query($query)->result_array();
	            // echo "<pre>"; print_r($data); exit;

	            if(!empty($data)){
	            	$amt = $amt + $data[0]['amt'];
	            }
	            $query ="
	                        SELECT IFNULL(SUM(vm.vm_total_amt + vm.vm_round_off), 0) as amt
	                        FROM voucher_master vm
	                        WHERE vm.vm_type = 'PAYMENT' 
	                        AND vm.vm_party_id = $acc_id
	                        AND vm.vm_entry_date < '$date'
	                        AND vm.vm_branch_id = ".$_SESSION['user_branch_id']."
	                    ";
	            // echo "<pre>"; print_r($query); exit;
	            $data = $this->db->query($query)->result_array();
	            // echo "<pre>"; print_r($data); exit;

	            if(!empty($data)){
	            	$amt = $amt + $data[0]['amt'];
	            }
	            $sales = $constant == 'BANK' ? " AND sm.sm_payment_mode != 'CASH'" : " AND sm.sm_payment_mode = '".$constant."'";
	            $query ="
	                        SELECT IFNULL(SUM(sm.sm_collected_amt - sm.sm_to_pay), 0) as amt
	                        FROM sales_master sm
	                        WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
	                        AND sm.sm_bill_date < '$date'
	                        AND sm.sm_sales_type=0
	                        $sales
	                    ";
	            // echo "<pre>"; print_r($query); exit;
	            $data = $this->db->query($query)->result_array();
	            // echo "<pre>"; print_r($data); exit;

	            if(!empty($data)){
	            	$amt = $amt + $data[0]['amt'];
	            }
			}
            return $amt;
        }
        public function get_sales_amt($mode, $from_date, $to_date){
            $subsql = '';
            if(!empty($mode)){
                if($mode == 'BANK'){
                    $subsql .=" AND sm.sm_payment_mode != 'CASH'";
                }else{
                    $subsql .=" AND sm.sm_payment_mode = '".$mode."'";
                }
            }
            if(!empty($from_date)){
                $subsql .=" AND sm.sm_bill_date >= '".$from_date."'";
            }
            if(!empty($to_date)){
                $subsql .=" AND sm.sm_bill_date <= '".$to_date."'";
            }
            $query ="
                        SELECT IFNULL(SUM(sm.sm_collected_amt - sm.sm_to_pay), 0) as amt
                        FROM sales_master sm
                        WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
                        AND sm.sm_sales_type=0
                        $subsql
                    ";
            // echo "<pre>"; print_r($query); exit;
            $data = $this->db->query($query)->result_array();
            // echo "<pre>"; print_r($data); exit;

            if(empty($data)) return 0;
            return $data[0]['amt'];
        }
        public function get_return_amt($from_date, $to_date){
            $subsql = '';
            if(!empty($from_date)){
                $subsql .=" AND srm.srm_entry_date >= '".$from_date."'";
            }
            if(!empty($to_date)){
                $subsql .=" AND srm.srm_entry_date <= '".$to_date."'";
            }
            $query ="
                        SELECT IFNULL(SUM(srm.srm_amt_paid), 0) as amt
                        FROM sales_return_master srm
                        WHERE srm.srm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                    ";
            // echo "<pre>"; print_r($query); exit;
            $data = $this->db->query($query)->result_array();
            // echo "<pre>"; print_r($data); exit;

            if(empty($data)) return 0;
            return $data[0]['amt'];
        }
	}
?>