<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class SalesReturnmdl extends CI_model{
		protected $master;
		protected $trans;
		public function __construct(){
			parent::__construct();

			$this->master = 'sales_return_master';
			$this->trans  = 'sales_return_trans';
			$this->load->model('master/Accountmdl');
		}
		public function get_entry_no($condition){
            $data   = $this->db->get_where($this->master,$condition)->result_array();
            if(empty($data)) return ['value' => '', 'text' => ''];
            $value  = $data[0]['srm_id'];
            $text   = $data[0]['srm_entry_no'];
            return ['value' => $value, 'text' => $text];
        }
		public function get_data($wantCount, $per_page = PER_PAGE, $offset = OFFSET){
			$record 	= [];
			$subsql 	= '';
			$limit  	= '';
			$ofset  	= '';
            $role       = $_SESSION['user_role'];			
			if(!$wantCount){
				$limit .= " LIMIT $per_page";
				$ofset .= " OFFSET $offset";
			}
			
			if(isset($_GET['entry_no']) && !empty($_GET['entry_no'])){
                $subsql .=" AND srm.srm_id = ".$_GET['entry_no'];
                $record['search']['entry_no'] = $this->get_entry_no(['srm_id' => $_GET['entry_no']]);
            }
            if(isset($_GET['from_entry_date']) && !empty($_GET['from_entry_date'])){
                $from_entry_date = date('Y-m-d', strtotime($_GET['from_entry_date']));
                $subsql .= " AND srm.srm_entry_date >= '".$from_entry_date."'";
            }else{
                if($role == SALES){
                    $date    = $role == SALES ? date('Y-m-d') : '';
                    $subsql .= " AND srm.srm_entry_date >= '".$date."'";
                }
            }
            if(isset($_GET['to_entry_date']) && !empty($_GET['to_entry_date'])){
                $to_entry_date = date('Y-m-d', strtotime($_GET['to_entry_date']));
                $subsql .= " AND srm.srm_entry_date <= '".$to_entry_date."'";
            }else{
                if($role == SALES){
                    $date    = $role == SALES ? date('Y-m-d') : '';
                    $subsql .= " AND srm.srm_entry_date <= '".$date."'";
                }
            }
            if(isset($_GET['acc_id']) && !empty($_GET['acc_id'])){
                $subsql .=" AND srm.srm_acc_id = ".$_GET['acc_id'];
                $record['search']['acc_id'] = $this->Accountmdl->get_search(['account_id' => $_GET['acc_id']]);
            }
            if(isset($_GET['from_qty'])){
                if($_GET['from_qty'] != ''){
                    $subsql .=" AND srm.srm_total_qty >= ".$_GET['from_qty'];
                }
            }
            if(isset($_GET['to_qty'])){
                if($_GET['to_qty'] != ''){
                    $subsql .=" AND srm.srm_total_qty <= ".$_GET['to_qty'];
                }
            }
            if(isset($_GET['from_bill_amt'])){
                if($_GET['from_bill_amt'] != ''){
                    $subsql .=" AND srm.srm_final_amt >= ".$_GET['from_bill_amt'];
                }
            }
            if(isset($_GET['to_bill_amt'])){
                if($_GET['to_bill_amt'] != ''){
                    $subsql .=" AND srm.srm_final_amt <= ".$_GET['to_bill_amt'];
                }
            }
			$query ="
						SELECT srm.*, CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as account_name
						FROM ".$this->master." srm
						LEFT JOIN account_master acc ON(acc.account_id = srm.srm_acc_id)
						WHERE srm.srm_branch_id = ".$_SESSION['user_branch_id']."
                        AND srm.srm_fin_year = '".$_SESSION['fin_year']."'
						$subsql
						ORDER BY srm.srm_id DESC
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
					$record['data'][$key]['isExist'] = $this->isExist($value['srm_id']);
				}
			}
			return $record;
		}
		public function get_data_for_add(){
			$record['srm_entry_no'] = $this->db_operations->get_fin_year_branch_max_id($this->master, 'srm_entry_no', 'srm_fin_year', $_SESSION['fin_year'], 'srm_branch_id', $_SESSION['user_branch_id']);
			return $record;
		}
		public function get_data_for_edit($srm_id){
			$master_query = "
                                SELECT srm.*, CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as account_name
                                FROM ".$this->master." srm
                                LEFT JOIN account_master acc ON(acc.account_id = srm.srm_acc_id)
                                WHERE srm.srm_id = $srm_id
                            ";
            $record['master_data'] = $this->db->query($master_query)->result_array();

            $trans_query = "
                                SELECT srt.*, bm.bm_item_code, sm.sm_payment_mode,
                                UPPER(user.user_fullname) as user_name,
                                UPPER(style.style_name) as style_name,
                                UPPER(brand.brand_name) as brand_name
                                FROM ".$this->trans." srt
                                LEFT JOIN style_master style ON(style.style_id = srt.srt_style_id)
                                LEFT JOIN brand_master brand ON(brand.brand_id = srt.srt_brand_id)
                                LEFT JOIN barcode_master bm ON(bm.bm_id = srt.srt_bm_id)
                                LEFT JOIN user_master user ON(user.user_id = srt.srt_user_id)
                                LEFT JOIN sales_master sm ON(sm.sm_id = srt.srt_sm_id)
                                WHERE srt.srt_srm_id = $srm_id
                            ";
            $record['trans_data'] = $this->db->query($trans_query)->result_array();
            return $record; 
		}
		public function get_data_for_print($srm_id){
			$master_query ="
                            SELECT srm.srm_entry_no, srm.srm_entry_date, srm.srm_total_qty, srm.srm_total_disc,
                            srm.srm_sub_total, srm.srm_final_amt, srm.srm_amt_paid,
                            acc.account_name, acc.account_mobile
                            FROM sales_return_master srm
                            LEFT JOIN account_master acc ON(acc.account_id = srm.srm_acc_id)
                            WHERE srm.srm_id = $srm_id
                         ";
            $record['sales_data'] = $this->db->query($master_query)->result_array();

            $trans_query ="
                            SELECT style.style_name, srt.srt_qty, srt.srt_rate, srt.srt_disc_amt, srt.srt_total_amt,
                            user.user_fullname
                            FROM sales_return_trans srt
                            LEFT JOIN style_master style ON(style.style_id = srt.srt_style_id)
                            LEFT JOIN user_master user ON(user.user_id = srt.srt_user_id)
                            WHERE srt.srt_srm_id = $srm_id
                          ";
            $record['trans_data'] = $this->db->query($trans_query)->result_array();
            
            // echo "<pre>"; print_r($record); exit();

            return $record;   
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
					$record[$value['srm_id']] = strtoupper($value['srm_entry_no']);
				}
			}
			return $record;
		}
		public function isExist($id){
			
			return false;
		}
		public function get_sum_final_amt($acc_id){
        	$query = "
			            SELECT SUM(srm.srm_final_amt) as bal_amt
			            FROM sales_return_master srm 
			            WHERE srm.srm_acc_id = $acc_id
			            GROUP BY srm.srm_acc_id";
            $data = $this->db->query($query)->result_array();
            if (empty($data)) return 0;
            return $data[0]['bal_amt'];
		}
        public function get_credited_amt($id, $from_date, $to_date){
            $subsql = '';
            if(!empty($id)){
                $subsql .=" AND srm.srm_acc_id = $id";
            }
            if(!empty($from_date)){
                $subsql .=" AND srm.srm_entry_date >= '".$from_date."'";
            }
            if(!empty($to_date)){
                $subsql .=" AND srm.srm_entry_date <= '".$to_date."'";
            }
            $query ="
                        SELECT IFNULL(SUM(srm.srm_amt_paid), 0) as amt
                        FROM sales_return_master srm
                        WHERE srm.srm_branch_id = '".$_SESSION['user_branch_id']."'
                        $subsql
                    ";
            // echo "<pre>"; print_r($query); exit;
            $data = $this->db->query($query)->result_array();
            // echo "<pre>"; print_r($data); exit;

            if(empty($data)) return 0;
            return $data[0]['amt'];
        }
        public function get_credit_balance($id, $date){
            $subsql = '';
            if(!empty($id)){
                $subsql .=" AND srm.srm_acc_id = $id";
            }
            $query ="
                        SELECT IFNULL(SUM(srm.srm_final_amt), 0) as amt
                        FROM sales_return_master srm
                        WHERE srm.srm_entry_date < '$date'
                        AND srm.srm_branch_id = '".$_SESSION['user_branch_id']."'
                        $subsql
                    ";
            // echo "<pre>"; print_r($query); exit;
            $data = $this->db->query($query)->result_array();
            if(empty($data)) return 0;
            return $data[0]['amt'];
        }
        public function get_credited_balance($id, $date){
            $subsql = '';
            if(!empty($id)){
                $subsql .=" AND srm.srm_acc_id = $id";
            }
            $query ="
                        SELECT IFNULL(SUM(srm.srm_amt_paid), 0) as amt
                        FROM sales_return_master srm
                        WHERE srm.srm_entry_date < '$date'
                        AND srm.srm_branch_id = '".$_SESSION['user_branch_id']."'
                        $subsql
                    ";
            // echo "<pre>"; print_r($query); exit;
            $data = $this->db->query($query)->result_array();
            // echo "<pre>"; print_r($data); exit;

            if(empty($data)) return 0;
            return $data[0]['amt'];
        }
        public function get_credited_bal($mode, $id, $date){
            $subsql = '';
            if(!empty($mode)){
                if($mode == 'BANK'){
                    $subsql .=" AND srm.srm_acc_id = -1";
                }else{
                    if(!empty($id)){
                        $subsql .=" AND srm.srm_acc_id = $id";
                    }
                }
            }
            $query ="
                        SELECT IFNULL(SUM(srm.srm_amt_paid), 0) as amt
                        FROM sales_return_master srm
                        WHERE srm.srm_entry_date < '$date'
                        AND srm.srm_branch_id = ".$_SESSION['user_branch_id']."
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
                $subsql .= " AND (srm.srm_entry_no LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT srm_id as id, srm_entry_no as name
                        FROM ".$this->master." srm
                        WHERE srm.srm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        ORDER BY srm_entry_no ASC
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
                        FROM ".$this->master." srm
                        INNER JOIN account_master acc ON(acc.account_id = srm.srm_acc_id)
                        WHERE srm.srm_branch_id = ".$_SESSION['user_branch_id']."
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
                        FROM sales_return_master srm
                        INNER JOIN sales_return_trans srt ON(srt.srt_srm_id = srm.srm_id)
                        INNER JOIN user_master user ON(user.user_id = srt.srt_user_id)
                        WHERE srm.srm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        GROUP BY user.user_id 
                        ORDER BY user.user_fullname ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
        public function get_select2_bm_id(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (bm.bm_item_code LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT bm.bm_id as id, bm.bm_item_code as name
                        FROM sales_return_trans srt
                        INNER JOIN barcode_master bm ON(bm.bm_id = srt.srt_bm_id)
                        WHERE bm.bm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        GROUP BY bm.bm_id 
                        ORDER BY bm.bm_item_code ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
        public function get_select2_style_id(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (style.style_name LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT style.style_id as id, UPPER(style.style_name) as name
                        FROM sales_return_master srm
                        INNER JOIN sales_return_trans srt ON(srt.srt_srm_id = srm.srm_id)
                        INNER JOIN style_master style ON(style.style_id = srt.srt_style_id)
                        WHERE srm.srm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        GROUP BY style.style_id 
                        ORDER BY style.style_name ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
        public function get_select2_brand_id(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (brand.brand_name LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT brand.brand_id as id, UPPER(brand.brand_name) as name
                        FROM sales_return_master srm
                        INNER JOIN sales_return_trans srt ON(srt.srt_srm_id = srm.srm_id)
                        INNER JOIN brand_master brand ON(brand.brand_id = srt.srt_brand_id)
                        WHERE srm.srm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        GROUP BY brand.brand_id 
                        ORDER BY brand.brand_name ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
        public function get_select2_design_id(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (design.design_name LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT design.design_id as id, UPPER(design.design_name) as name
                        FROM sales_return_trans srt
                        INNER JOIN barcode_master bm ON(bm.bm_id = srt.srt_bm_id)
                        INNER JOIN design_master design ON(design.design_id = bm.bm_design_id)
                        WHERE bm.bm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        GROUP BY design.design_id 
                        ORDER BY design.design_name ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
        public function get_select2_age_id(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (age.age_name LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT age.age_id as id, UPPER(age.age_name) as name
                        FROM sales_return_trans srt
                        INNER JOIN barcode_master bm ON(bm.bm_id = srt.srt_bm_id)
                        INNER JOIN age_master age ON(age.age_id = bm.bm_age_id)
                        WHERE bm.bm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        GROUP BY age.age_id 
                        ORDER BY age.age_name ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
	}
?>