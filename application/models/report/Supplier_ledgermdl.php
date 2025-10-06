<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Supplier_ledgermdl extends CI_model{
		public function __construct(){
			parent::__construct();

			$this->load->model('master/Accountmdl');
			$this->load->model('purchase/Purchasemdl');
			$this->load->model('purchase/PurchaseReturnmdl');
			$this->load->model('voucher/Vouchermdl');
		}
		public function get_data(){
			$subsql1 	= '';
			$subsql2 	= '';
			$subsql3 	= '';
			$from_date 	= $_SESSION['start_year'];
			$to_date 	= $_SESSION['end_year'];
			$account_id = -1;
			$record 	= [];
			if(isset($_GET['acc_id']) && !empty($_GET['acc_id'])){
				$subsql1 .=" AND pm.pm_acc_id = ".$_GET['acc_id'];
				$subsql2 .=" AND prm.prm_acc_id = ".$_GET['acc_id'];
				$subsql3 .=" AND vm.vm_party_id = ".$_GET['acc_id'];
				$account_id = $_GET['acc_id'];
				$record['search']['acc_id'] = $this->Accountmdl->get_search_supplier(['account_id' => $_GET['acc_id']]);
			}else{
				$subsql1 .=" AND pm.pm_acc_id = 0";
				$subsql2 .=" AND prm.prm_acc_id = 0";
				$subsql3 .=" AND vm.vm_party_id = 0";
			}
			if(isset($_GET['from_date']) && !empty($_GET['from_date'])){
				$from_date = date('Y-m-d', strtotime($_GET['from_date']));
				$subsql1 .= " AND pm.pm_bill_date >= '".$from_date."'";
				$subsql2 .= " AND prm.prm_entry_date >= '".$from_date."'";
				$subsql3 .= " AND vm.vm_entry_date >= '".$from_date."'";
			}else{
				$subsql1 .= " AND pm.pm_bill_date >= '".$from_date."'";
				$subsql2 .= " AND prm.prm_entry_date >= '".$from_date."'";
				$subsql3 .= " AND vm.vm_entry_date >= '".$from_date."'";
			}
			if(isset($_GET['to_date']) && !empty($_GET['to_date'])){
				$to_date = date('Y-m-d', strtotime($_GET['to_date']));
				$subsql1 .= " AND pm.pm_bill_date <= '".$to_date."'";
				$subsql2 .= " AND prm.prm_entry_date <= '".$to_date."'";
				$subsql3 .= " AND vm.vm_entry_date <= '".$to_date."'";
			}else{
				$subsql1 .= " AND pm.pm_bill_date <= '".$to_date."'";
				$subsql2 .= " AND prm.prm_entry_date <= '".$to_date."'";
				$subsql3 .= " AND vm.vm_entry_date <= '".$to_date."'";
			}
			$open_amt 				= $this->Accountmdl->get_opening_balance('SUPPLIER', $account_id, $to_date); 
			$record['open_amt'] 	= $open_amt;
			$amt_to_credit 			= $this->Purchasemdl->get_credit_balance($account_id, $from_date); 
			$record['amt_to_credit']= $amt_to_credit;
			$amt_to_debit 			= $this->PurchaseReturnmdl->get_debit_balance($account_id, $from_date); 
			$record['amt_to_debit'] = $amt_to_debit;
			$amt_credited 			= $this->Vouchermdl->get_credited_balance('SUPPLIER', $account_id, $from_date, false); 
			$record['amt_credited']	= $amt_credited;
			$amt_debited 			= $this->Vouchermdl->get_debited_balance('SUPPLIER', $account_id, $from_date, false); 
			$record['amt_debited']	= $amt_credited;

			$open_amt 				= ($open_amt + ($amt_to_credit - $amt_credited)) - ($amt_to_debit - $amt_debited);
			$close_amt				= 0;
			$open_label 			= $open_amt < 0 ? TO_RECEIVE : TO_PAY;
			$close_label 			= $close_amt < 0 ? TO_RECEIVE : TO_PAY;
			$record['open_bal'] 	= abs($open_amt)." ".$open_label;
			$pur_query ="
						SELECT pm.pm_entry_no as entry_no, DATE_FORMAT(pm.pm_entry_date, '%d-%m-%Y') as entry_date, 
						pm.pm_final_amt as amt_to_credit, pm.pm_created_at as created_at,
						CONCAT(UPPER(acc.account_code), ' - ', UPPER(acc.account_name)) as account_name
						FROM purchase_master pm
						INNER JOIN account_master acc ON(acc.account_id = pm.pm_acc_id)
						WHERE pm.pm_branch_id = ".$_SESSION['user_branch_id']."
						AND pm.pm_fin_year = '".$_SESSION['fin_year']."'
						$subsql1
						ORDER BY pm.pm_created_at ASC
					";
			// echo "<pre>"; print_r($pur_query); exit;
			$pur_data = $this->db->query($pur_query)->result_array();
			if(!empty($pur_data)){
				foreach ($pur_data as $key => $value) {
					$record['data'][strtotime($value['created_at'])]['created_at']		= strtotime($value['created_at']);
					$record['data'][strtotime($value['created_at'])]['account_name']	= $value['account_name'];
					$record['data'][strtotime($value['created_at'])]['entry_no'] 		= $value['entry_no'];
					$record['data'][strtotime($value['created_at'])]['entry_date'] 		= $value['entry_date'];
					$record['data'][strtotime($value['created_at'])]['action'] 			= 'PURCHASE';
					$record['data'][strtotime($value['created_at'])]['amt_to_debit']	= 0;
					$record['data'][strtotime($value['created_at'])]['amt_debited'] 	= 0;
					$record['data'][strtotime($value['created_at'])]['amt_to_credit']	= $value['amt_to_credit'];
					$record['data'][strtotime($value['created_at'])]['amt_credited'] 	= 0;
					$record['data'][strtotime($value['created_at'])]['bal_amt'] 		= 0;
					$record['data'][strtotime($value['created_at'])]['label'] 			= '';
				}
			}

			$return_query ="
						SELECT prm.prm_entry_no as entry_no, DATE_FORMAT(prm.prm_entry_date, '%d-%m-%Y') as entry_date, 
						prm.prm_final_amt as amt_to_debit, prm.prm_created_at as created_at,
						CONCAT(UPPER(acc.account_code), ' - ', UPPER(acc.account_name)) as account_name
						FROM purchase_return_master prm
						INNER JOIN account_master acc ON(acc.account_id = prm.prm_acc_id)
						WHERE prm.prm_branch_id = ".$_SESSION['user_branch_id']."
						AND prm.prm_fin_year = '".$_SESSION['fin_year']."'
						$subsql2
						ORDER BY prm.prm_created_at ASC
					";
			// echo "<pre>"; print_r($return_query); exit;
			$return_data = $this->db->query($return_query)->result_array();
			if(!empty($return_data)){
				foreach ($return_data as $key => $value) {
					$record['data'][strtotime($value['created_at'])]['created_at']		= strtotime($value['created_at']);
					$record['data'][strtotime($value['created_at'])]['account_name']	= $value['account_name'];
					$record['data'][strtotime($value['created_at'])]['entry_no'] 		= $value['entry_no'];
					$record['data'][strtotime($value['created_at'])]['entry_date'] 		= $value['entry_date'];
					$record['data'][strtotime($value['created_at'])]['action'] 			= 'PURCHASE RETURN';
					$record['data'][strtotime($value['created_at'])]['amt_to_credit'] 	= 0;
					$record['data'][strtotime($value['created_at'])]['amt_credited']	= 0;
					$record['data'][strtotime($value['created_at'])]['amt_to_debit'] 	= $value['amt_to_debit'];
					$record['data'][strtotime($value['created_at'])]['amt_debited'] 	= 0;
					$record['data'][strtotime($value['created_at'])]['bal_amt'] 		= 0;
					$record['data'][strtotime($value['created_at'])]['label'] 			= '';
				}
			}
			$voucher_query ="
						SELECT vm.vm_acc_id, vm.vm_entry_no as entry_no, DATE_FORMAT(vm.vm_entry_date, '%d-%m-%Y') as entry_date, 
						vm.vm_type as action, IF(vm.vm_type = 'RECEIPT', (vm.vm_total_amt + vm.vm_round_off), 0) as amt_debited, vm.vm_constant,
						IF(vm.vm_type = 'PAYMENT', (vm.vm_total_amt + vm.vm_round_off), 0) as amt_credited, vm.vm_created_at as created_at, 
						CONCAT(UPPER(acc.account_code), ' - ', UPPER(acc.account_name)) as account_name
						FROM voucher_master vm
						INNER JOIN account_master acc ON(acc.account_id = vm.vm_party_id)
						WHERE vm.vm_branch_id = ".$_SESSION['user_branch_id']."
						AND vm.vm_fin_year = '".$_SESSION['fin_year']."'
						AND vm.vm_group = 'SUPPLIER'
						$subsql3
						ORDER BY vm.vm_created_at ASC
					";
			// echo "<pre>"; print_r($voucher_query); exit;
			$voucher_data = $this->db->query($voucher_query)->result_array();
			if(!empty($voucher_data)){
				foreach ($voucher_data as $key => $value) {
					$amt_credited = 0;
					$amt_debited  = 0;
					if($value['vm_constant'] == 'CREDIT_NOTE'){
						$amt_debited 	= $value['amt_credited'];
						$amt_credited 	= $value['amt_debited'];
					}else if($value['vm_constant'] == 'DEBIT_NOTE'){
						$amt_debited 	= $value['amt_credited'];
						$amt_credited 	= $value['amt_debited'];
					}else{
						$amt_debited 	= $value['amt_debited'];
						$amt_credited 	= $value['amt_credited'];
					}
					$record['data'][strtotime($value['created_at'])]['created_at']		= strtotime($value['created_at']);
					$record['data'][strtotime($value['created_at'])]['account_name']	= $value['account_name'];
					$record['data'][strtotime($value['created_at'])]['entry_no'] 		= $value['entry_no'];
					$record['data'][strtotime($value['created_at'])]['entry_date'] 		= $value['entry_date'];
					$record['data'][strtotime($value['created_at'])]['action'] 			= $value['action'];
					$record['data'][strtotime($value['created_at'])]['amt_to_credit']	= 0;
					$record['data'][strtotime($value['created_at'])]['amt_to_debit'] 	= 0;
					$record['data'][strtotime($value['created_at'])]['amt_debited'] 	= $amt_debited;
					$record['data'][strtotime($value['created_at'])]['amt_credited']	= $amt_credited;
					$record['data'][strtotime($value['created_at'])]['bal_amt'] 		= 0;
					$record['data'][strtotime($value['created_at'])]['label'] 			= '';
				}
			}
			if(!empty($record['data'])){
				usort($record['data'], function($a, $b){
					if ($a == $b) return 0;
		        	return ($a['created_at'] < $b['created_at']) ? -1 : 1;
				});
				$close_amt = $open_amt;
				foreach ($record['data'] as $key => $value) {
					$amt_to_debit 	= $value['amt_to_debit'];
					$amt_debited 	= $value['amt_debited'];
					$amt_to_credit 	= $value['amt_to_credit'];
					$amt_credited 	= $value['amt_credited'];
					$bal_amt 		= $close_amt + ($amt_to_credit - $amt_credited) - ($amt_to_debit - $amt_debited);
					$label 			= $bal_amt < 0 ? TO_RECEIVE : TO_PAY;
					$close_amt 		= $bal_amt;
					$bal_amt 		= abs($bal_amt);

					$record['data'][$key]['amt_to_debit'] 	= round($amt_to_debit, 2);
					$record['data'][$key]['amt_debited'] 	= round($amt_debited, 2);
					$record['data'][$key]['amt_to_credit'] 	= round($amt_to_credit, 2);
					$record['data'][$key]['amt_credited'] 	= round($amt_credited, 2);
					$record['data'][$key]['bal_amt'] 		= round($bal_amt, 2)." ".$label;
				}	
			}
			$close_label 		= $close_amt < 0 ? TO_RECEIVE : TO_PAY;
			$record['close_bal']= abs($close_amt)." ".$close_label;
			// echo "<pre>"; print_r($record); exit;
			return $record;
		}
	}
?>