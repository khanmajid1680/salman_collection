<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Salesmdl extends CI_model{
		protected $master;
		public function __construct(){
			parent::__construct();

            $this->master = 'sales_master';
			$this->trans  = 'sales_trans';
			$this->config->load('extra');
            $this->load->model('master/Accountmdl');
            $this->load->model('master/Usermdl');
            $this->load->model('sales/SalesReturnmdl');
		}
		public function isExist($id){
            $barcode = $this->db->query("
                SELECT bm.bm_id
                FROM barcode_master bm
                WHERE bm.bm_delete_status = 0
                AND (bm.bm_pt_qty != 1 OR bm.bm_prt_qty != 0 OR bm.bm_srt_qty != 0)
                AND bm.bm_sm_id = $id
            ")->result_array();

            $voucher = $this->db->query("
                    SELECT vt.vt_id
                    FROM voucher_trans vt
                    WHERE vt.vt_sm_id = $id
                ")->result_array();
            // echo "<pre>"; print_r($barcode);
            // echo "<pre>"; print_r($voucher);exit();
            if(!empty($barcode) || !empty($voucher)) return true;			
			return false;
		}
        public function get_bill_no($condition){
            $data   = $this->db->get_where($this->master,$condition)->result_array();
            if(empty($data)) return ['value' => '', 'text' => ''];
            $value  = $data[0]['sm_id'];
            $text   = $data[0]['sm_bill_no'];
            return ['value' => $value, 'text' => $text];
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
					$record[$value['sm_id']] = strtoupper($value['sm_bill_no']);
				}
			}
			return $record;
		}
		public function get_data($wantCount,$menu, $per_page = PER_PAGE, $offset = OFFSET){
			$record 	= [];
			$subsql 	= '';
			$limit  	= '';
			$ofset  	= '';
			$role       = $_SESSION['user_role'];
			if(!$wantCount){
				$limit .= " LIMIT $per_page";
				$ofset .= " OFFSET $offset";
			}
			
            $sales_type = ($menu=='sales') ? 0 : 1;
            $subsql .=" AND sm.sm_sales_type = ".$sales_type;

			if(isset($_GET['bill_no']) && !empty($_GET['bill_no'])){
                $subsql .=" AND sm.sm_id = ".$_GET['bill_no'];
                $record['search']['bill_no'] = $this->get_bill_no(['sm_id' => $_GET['bill_no']]);
            }
            if(isset($_GET['from_bill_date']) && !empty($_GET['from_bill_date'])){
                $from_bill_date = date('Y-m-d', strtotime($_GET['from_bill_date']));
                $subsql .= " AND sm.sm_bill_date >= '".$from_bill_date."'";
            }else{
                if($role == SALES){
                    $date    = $role == SALES ? date('Y-m-d') : '';
                    $subsql .= " AND sm.sm_bill_date >= '".$date."'";
                }
            }
            if(isset($_GET['to_bill_date']) && !empty($_GET['to_bill_date'])){
                $to_bill_date = date('Y-m-d', strtotime($_GET['to_bill_date']));
                $subsql .= " AND sm.sm_bill_date <= '".$to_bill_date."'";
            }else{
                if($role == SALES){
                    $date    = $role == SALES ? date('Y-m-d') : '';
                    $subsql .= " AND sm.sm_bill_date <= '".$date."'";
                }
            }
            if(isset($_GET['acc_id']) && !empty($_GET['acc_id'])){
                $subsql .=" AND sm.sm_acc_id = ".$_GET['acc_id'];
                $record['search']['acc_id'] = $this->Accountmdl->get_search(['account_id' => $_GET['acc_id']]);
            }
            if(isset($_GET['user_id']) && !empty($_GET['user_id'])){
                $subsql .=" AND sm.sm_user_id = ".$_GET['user_id'];
                $record['search']['user_id'] = $this->Usermdl->get_search(['user_id' => $_GET['user_id']]);
            }
            if(isset($_GET['from_qty'])){
                if($_GET['from_qty'] != ''){
                    $subsql .=" AND sm.sm_total_qty >= ".$_GET['from_qty'];
                }
            }
            if(isset($_GET['to_qty'])){
                if($_GET['to_qty'] != ''){
                    $subsql .=" AND sm.sm_total_qty <= ".$_GET['to_qty'];
                }
            }
            if(isset($_GET['from_bill_amt'])){
                if($_GET['from_bill_amt'] != ''){
                    $subsql .=" AND sm.sm_final_amt >= ".$_GET['from_bill_amt'];
                }
            }
            if(isset($_GET['to_bill_amt'])){
                if($_GET['to_bill_amt'] != ''){
                    $subsql .=" AND sm.sm_final_amt <= ".$_GET['to_bill_amt'];
                }
            }

			$query ="
						SELECT sm.*, 
                        CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as account_name,
                        CONCAT(UPPER(sh_acc.account_name), ' - ', sh_acc.account_mobile) as shipping_account_name,
                        user.user_fullname
						FROM ".$this->master." sm
						LEFT JOIN account_master acc ON(acc.account_id = sm.sm_acc_id)
                        LEFT JOIN account_master sh_acc ON(sh_acc.account_id = sm.sm_shipping_acc_id)
                        LEFT JOIN user_master user ON(user.user_id = sm.sm_user_id)
						WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
                        AND sm.sm_fin_year = '".$_SESSION['fin_year']."'
						$subsql
						ORDER BY sm.sm_id DESC 
						$limit
						$ofset";
			// echo "<pre>"; print_r($query); exit;
			if($wantCount){
				return $this->db->query($query)->num_rows();
			}
			$record['data'] = $this->db->query($query)->result_array();

			if(!empty($record['data'])){
				foreach ($record['data'] as $key => $value) {
					$record['data'][$key]['isExist'] = $this->isExist($value['sm_id']);
				}
			}
			return $record;
		}

        public function get_entry_no($sm_id, $sm_with_gst,$sales_type){    
            if(!empty($sm_id)){
                $data = $this->db_operations->get_record('sales_master', ['sm_id' => $sm_id]);
                if(!empty($data)){
                    if($data[0]['sm_sales_type']==0){
                        if(!empty($data) && !empty($data[0]['sm_bill_no'])) return $data[0]['sm_bill_no'];
                    }
                }
            } 
            $query="SELECT sm_bill_no as max_no
                    FROM sales_master
                    WHERE sm_with_gst = $sm_with_gst
                    AND sm_sales_type = $sales_type
                    AND sm_branch_id = '".$_SESSION['user_branch_id']."'
                    AND sm_fin_year = '".$_SESSION['fin_year']."'
                    ORDER BY sm_bill_no DESC
                    LIMIT 1";
            // print_r($query);die;        
            $data = $this->db->query($query)->result_array();
            // $default = ($sm_with_gst>0)? '5785' : 1;
            // return empty($data) ? $default : ($data[0]['max_no']+1);
            return !empty($data) ? ($data[0]['max_no']+1) : 1;

        }

		public function get_data_for_add(){
			$record['sm_bill_no'] 	= $this->db_operations->get_order_fin_year_branch_max_id($this->master, 'sm_bill_no', 'sm_fin_year', $_SESSION['fin_year'], 'sm_branch_id', $_SESSION['user_branch_id'], 'sm_with_gst', 0, 'sm_sales_type', 0);
            $record['walkin']       = $this->Accountmdl->get_record(['account_constant' => 'WALKIN', 'account_branch_id' => $_SESSION['user_branch_id']]);
			$payment_modes 	        = $this->config->item('payment_mode');
            if(!empty($payment_modes)){
                $record['payment_modes'][''] = 'SELECT';
                foreach ($payment_modes as $key => $value) {
                    $record['payment_modes'][$key] = $value;
                }
            }else{
                $record['payment_modes'][''] = 'NO PAYMENT MODE ADDED';
            }
            // echo "<pre>"; print_r($record);exit;
			return $record;
		}
		public function get_data_for_edit($sm_id){ 
			$master_query ="
                            SELECT sm.*, 
                            acc.account_name, acc.account_mobile,
                            sh_acc.account_name as shipping_account_name, 
                            sh_acc.account_mobile as shipping_account_mobile,
                            UPPER(transport.transport_name) as transport_name,
                            user.user_fullname
                            FROM sales_master sm
                            LEFT JOIN account_master acc ON(acc.account_id = sm.sm_acc_id)
                            LEFT JOIN account_master sh_acc ON(sh_acc.account_id = sm.sm_shipping_acc_id)
                            LEFT JOIN transport_master transport ON(transport.transport_id = sm.sm_transport_id)
                            LEFT JOIN user_master user ON(user.user_id = sm.sm_user_id)
                            WHERE sm.sm_id = $sm_id
                         ";
            $record['master_data'] = $this->db->query($master_query)->result_array();
            if(!empty($record['master_data'])){
                foreach ($record['master_data'] as $key => $value) {
                    $record['master_data'][$key]['isExist'] = $this->isExist($value['sm_id']);
                }
            }

            $trans_query ="
                            SELECT design.design_name,style.style_name, st.*, bm.bm_item_code, brand.brand_name
                            FROM sales_trans st
                            LEFT JOIN barcode_master bm ON(bm.bm_id = st.st_bm_id)
                            LEFT JOIN design_master design ON(design.design_id = bm.bm_design_id)
                            LEFT JOIN style_master style ON(style.style_id = st.st_style_id)
                            LEFT JOIN brand_master brand ON(brand.brand_id = st.st_brand_id)
                            WHERE st.st_sm_id = $sm_id
                          ";
            $record['trans_data'] = $this->db->query($trans_query)->result_array();
            if(!empty($record['trans_data'])){
                foreach ($record['trans_data'] as $key => $value) {
                    $record['trans_data'][$key]['isExist'] = $this->isExist($sm_id);
                }
            }
			$record['payment_modes'] 	= $this->config->item('payment_mode');
            // echo "<pre>"; print_r($record); exit();

            return $record;   
		}
		public function get_data_for_print($sm_id){   
			$sales_query ="
                            SELECT sm.*,
                            UPPER(acc.account_name) as account_name, 
                            acc.account_mobile,
                            acc.account_address,
                            acc.account_gst_no, 
                            UPPER(sh_acc.account_name) as shipping_account_name, 
                            sh_acc.account_mobile as shipping_account_mobile,
                            sh_acc.account_address as shipping_account_address,
                            sh_acc.account_gst_no as shipping_account_gst_no,
                            UPPER(user.user_fullname) as user_fullname,
                            user.user_mobile as user_mobile,
                            UPPER(transport.transport_name) as transport_name,
                            transport.transport_gst_no,
                            transport.transport_address 
                            FROM sales_master sm
                            LEFT JOIN account_master acc ON(acc.account_id = sm.sm_acc_id)
                            LEFT JOIN account_master sh_acc ON(sh_acc.account_id = sm.sm_shipping_acc_id)
                            LEFT JOIN transport_master transport ON(transport.transport_id = sm.sm_transport_id)
                            LEFT JOIN user_master user ON(user.user_id = sm.sm_user_id)
                            WHERE sm.sm_id = $sm_id ";
            $record['sales_data'] = $this->db->query($sales_query)->result_array();
            $trans_query ="
                            SELECT 
                            IF(st_dispatch_date !='',DATE_FORMAT(st.st_dispatch_date,'%d-%m-%Y'),'') as dispatch_date,
                            UPPER(style.style_name) as style_name,
                            UPPER(design.design_name) as design_name, 
                            UPPER(hsn.hsn_name) as hsn_name, 
                            st.*
                            FROM sales_trans st
                            INNER JOIN barcode_master bm ON(bm.bm_id = st.st_bm_id)
                            LEFT JOIN design_master design ON(design.design_id = bm.bm_design_id)
                            LEFT JOIN hsn_master hsn ON(hsn.hsn_id = bm.bm_hsn_id)
                            LEFT JOIN style_master style ON(style.style_id = st.st_style_id)
                            WHERE st.st_sm_id = $sm_id
                          ";
            $record['trans_data'] = $this->db->query($trans_query)->result_array();
            
            // echo "<pre>"; print_r($record); exit();

            return $record;   
		}
		public function get_data_for_payment($acc_id){
        	$sales_query = "
                        SELECT sm.*,(sm.sm_final_amt - (sm.sm_allocated_amt + sm.sm_allocated_round_off)) as bal_amt,
                        	DATE_FORMAT(sm.sm_bill_date,'%d-%m-%Y') AS sm_bill_date
                        FROM sales_master sm 
                        WHERE sm.sm_acc_id = $acc_id 
                        HAVING bal_amt > 0";
            $sales_data = $this->db->query($sales_query)->result_array();

            $ret_query = "
                        SELECT sm.*, SUM(srt.srt_qty) as return_qty,
                            DATE_FORMAT(sm.sm_bill_date,'%d-%m-%Y') AS sm_bill_date
                        FROM sales_master sm 
                        INNER JOIN sales_return_trans srt ON(srt.srt_sm_id = sm.sm_id)
                        WHERE sm.sm_acc_id = $acc_id 
                        AND sm.sm_return_amt = 0
                        GROUP BY sm.sm_id
                    ";
            // echo "<pre>"; print_r($query);exit;

            $ret_data = $this->db->query($ret_query)->result_array();

            return ['sales_data' => $sales_data, 'ret_data' => $ret_data];
        }
  		public function get_balance($acc_id){
        	$bill_amt 	= 0;
        	$return_amt = $this->SalesReturnmdl->get_sum_final_amt($acc_id);
        	$credit_note= 0;
        	$sales_query= "
                        SELECT (SUM(sm.sm_final_amt) - (SUM(sm.sm_allocated_amt) + SUM(sm.sm_allocated_round_off))) as bal_amt, SUM(sm.sm_return_amt) as sm_return_amt 
                        FROM sales_master sm 
                        WHERE sm.sm_acc_id = $acc_id
                        GROUP BY sm.sm_acc_id
                    ";
            // echo $sales_query; exit;
            $sales_data = $this->db->query($sales_query)->result_array();
            if (!empty($sales_data)){
            	$bill_amt  	= $sales_data[0]['bal_amt'];
            	$credit_note= $sales_data[0]['sm_return_amt'];
            }
        	$return_amt  = $return_amt - $credit_note;
            return ['bill_amt' => $bill_amt, 'return_amt' => $return_amt];
        }
        public function get_debited_amt($id, $from_date, $to_date){
            $subsql = '';
            if(!empty($id)){
                $subsql .=" AND sm.sm_acc_id = $id";
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
                        WHERE sm.sm_branch_id = '".$_SESSION['user_branch_id']."'
                        $subsql
                    ";
            // echo "<pre>"; print_r($query); exit;
            $data = $this->db->query($query)->result_array();
            // echo "<pre>"; print_r($data); exit;

            if(empty($data)) return 0;
            return $data[0]['amt'];
        }
        public function get_debited_amount($mode, $from_date, $to_date){
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
                        WHERE sm.sm_branch_id = '".$_SESSION['user_branch_id']."'
                        $subsql
                    ";
            // echo "<pre>"; print_r($query); exit;
            $data = $this->db->query($query)->result_array();
            // echo "<pre>"; print_r($data); exit;

            if(empty($data)) return 0;
            return $data[0]['amt'];
        }
        public function get_debit_balance($id, $date){
            $subsql = '';
            if(!empty($id)){
                $subsql .=" AND sm.sm_acc_id = $id";
            }
            $query ="
                        SELECT IFNULL(SUM(sm.sm_final_amt), 0) as amt
                        FROM sales_master sm
                        WHERE sm.sm_bill_date < '$date'
                        AND sm.sm_branch_id = '".$_SESSION['user_branch_id']."'
                        $subsql
                    ";
            // echo "<pre>"; print_r($query); exit;
            $data = $this->db->query($query)->result_array();
            // echo "<pre>"; print_r($data); exit;

            if(empty($data)) return 0;
            return $data[0]['amt'];
        }
        public function get_debited_balance($id, $date){
            $subsql = '';
            if(!empty($id)){
                $subsql .=" AND sm.sm_acc_id = $id";
            }
            $query ="
                        SELECT IFNULL(SUM(sm.sm_collected_amt - sm.sm_to_pay), 0) as amt
                        FROM sales_master sm
                        WHERE sm.sm_bill_date < '$date'
                        AND sm.sm_branch_id = '".$_SESSION['user_branch_id']."'
                        $subsql
                    ";
            // echo "<pre>"; print_r($query); exit;
            $data = $this->db->query($query)->result_array();
            // echo "<pre>"; print_r($data); exit;

            if(empty($data)) return 0;
            return $data[0]['amt'];
        }
        public function get_debited_bal($mode, $id, $date){
            $subsql = '';
            if(!empty($mode)){
                if($mode == 'BANK'){
                    $subsql .=" AND sm.sm_payment_mode != 'CASH'";
                }else{
                    $subsql .=" AND sm.sm_payment_mode = '".$mode."'";
                }
            }
            if(!empty($id)){
                $subsql .=" AND sm.sm_acc_id = $id";
            }
            $query ="
                        SELECT IFNULL(SUM(sm.sm_collected_amt - sm.sm_to_pay), 0) as amt
                        FROM sales_master sm
                        WHERE sm.sm_bill_date < '$date'
                        AND sm.sm_branch_id = '".$_SESSION['user_branch_id']."'
                        $subsql
                    ";
            // echo "<pre>"; print_r($query); exit;
            $data = $this->db->query($query)->result_array();
            // echo "<pre>"; print_r($data); exit;

            if(empty($data)) return 0;
            return $data[0]['amt'];
        }

        public function get_payment_mode_data($sm_id){  
                $query="SELECT spmt.spmt_id,
                        spmt.spmt_amt as spmt_amt,
                        spmt.spmt_payment_mode_id as spmt_payment_mode_id,
                        UPPER(payment_mode.payment_mode_name) as payment_mode_name
                        FROM sales_payment_mode_trans spmt
                        INNER JOIN payment_mode_master payment_mode ON(payment_mode.payment_mode_id = spmt.spmt_payment_mode_id)
                        WHERE spmt.spmt_delete_status = 0
                        AND spmt.spmt_sm_id = $sm_id
                        ORDER BY payment_mode.payment_mode_name ASC";
                $data = $this->db->query($query)->result_array();
                $ids  = '';
                $subsql='';
                $record=[];
                if(!empty($data)){
                    foreach ($data as $key => $value) {
                        array_push($record, $value);
                        $ids .= empty($ids) ? $value['spmt_payment_mode_id'] : ', '.$value['spmt_payment_mode_id'];
                    }
                    $subsql .=" AND payment_mode.payment_mode_id NOT IN(".$ids.")";
                }

                $query="SELECT 0 as spmt_id,
                        0 as spmt_amt,
                        payment_mode.payment_mode_id as spmt_payment_mode_id,
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
        
        public function get_approval_data($id){ 
            $query="SELECT sm.*,
                    UPPER(acc.account_name) as customer_name
                    FROM sales_master sm
                    INNER JOIN account_master acc ON(acc.account_id = sm.sm_acc_id)
                    WHERE sm.sm_id = $id";
            return $this->db->query($query)->result_array();
        } 

        public function get_select2_bill_no(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (sm.sm_bill_no LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT sm_id as id, 
                        CONCAT(IF(sm.sm_with_gst=1,'INV','EST'),'-',sm.sm_bill_no) as name
                        FROM ".$this->master." sm
                        WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        ORDER BY sm_bill_no ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
        public function get_select2_acc_id(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (acc.account_name LIKE '%".$name."%' OR acc.account_mobile LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT acc.account_id as id, CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as name
                        FROM ".$this->master." sm
                        INNER JOIN account_master acc ON(acc.account_id = sm.sm_acc_id)
                        WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        GROUP BY acc.account_id 
                        ORDER BY acc.account_name ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
        public function get_select2_user_id(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (user.user_fullname LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT user.user_id as id, UPPER(user.user_fullname) as name
                        FROM ".$this->master." sm
                        INNER JOIN user_master user ON(user.user_id = sm.sm_user_id)
                        WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        GROUP BY user.user_id 
                        ORDER BY user.user_fullname ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
	}
?>