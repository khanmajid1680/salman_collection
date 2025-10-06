<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Receiptmdl extends CI_model{
		protected $master;
		protected $trans;
		public function __construct(){
			parent::__construct();

			$this->master = 'voucher_master';
			$this->trans  = 'voucher_trans';

			$this->load->model('master/Accountmdl');
			$this->config->load('extra');
		}
		public function isExist($id){
			
			return false;
		}
		public function get_record($condition, $wantDropDown = false){
			$record = [];
			$data 	= $this->db->get_where($this->master,$condition)->result_array();
			if(!$wantDropDown) return $data;
			if(empty($data)){
				$record[0] = 'NO ENTRY ADDED';
			}else{
				$record[0] = 'SELECT';
				foreach ($data as $key => $value) {
					$record[$value['om_id']] = strtoupper($value['om_entry_no']);
				}
			}
			return $record;
		}
		public function get_entry_no($condition){
            $data   = $this->db->get_where($this->master,$condition)->result_array();
            if(empty($data)) return ['value' => '', 'text' => ''];
            $value  = $data[0]['vm_id'];
            $text   = $data[0]['vm_entry_no'];
            return ['value' => $value, 'text' => $text];
        }
        public function get_data($wantCount, $per_page = PER_PAGE, $offset = OFFSET){
			$record 	= [];
			$subsql 	= '';
			$limit  	= '';
			$ofset  	= '';
			
			if(!$wantCount){
				$limit .= " LIMIT $per_page";
				$ofset .= " OFFSET $offset";
			}
			
			if(isset($_GET['id']) && !empty($_GET['id'])){
				$subsql .= " AND vm.vm_id = ". $_GET['id'];
			}
			if(isset($_GET['entry_no']) && !empty($_GET['entry_no'])){
                $subsql .=" AND vm.vm_id = ".$_GET['entry_no'];
                $record['search']['entry_no'] = $this->get_entry_no(['vm_id' => $_GET['entry_no']]);
            }
            if(isset($_GET['from_entry_date']) && !empty($_GET['from_entry_date'])){
                $from_entry_date = date('Y-m-d', strtotime($_GET['from_entry_date']));
                $subsql .= " AND vm.vm_entry_date >= '".$from_entry_date."'";
            }
            if(isset($_GET['to_entry_date']) && !empty($_GET['to_entry_date'])){
                $to_entry_date = date('Y-m-d', strtotime($_GET['to_entry_date']));
                $subsql .= " AND vm.vm_entry_date <= '".$to_entry_date."'";
            }
            if(isset($_GET['account_id']) && !empty($_GET['account_id'])){
                $subsql .=" AND vm.vm_acc_id = ".$_GET['account_id'];
                $record['search']['account_id'] = $this->Accountmdl->get_search(['account_id' => $_GET['account_id']]);
            }
            if(isset($_GET['party_id']) && !empty($_GET['party_id'])){
                $subsql .=" AND vm.vm_party_id = ".$_GET['party_id'];
                $record['search']['party_id'] = $this->Accountmdl->get_search(['account_id' => $_GET['party_id']]);
            }
            if(isset($_GET['from_amt'])){
                if($_GET['from_amt'] != ''){
                    $subsql .=" AND (vm.vm_total_amt + vm.vm_round_off) >= ".$_GET['from_amt'];
                }
            }
            if(isset($_GET['to_amt'])){
                if($_GET['to_amt'] != ''){
                    $subsql .=" AND (vm.vm_total_amt + vm.vm_round_off) <= ".$_GET['to_amt'];
                }
            }
			$query ="
						SELECT vm.*, acc.account_name, CONCAT(party.account_name,' - ',party.account_mobile) as party_name
						FROM ".$this->master." vm
						LEFT JOIN account_master acc ON(acc.account_id = vm.vm_acc_id)
						LEFT JOIN account_master party ON(party.account_id = vm.vm_party_id)
						WHERE vm.vm_branch_id = ".$_SESSION['user_branch_id']."
                        AND vm.vm_fin_year = '".$_SESSION['fin_year']."'
						AND vm.vm_type = 'RECEIPT'
						$subsql
						ORDER BY vm.vm_id DESC
						$limit
						$ofset
					";
			// echo "<pre>"; print_r($query); exit;
			if($wantCount){
				return $this->db->query($query)->num_rows();
			}
			$record['data'] = $this->db->query($query)->result_array();

			if(!empty($record['data'])){
				foreach ($record['data'] as $key => $value) {
					$record['data'][$key]['isExist'] = $this->isExist($value['vm_id']);
				}
			}
			return $record;
		}
		public function get_data_for_add(){
			$record['vm_entry_no'] = $this->db_operations->get_fin_year_branch_max_id($this->master, 'vm_entry_no', 'vm_fin_year', $_SESSION['fin_year'], 'vm_branch_id', $_SESSION['user_branch_id']);
			$record['accounts'] = $this->Accountmdl->get_record(['account_constant !=' => 'DEBIT_NOTE', 'account_group_id' => 9, 'account_status' => true, 'account_branch_id' => $_SESSION['user_branch_id']], true);
			$record['groups'] 	= $this->config->item('group');
			return $record;
		}
		public function get_data_for_edit($id){
			$master_query="
							SELECT vm.*, CONCAT(party.account_name,' - ',party.account_mobile) as party_name, acc.account_constant
							FROM voucher_master vm
                            LEFT JOIN account_master acc ON(acc.account_id = vm.vm_acc_id)
							LEFT JOIN account_master party ON(party.account_id = vm.vm_party_id)
							WHERE vm.vm_id = $id
						  ";
			$trans_query ="
							SELECT vt.*, sm.*
							FROM voucher_trans vt
							LEFT JOIN sales_master sm ON(sm.sm_id = vt.vt_sm_id)
							WHERE vt.vt_vm_id = $id
						  ";
			$record['master_data'] 	= $this->db->query($master_query)->result_array();
			$record['trans_data'] 	= $this->db->query($trans_query)->result_array();
			$record['accounts'] 	= $this->Accountmdl->get_record(['account_constant !=' => 'DEBIT_NOTE', 'account_group_id' => 9, 'account_status' => true, 'account_branch_id' => $_SESSION['user_branch_id']], true);
			$record['groups'] 		= $this->config->item('group');
			return $record;
		}

        public function get_payment_mode_data($receipt_id){
            $query="SELECT rpmt.rpmt_id,
                    rpmt.rpmt_amt as rpmt_amt,
                    rpmt.rpmt_payment_mode_id as rpmt_payment_mode_id,
                    UPPER(payment_mode.payment_mode_name) as payment_mode_name
                    FROM receipt_payment_mode_trans rpmt
                    INNER JOIN payment_mode_master payment_mode ON(payment_mode.payment_mode_id = rpmt.rpmt_payment_mode_id)
                    WHERE rpmt.rpmt_delete_status = 0
                    AND rpmt.rpmt_receipt_id = $receipt_id
                    ORDER BY payment_mode.payment_mode_name ASC";
            $data = $this->db->query($query)->result_array();
            $ids  = '';
            $subsql='';
            $record=[];
            if(!empty($data)){
                foreach ($data as $key => $value) {
                    array_push($record, $value);
                    $ids .= empty($ids) ? $value['rpmt_payment_mode_id'] : ', '.$value['rpmt_payment_mode_id'];
                }
                $subsql .=" AND payment_mode.payment_mode_id NOT IN(".$ids.")";
            }

            $query="SELECT 0 as rpmt_id,
                    0 as rpmt_amt,
                    payment_mode.payment_mode_id as rpmt_payment_mode_id,
                    UPPER(payment_mode.payment_mode_name) as payment_mode_name
                    FROM payment_mode_master payment_mode
                    WHERE payment_mode.payment_mode_status = 1
                    $subsql
                    ORDER BY payment_mode.payment_mode_name ASC";
            $data = $this->db->query($query)->result_array();
            if(!empty($data)){
                foreach ($data as $key => $value) {
                    array_push($record, $value);
                }
            }
           
           usort($record, function($a, $b) {
                if ($a['payment_mode_name'] == $b['payment_mode_name']) {
                    return 0; // equal
                }
                return ($a['payment_mode_name'] > $b['payment_mode_name']) ? 1 : -1; // return 1 or -1 based on comparison
            });

            return $record;
        }
            
		public function get_debited_balance($group, $id, $date){
            $subsql = '';
            if(!empty($id)){
                $subsql .=" AND vm.vm_party_id = $id";
            }
            $query ="
                        SELECT IFNULL(SUM(vm.vm_total_amt + vm.vm_round_off), 0) as amt
                        FROM voucher_master vm
                        WHERE vm.vm_entry_date < '$date'
                        AND vm.vm_branch_id = '".$_SESSION['user_branch_id']."'
                        AND vm.vm_type = 'RECEIPT'
                        AND vm.vm_group = '$group'
                        AND vm.vm_acc_id != 3 AND vm.vm_acc_id != 4
                        $subsql
                    ";
            // echo "<pre>"; print_r($query); exit;
            $data = $this->db->query($query)->result_array();
            // echo "<pre>"; print_r($data); exit;
            if(empty($data)) return 0;
            return $data[0]['amt'];
        }
        public function get_debited_bal($group, $acc_id, $party_id, $date){
            $subsql = '';
            if(!empty($acc_id)){
                $subsql .=" AND vm.vm_acc_id = $acc_id";
            }
            if(!empty($party_id)){
                $subsql .=" AND vm.vm_party_id = $party_id";
            }
            $query ="
                        SELECT IFNULL(SUM(vm.vm_total_amt + vm.vm_round_off), 0) as amt
                        FROM voucher_master vm
                        WHERE vm.vm_entry_date < '$date'
                        AND vm.vm_branch_id = '".$_SESSION['user_branch_id']."'
                        AND vm.vm_type = 'RECEIPT'
                        AND vm.vm_group = '$group'
                        $subsql
                    ";
            // echo "<pre>"; print_r($query); exit;
            $data = $this->db->query($query)->result_array();
            // echo "<pre>"; print_r($data); exit;
            if(empty($data)) return 0;
            return $data[0]['amt'];
        }
        public function get_credited_balance($group, $id, $date){
			$subsql = '';
            if(!empty($id)){
                $subsql .=" AND vm.vm_party_id = $id";
            }
            $query ="
                        SELECT IFNULL(SUM(vm.vm_total_amt + vm.vm_round_off), 0) as amt
                        FROM voucher_master vm
                        WHERE vm.vm_entry_date < '$date'
                        AND vm.vm_branch_id = '".$_SESSION['user_branch_id']."'
                        AND vm.vm_type = 'RECEIPT'
                        AND vm.vm_group = '$group'
                        AND vm.vm_acc_id = 4
                        $subsql
                    ";
            // echo "<pre>"; print_r($query); exit;
            $data = $this->db->query($query)->result_array();
            // echo "<pre>"; print_r($data); exit;

            if(empty($data)) return 0;
        	return $data[0]['amt'];
        }
        public function get_credited_bal($group, $acc_id, $party_id, $date){
            $subsql = '';
            if(!empty($acc_id)){
                $subsql .=" AND vm.vm_acc_id = $acc_id";
            }
            if(!empty($party_id)){
                $subsql .=" AND vm.vm_party_id = $party_id";
            }
            $query ="
                        SELECT IFNULL(SUM(vm.vm_total_amt + vm.vm_round_off), 0) as amt
                        FROM voucher_master vm
                        WHERE vm.vm_entry_date < '$date'
                        AND vm.vm_branch_id = '".$_SESSION['user_branch_id']."'
                        AND vm.vm_type = 'RECEIPT'
                        AND vm.vm_group = '$group'
                        $subsql
                    ";
            // echo "<pre>"; print_r($query); exit;
            $data = $this->db->query($query)->result_array();
            // echo "<pre>"; print_r($data); exit;

            if(empty($data)) return 0;
            return $data[0]['amt'];
        }
		public function get_select2_entry_no(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (vm_entry_no LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT vm_id as id, vm_entry_no as name
                        FROM voucher_master
                        WHERE vm_type = 'RECEIPT'
                        AND vm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        ORDER BY vm_entry_no ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
        public function get_select2_account_id(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (acc.account_name LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT acc.account_id as id, UPPER(acc.account_name) as name
                        FROM voucher_master vm
                        INNER JOIN account_master acc ON(acc.account_id = vm.vm_acc_id)
                        WHERE vm.vm_type = 'RECEIPT'
                        AND vm.vm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        GROUP BY acc.account_id
                        ORDER BY acc.account_name ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
        public function get_select2_party_id(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (acc.account_name LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT acc.account_id as id, UPPER(acc.account_name) as name
                        FROM voucher_master vm
                        INNER JOIN account_master acc ON(acc.account_id = vm.vm_party_id)
                        WHERE vm.vm_type = 'RECEIPT'
                        AND vm.vm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        GROUP BY acc.account_id
                        ORDER BY acc.account_name ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
	}
?>