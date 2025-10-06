<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class PaymentRemindermdl extends CI_model{
		public function __construct(){
			parent::__construct();
			$this->load->model('master/Accountmdl');
		}
		public function get_data(){ 
			$date 	= date('Y-m-d');
			$subsql = '';
			$having = '';
			if(isset($_GET['as_on_date']) && !empty($_GET['as_on_date'])){
				$date = date('Y-m-d', strtotime($_GET['as_on_date']));
			}
			$select = " DATEDIFF('$date', DATE_ADD(pm.pm_bill_date, INTERVAL acc.account_credit_days DAY)) as diff";
			if(isset($_GET['_acc_id']) && !empty($_GET['_acc_id'])){
				$subsql .=" AND acc.account_id = ".$_GET['_acc_id'];
				$record['search']['_acc_id'] = $this->Accountmdl->get_search(['account_id' => $_GET['_acc_id']]);
			}
			if(isset($_GET['pm_bill_no']) && !empty($_GET['pm_bill_no'])){
				$subsql .=" AND pm.pm_bill_no = '".$_GET['pm_bill_no']."'";
				$record['search']['pm_bill_no']['text'] = $_GET['pm_bill_no'];
				$record['search']['pm_bill_no']['value'] = $_GET['pm_bill_no'];
			}
			if(isset($_GET['from_bill_date']) && !empty($_GET['from_bill_date'])){
				$from_date = date('Y-m-d', strtotime($_GET['from_bill_date']));
				$subsql .= " AND pm.pm_bill_date >= '".$from_date."'";
			}
			if(isset($_GET['to_bill_date']) && !empty($_GET['to_bill_date'])){
				$to_date = date('Y-m-d', strtotime($_GET['to_bill_date']));
				$subsql .= " AND pm.pm_bill_date <= '".$to_date."'";
			}
			if(isset($_GET['from_credit_day'])){
				if($_GET['from_credit_day'] != ''){
					$subsql .=" AND acc.account_credit_days >= ".$_GET['from_credit_day'];
				}
			}
			if(isset($_GET['to_credit_day'])){
				if($_GET['to_credit_day'] != ''){
					$subsql .=" AND acc.account_credit_days <= ".$_GET['to_credit_day'];
				}
			}
			if(isset($_GET['from_rem_day'])){
				if($_GET['from_rem_day'] != ''){
					$having .=" AND diff >= ".$_GET['from_rem_day'];
				}
			}
			if(isset($_GET['to_rem_day'])){
				if($_GET['to_rem_day'] != ''){
					$having .=" AND diff <= ".$_GET['to_rem_day'];
				}
			}
			if(isset($_GET['from_bill_amt'])){
				if($_GET['from_bill_amt'] != ''){
					$having .=" AND bal_amt >= ".$_GET['from_bill_amt'];
				}
			}
			if(isset($_GET['to_bill_amt'])){
				if($_GET['to_bill_amt'] != ''){
					$having .=" AND bal_amt <= ".$_GET['to_bill_amt'];
				}
			}
			$query ="
						SELECT pm.pm_bill_no, DATE_FORMAT(pm.pm_bill_date, '%d-%m-%Y') as pm_bill_date, 
						DATE_FORMAT(DATE_ADD(pm.pm_bill_date, INTERVAL acc.account_credit_days DAY), '%d-%m-%Y') as due_date,
						(pm.pm_final_amt - (pm.pm_allocated_amt + pm.pm_allocated_round_off + pm.pm_return_amt)) as bal_amt,
						acc.account_id, CONCAT(UPPER(acc.account_code), ' - ',UPPER(acc.account_name)) as account_name, acc.account_credit_days, $select
						FROM purchase_master pm
						INNER JOIN account_master acc ON(acc.account_id = pm.pm_acc_id)
						WHERE pm.pm_acc_id != 38
						AND (pm.pm_final_amt - (pm.pm_allocated_amt + pm.pm_allocated_round_off + pm.pm_return_amt)) > 0
						AND pm.pm_branch_id = ".$_SESSION['user_branch_id']."
						$subsql
						HAVING diff >= 0
						$having
						ORDER BY diff ASC
					";
			// echo "<pre>"; print_r($query); exit;
			$record['data'] = $this->db->query($query)->result_array();
			$bal_amt 		= 0;
			if(!empty($record['data'])){
				foreach ($record['data'] as $key => $value) {
					$bal_amt 	= $bal_amt + $value['bal_amt'];
				}
			}
			$record['totals']['bal_amt'] 	= $bal_amt;
			// echo "<pre>"; print_r($record); exit;
			return $record;
		}
		public function get_select2_supplier(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (acc.account_name LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT acc.account_id as id, UPPER(acc.account_name) as name
                        FROM purchase_master pm
                        INNER JOIN account_master acc ON(acc.account_id = pm.pm_acc_id)
						WHERE pm.pm_acc_id != 38
						AND (pm.pm_final_amt - (pm.pm_allocated_amt + pm.pm_allocated_round_off + pm.pm_return_amt)) > 0
						AND pm.pm_branch_id = ".$_SESSION['user_branch_id']."
						$subsql
						GROUP BY acc.account_id
						ORDER BY acc.account_name ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
        public function get_select2_bill_no(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (pm.pm_bill_no LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT pm.pm_bill_no as id, pm.pm_bill_no as name
                        FROM purchase_master pm
						WHERE pm.pm_acc_id != 38
						AND (pm.pm_final_amt - (pm.pm_allocated_amt + pm.pm_allocated_round_off + pm.pm_return_amt)) > 0
						AND pm.pm_branch_id = ".$_SESSION['user_branch_id']."
						$subsql	
						ORDER BY pm.pm_bill_no ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
	}
?>