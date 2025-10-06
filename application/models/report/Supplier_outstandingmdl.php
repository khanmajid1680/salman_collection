<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Supplier_outstandingmdl extends CI_model{
		public function __construct(){
			parent::__construct();

			$this->load->model('master/Accountmdl');
		}
		public function get_data(){
			$subsql 	= '';
			$having 	= '';
			if(isset($_GET['acc_id']) && !empty($_GET['acc_id'])){
				$subsql .=" AND acc.account_id = ".$_GET['acc_id'];
				$record['search']['acc_id'] = $this->Accountmdl->get_name(['account_id' => $_GET['acc_id']]);
			}
			if(isset($_GET['credit_frm'])){
				if($_GET['credit_frm'] != ''){
					$having .=" AND credit_amt >= ".$_GET['credit_frm'];
				}
			}
			if(isset($_GET['credit_to'])){
				if($_GET['credit_to'] != ''){
					$having .=" AND credit_amt <= ".$_GET['credit_to'];
				}
			}
			if(isset($_GET['credited_frm'])){
				if($_GET['credited_frm'] != ''){
					$having .=" AND credited_amt >= ".$_GET['credited_frm'];
				}
			}
			if(isset($_GET['credited_to'])){
				if($_GET['credited_to'] != ''){
					$having .=" AND credited_amt <= ".$_GET['credited_to'];
				}
			}
			if(isset($_GET['bal_frm'])){
				if($_GET['bal_frm'] != ''){
					$having .=" AND bal_amt >= ".$_GET['bal_frm'];
				}
			}else{
				$having .=" AND bal_amt >= 1";
			}
			if(isset($_GET['bal_to'])){
				if($_GET['bal_to'] != ''){
					$having .=" AND bal_amt <= ".$_GET['bal_to'];
				}
			}
			$query ="
						SELECT CONCAT(UPPER(acc.account_code), ' - ', UPPER(acc.account_name)) as account_name, acc.account_open_bal as open_amt,
						acc.account_drcr, acc.account_amt_to_credit as credit_amt, acc.account_amt_credited as credited_amt, 
						acc.account_amt_to_debit as debit_amt, acc.account_amt_debited as debited_amt,
						IF(acc.account_drcr = 'CR', ((acc.account_open_bal + (acc.account_amt_to_credit - acc.account_amt_credited)) - (acc.account_amt_to_debit - acc.account_amt_debited)), ((acc.account_open_bal + (acc.account_amt_to_debit - acc.account_amt_debited)) - (acc.account_amt_to_credit - acc.account_amt_credited))) as bal_amt
						FROM account_master acc
						WHERE acc.account_type = 'SUPPLIER'
						AND acc.account_branch_id = ".$_SESSION['user_branch_id']."
						$subsql
						GROUP BY acc.account_id DESC
						HAVING 1
						$having
					";
			// echo "<pre>"; print_r($query); exit;
			$record['data'] = $this->db->query($query)->result_array();
			$open_amt  		= 0;
			$credit_amt  	= 0;
			$credited_amt  	= 0;
			$debit_amt  	= 0;
			$debited_amt  	= 0;
			$bal_amt   		= 0;
			
			if(!empty($record['data'])){
				foreach ($record['data'] as $key => $value) {
					$open_amt 		= $open_amt + $value['open_amt'];
					$credit_amt 	= $credit_amt + $value['credit_amt'];
					$credited_amt 	= $credited_amt + $value['credited_amt'];
					$debit_amt 		= $debit_amt + $value['debit_amt'];
					$debited_amt 	= $debited_amt + $value['debited_amt'];
					if($value['account_drcr'] == 'DR'){
						$bal_amt 		= $bal_amt - $value['bal_amt'];
						$label 			= $value['bal_amt'] < 0 ? TO_PAY : TO_RECEIVE;
						$record['data'][$key]['bal_amt'] = abs(round($value['bal_amt'], 2)).' '.$label;	
					}else{
						$bal_amt 		= $bal_amt + $value['bal_amt'];
						$label 			= $value['bal_amt'] < 0 ? TO_RECEIVE : TO_PAY;
						$record['data'][$key]['bal_amt'] = abs(round($value['bal_amt'], 2)).' '.$label;	
					}
				}
			}
			$record['totals']['open_amt'] 		= $open_amt;
			$record['totals']['credit_amt'] 	= $credit_amt;
			$record['totals']['credited_amt'] 	= $credited_amt;
			$record['totals']['debit_amt'] 		= $debit_amt;
			$record['totals']['debited_amt'] 	= $debited_amt;
			$record['totals']['bal_amt'] 		= abs($bal_amt);
			$record['totals']['label'] 			= $bal_amt < 0 ? TO_RECEIVE : TO_PAY;
			// echo "<pre>"; print_r($record); exit;
			return $record;
		}
	}
?>