<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class General_outstandingmdl extends CI_model{
		public function __construct(){
			parent::__construct();

			$this->load->model('master/Accountmdl');
		}
		public function get_data(){
			$subsql 	= '';
			$having 	= '';
			if(isset($_GET['acc_id']) && !empty($_GET['acc_id'])){
				$subsql .=" AND acc.account_id = ".$_GET['acc_id'];
				$record['search']['acc_id'] = $this->Accountmdl->get_search(['account_id' => $_GET['acc_id']]);
			}
			if(isset($_GET['bal_frm'])){
				if($_GET['bal_frm'] != ''){
					$having .=" AND bal_amt >= ".$_GET['bal_frm'];
				}
			}
			if(isset($_GET['bal_to'])){
				if($_GET['bal_to'] != ''){
					$having .=" AND bal_amt <= ".$_GET['bal_to'];
				}
			}
			$query ="
						SELECT CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as account_name, acc.account_open_bal as open_amt,
						acc.account_drcr, acc.account_amt_to_credit as credit_amt, acc.account_amt_credited as credited_amt, 
						acc.account_amt_to_debit as debit_amt, acc.account_amt_debited as debited_amt,
						IF(acc.account_drcr = 'DR', ((acc.account_open_bal + (acc.account_amt_debited)) - (acc.account_amt_credited)), ((acc.account_open_bal + (acc.account_amt_credited)) - (acc.account_amt_debited))) as bal_amt
						FROM account_master acc
						WHERE acc.account_type = 'GENERAL'
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
						$label 			= '';
						// $record['data'][$key]['bal_amt'] = round($value['bal_amt'], 2);	
					}else{
						$bal_amt 		= $bal_amt + $value['bal_amt'];
						$label 			= '';
						// $record['data'][$key]['bal_amt'] = round($value['bal_amt'], 2);	
					}
				}
			}
			$record['totals']['open_amt'] 		= $open_amt;
			$record['totals']['credit_amt'] 	= $credit_amt;
			$record['totals']['credited_amt'] 	= $credited_amt;
			$record['totals']['debit_amt'] 		= $debit_amt;
			$record['totals']['debited_amt'] 	= $debited_amt;
			$record['totals']['label'] 			= '';
			$record['totals']['bal_amt'] 		= $bal_amt;
			// echo "<pre>"; print_r($record); exit;
			return $record;
		}
			
	}
?>