<?php defined('BASEPATH') OR exit('No direct script access allowed');
    class BranchReceiptmdl extends CI_model{
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
        public function get_data($wantCount, $per_page = PER_PAGE, $offset = OFFSET){
            $record     = [];
            $subsql     = '';
            $limit      = '';
            $ofset      = '';
            
            if(!$wantCount){
                $limit .= " LIMIT $per_page";
                $ofset .= " OFFSET $offset";
            }
            
            if(isset($_GET['id']) && !empty($_GET['id'])){
                $subsql .= " AND vm.vm_id = ". $_GET['id'];
            }
            if(isset($_GET['_entry_no']) && !empty($_GET['_entry_no'])){
                $subsql .=" AND vm.vm_entry_no = ".$_GET['_entry_no'];
                $record['search']['_entry_no']['text'] = $_GET['_entry_no'];
                $record['search']['_entry_no']['value'] = $_GET['_entry_no'];
            }
            if(isset($_GET['_date_from']) && !empty($_GET['_date_from'])){
                $_date_from = date('Y-m-d', strtotime($_GET['_date_from']));
                $subsql .= " AND vm.vm_entry_date >= '".$_date_from."'";
            }
            if(isset($_GET['_date_to']) && !empty($_GET['_date_to'])){
                $_date_to = date('Y-m-d', strtotime($_GET['_date_to']));
                $subsql .= " AND vm.vm_entry_date <= '".$_date_to."'";
            }
            if(isset($_GET['_account_name']) && !empty($_GET['_account_name'])){
                $subsql .=" AND acc.account_name = '".$_GET['_account_name']."'";
                $record['search']['_account_name']['text'] = $_GET['_account_name'];
                $record['search']['_account_name']['value'] = $_GET['_account_name'];
            }
            if(isset($_GET['_party_name']) && !empty($_GET['_party_name'])){
                $subsql .=" AND party.account_name = '".$_GET['_party_name']."'";
                $record['search']['_party_name']['text'] = $_GET['_party_name'];
                $record['search']['_party_name']['value'] = $_GET['_party_name'];
            }
            if(isset($_GET['_amt_from'])){
                if($_GET['_amt_from'] != ''){
                    $subsql .=" AND (vm.vm_total_amt + vm.vm_round_off) >= ".$_GET['_amt_from'];
                }
            }
            if(isset($_GET['_amt_to'])){
                if($_GET['_amt_to'] != ''){
                    $subsql .=" AND (vm.vm_total_amt + vm.vm_round_off) <= ".$_GET['_amt_to'];
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
                        AND vm.vm_group= 'BRANCH'
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
            $accounts = $this->db->query("
                                            SELECT account_id, UPPER(account_name) as account_name 
                                            FROM account_master 
                                            WHERE account_branch_id = ".$_SESSION['user_branch_id']."
                                            AND account_constant IN ('CASH', 'BANK')
                                        ")->result_array();
            if(!empty($accounts)){
                $record['accounts'][0] = 'SELECT';
                foreach ($accounts as $key => $value) {
                    $record['accounts'][$value['account_id']] = $value['account_name'];
                }
            }else{
                $record['accounts'][0] = 'NO ACCOUNT ADDED';
            }
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
                            SELECT vt.*, DATE_FORMAT(vt.vt_bill_date, '%d-%m-%Y') as vt_bill_date
                            FROM voucher_trans vt
                            WHERE vt.vt_vm_id = $id
                          ";
            $record['master_data']  = $this->db->query($master_query)->result_array();
            $record['trans_data']   = $this->db->query($trans_query)->result_array();
            $accounts = $this->db->query("
                                            SELECT account_id, UPPER(account_name) as account_name 
                                            FROM account_master 
                                            WHERE account_branch_id = ".$_SESSION['user_branch_id']."
                                            AND account_constant IN ('CASH', 'BANK')
                                        ")->result_array();
            if(!empty($accounts)){
                $record['accounts'][0] = 'SELECT';
                foreach ($accounts as $key => $value) {
                    $record['accounts'][$value['account_id']] = $value['account_name'];
                }
            }else{
                $record['accounts'][0] = 'NO ACCOUNT ADDED';
            }
            return $record;
        }
        public function get_branch_balance($data, $constant){
            $balance_amt = 0;
            $type        = TO_PAY;
            if(!empty($data)){
                $opening_amt        = 0;
                
                $outward_amt        = $this->get_outward_amt($data[0]['branch_id'], $constant);
                $payment_amt        = $this->get_payment_amt($data[0]['branch_id'], $constant);
                
                $receipt_amt        = $this->get_receipt_amt($data[0]['branch_id'], $constant);
                $receipt_balance    = $this->get_receipt_balance($data[0]['branch_id'], $constant);
                
                $closing_amt        = ($opening_amt  + $receipt_amt + $receipt_balance) - ($payment_amt + $outward_amt);
                $balance_amt        = $closing_amt;
                if($balance_amt < 0){
                    $balance_amt= abs($balance_amt);
                    $type       = TO_RECEIVE;
                }
            }
            return ['closing_bal' => $closing_amt, 'amt' => $balance_amt, 'type' => $type];
        }
        public function get_outward_amt($id){
            $query="
                        SELECT SUM(om_final_amt) as amt
                        FROM outward_master
                        WHERE om_branch = $id
                        AND om_branch_id = ".$_SESSION['user_branch_id']."
                        GROUP BY om_branch
                    ";
            // echo "<pre>"; print_r($query);exit();
            $data = $this->db->query($query)->result_array();
            return !empty($data) ? $data[0]['amt'] : 0;
        }
        public function get_payment_amt($id, $constant){
            $query="
                        SELECT SUM(vm_total_amt) as amt
                        FROM voucher_master
                        WHERE vm_branch = $id
                        AND vm_branch_id = ".$_SESSION['user_branch_id']."
                        AND vm_constant NOT IN ('DEBIT_NOTE', 'CREDIT_NOTE')
                        AND vm_group = 'BRANCH'
                        AND vm_type = 'PAYMENT'
                        AND vm_constant = '$constant'
                        GROUP BY vm_party_id
                    ";
            // echo "<pre>"; print_r($query);exit();
            $data = $this->db->query($query)->result_array();
            return !empty($data) ? $data[0]['amt'] : 0;
        }
        public function get_receipt_amt($id, $constant){
            $query="
                        SELECT SUM(vm_total_amt) as amt
                        FROM voucher_master
                        WHERE vm_branch = $id
                        AND vm_branch_id = ".$_SESSION['user_branch_id']."
                        AND vm_constant NOT IN ('CREDIT_NOTE', 'DEBIT_NOTE')
                        AND vm_group = 'BRANCH'
                        AND vm_type = 'RECEIPT'
                        AND vm_constant = '$constant'
                        GROUP BY vm_party_id
                    ";
            $data = $this->db->query($query)->result_array();
            return !empty($data) ? $data[0]['amt'] : 0;
        }
        public function get_outward_balance($branch_id){
            $query = "
                        SELECT SUM(om.om_final_amt - (om.om_allocated_amt + om.om_allocated_round_off)) as amt
                        FROM outward_master om 
                        WHERE om.om_branch = $branch_id
                        AND om.om_branch_id = ".$_SESSION['user_branch_id']."
                        GROUP BY om.om_branch
                    ";
            $data = $this->db->query($query)->result_array();
            return !empty($data) ? $data[0]['amt'] : 0;
        }
        public function get_receipt_balance($branch_id, $constant){
            $query = "
                        SELECT SUM(om.om_gm_final_amt - (om.om_allocated_amt + om.om_allocated_round_off)) as amt
                        FROM voucher_master vm
                        INNER JOIN voucher_trans vt ON(vt.vt_vm_id = vm.vm_id)
                        INNER JOIN grn_master gm ON(gm.gm_id = vt.vt_gm_id)
                        INNER JOIN outward_master om  ON(om.om_id = gm.gm_om_id)
                        WHERE om.om_branch = $branch_id 
                        AND om_branch_id = ".$_SESSION['user_branch_id']."
                        AND (gm.gm_allocated_amt > 0)
                        AND (om.om_final_amt -  om.om_allocated_amt > 0)
                        AND vm.vm_constant = '$constant'
                        GROUP BY om.om_branch
                    ";
            // echo "<pre>"; print_r($query); exit;
            $data = $this->db->query($query)->result_array();
            return !empty($data) ? $data[0]['amt'] : 0;   
        }
        public function get_data_for_receipt($branch_id, $constant){
            $query = "
                        SELECT om.*, (om.om_final_amt - (om.om_allocated_amt + om.om_allocated_round_off)) as bal_amt,
                            DATE_FORMAT(om.om_entry_date,'%d-%m-%Y') AS om_entry_date
                        FROM voucher_master vm
                        INNER JOIN voucher_trans vt ON(vt.vt_vm_id = vm.vm_id)
                        INNER JOIN grn_master gm ON(gm.gm_id = vt.vt_gm_id)
                        INNER JOIN outward_master om  ON(om.om_id = gm.gm_om_id)
                        WHERE om.om_branch = $branch_id 
                        AND om_branch_id = ".$_SESSION['user_branch_id']."
                        AND (gm.gm_allocated_amt > 0)
                        AND vm.vm_constant = '$constant'
                        HAVING bal_amt > 0
                        ORDER BY om.om_id ASC
                    ";
            return $this->db->query($query)->result_array();
        }
        public function _entry_no(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (vm_entry_no LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT vm_entry_no as id, vm_entry_no as name
                        FROM voucher_master
                        WHERE vm_type = 'RECEIPT'
                        AND vm_group = 'BRANCH'
                        AND vm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        GROUP BY vm_entry_no ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
        public function _account_name(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (acc.account_name LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT acc.account_name as id, UPPER(acc.account_name) as name
                        FROM voucher_master vm
                        INNER JOIN account_master acc ON(acc.account_id = vm.vm_acc_id)
                        WHERE vm.vm_type = 'RECEIPT'
                        AND vm.vm_group = 'BRANCH'
                        AND vm.vm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        GROUP BY acc.account_name ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
        public function _party_name(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (acc.account_name LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT acc.account_name as id, UPPER(acc.account_name) as name
                        FROM voucher_master vm
                        INNER JOIN account_master acc ON(acc.account_id = vm.vm_party_id)
                        WHERE vm.vm_type = 'RECEIPT'
                        AND vm.vm_group = 'BRANCH'
                        AND vm.vm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        GROUP BY acc.account_name ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
        public function _branch_id(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (branch.branch_name LIKE '%".$name."%') ";
            }
            if(isset($_GET['param']) && !empty($_GET['param'])){
                $param   = $_GET['param'];
                $subsql .= " AND (vm.vm_constant = '".$param."')";
            }
            $query ="
                        SELECT branch.branch_id as id, UPPER(branch.branch_name) as name
                        FROM voucher_master vm
                        INNER JOIN voucher_trans vt ON(vt.vt_vm_id = vm.vm_id)
                        INNER JOIN grn_master gm ON(gm.gm_id = vt.vt_gm_id)
                        INNER JOIN outward_master om ON(om.om_id = gm.gm_om_id)
                        INNER JOIN branch_master branch ON(branch.branch_id = om.om_branch)
                        WHERE vm.vm_type = 'PAYMENT'
                        AND vm.vm_group = 'BRANCH'
                        AND vm.vm_branch = ".$_SESSION['user_branch_id']."
                        AND gm.gm_allocated_amt > 0
                        AND (om.om_gm_final_amt - om.om_allocated_amt) > 0
                        $subsql
                        GROUP BY branch.branch_id
                        ORDER BY branch.branch_name ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            $data = $this->db->query($query)->result_array();
            if(!empty($data)) return $data;
            return [0 => ['id' => 0, 'name' => 'No pending branch receipt available.']];
        }
    }
?>