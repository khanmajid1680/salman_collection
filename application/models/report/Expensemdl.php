<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Expensemdl extends CI_model{
		protected $start_date;
		protected $end_date;
		public function __construct(){
			parent::__construct();

			$this->start_date 	= isset($_SESSION['start_year']) ? $_SESSION['start_year']." 00:00:01" : date('Y-m-d H:i:s');
			$this->end_date 	= isset($_SESSION['end_year']) ? $_SESSION['end_year']." 23:59:59" : date('Y-m-d H:i:s');

			$this->load->model('master/Accountmdl');
		}
		public function get_data($flag = true){
			$subsql 	= '';
			$having 	= '';
			$from_date 	= date('Y-m-d') < $_SESSION['start_year'] ? date('Y-m-d') : date('Y-m-d', strtotime($_SESSION['start_year']));
			$to_date 	= date('Y-m-d') < $_SESSION['start_year'] ? date('Y-m-d', strtotime($_SESSION['end_year'])) : date('Y-m-d');
			if(isset($_GET['from_date']) && !empty($_GET['from_date'])){
				$from_date = date('Y-m-d', strtotime($_GET['from_date']));
				$subsql .= " AND vm.vm_entry_date >= '".$from_date."'";
			}else{
				$subsql .= " AND vm.vm_entry_date >= '".$from_date."'";;
			}
			if(isset($_GET['to_date']) && !empty($_GET['to_date'])){
				$to_date = date('Y-m-d', strtotime($_GET['to_date']));
				$subsql .= " AND vm.vm_entry_date <= '".$to_date."'";
			}else{
				$subsql .= " AND vm.vm_entry_date <= '".$to_date."'";
			}
			if(isset($_GET['_party_id']) && !empty($_GET['_party_id'])){
				$subsql .=" AND acc.account_id = ".$_GET['_party_id'];
				$record['search']['_party_id'] = $this->Accountmdl->get_search(['account_id' => $_GET['_party_id']]);
			}
			if(isset($_GET['amt_from'])){
				if($_GET['amt_from'] != ''){
					$having .= " AND total_amt >= ".$_GET['amt_from'];
				}
			}
			if(isset($_GET['amt_to'])){
				if($_GET['amt_to'] != ''){
					$having .= " AND total_amt <= ".$_GET['amt_to'];
				}
			}
			$query ="
						SELECT UPPER(acc.account_name) as account_name, SUM(vm.vm_total_amt + vm.vm_round_off) as total_amt
						FROM voucher_master vm
						INNER JOIN account_master acc ON(acc.account_id = vm.vm_party_id)
						WHERE vm.vm_branch_id = ".$_SESSION['user_branch_id']."
						AND vm.vm_created_at <= '".$this->end_date."'
						AND vm.vm_type = 'PAYMENT'
						AND acc.account_group_id = 22						
						$subsql
						GROUP BY acc.account_id
						HAVING 1
						$having
						ORDER BY total_amt DESC
					";
			// echo "<pre>"; print_r($query); exit;
			$record['data'] = $this->db->query($query)->result_array();
			// echo "<pre>"; print_r($record['data']); exit;
			
			$total_amt 	= 0;
			if(!empty($record['data'])){
				foreach ($record['data'] as $key => $value) {
					$total_amt 	= $total_amt + $value['total_amt'];
	            }
			}
			// echo "<pre>"; print_r($record); exit;
			$record['totals']['total_amt'] 	= $total_amt;
			// echo "<pre>"; print_r($record); exit;
			return $record;
		}
		public function get_select2_party_id(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (account_name LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT account_id as id, UPPER(account_name) as name
                        FROM account_master
                        WHERE account_status = 1
                        AND account_group_id = 22
                        AND account_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        ORDER BY account_name ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
	}
?>