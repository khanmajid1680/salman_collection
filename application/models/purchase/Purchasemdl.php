<?php defined('BASEPATH') OR exit('No direct script access allowed');

	class Purchasemdl extends CI_model{
		protected $master;
		protected $trans;
		public function __construct(){
			parent::__construct();

            $this->master= 'purchase_master';
			$this->trans = 'purchase_trans';
            $this->load->model('purchase/PurchaseReturnmdl');
            $this->load->model('master/Accountmdl');
			$this->load->model('master/Stylemdl');
			$this->load->model('master/Brandmdl');
			$this->load->model('master/Agemdl');
		}
		public function isExist($id){
            $barcode = $this->db->query("
                    SELECT bm.bm_id
                    FROM barcode_master bm
                    WHERE bm.bm_delete_status = 0
                    AND (bm.bm_prt_qty != 0 OR bm.bm_st_qty != 0 OR bm.bm_srt_qty != 0)
                    AND bm.bm_pm_id = $id
                ")->result_array();

            $voucher = $this->db->query("
                    SELECT vt.vt_id
                    FROM voucher_trans vt
                    WHERE vt.vt_pm_id = $id
                ")->result_array();
            // echo "<pre>"; print_r($barcode);
            // echo "<pre>"; print_r($voucher);exit();
            if(!empty($barcode) || !empty($voucher)) return true;

			return false;
		}
        public function get_entry_no($condition){
            $data   = $this->db->get_where($this->master,$condition)->result_array();
            if(empty($data)) return ['value' => '', 'text' => ''];
            $value  = $data[0]['pm_id'];
            $text   = $data[0]['pm_entry_no'];
            return ['value' => $value, 'text' => $text];
        }
        
        public function get_supplier_state($id){
            $query="SELECT account_state_id as gst_type_id
                    FROM account_master 
                    WHERE account_id = $id";
            return $this->db->query($query)->result_array();
        }

        public function get_bill_no($condition){
            $data   = $this->db->get_where($this->master,$condition)->result_array();
            if(empty($data)) return ['value' => '', 'text' => ''];
            $value  = $data[0]['pm_id'];
            $text   = $data[0]['pm_bill_no'];
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
					$record[$value['pm_id']] = strtoupper($value['pm_entry_no']);
				}
			}
			return $record;
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
			if(isset($_GET['entry_no']) && !empty($_GET['entry_no'])){
                $subsql .=" AND pm.pm_id = ".$_GET['entry_no'];
                $record['search']['entry_no'] = $this->get_entry_no(['pm_id' => $_GET['entry_no']]);
            }
            if(isset($_GET['from_entry_date']) && !empty($_GET['from_entry_date'])){
                $from_entry_date = date('Y-m-d', strtotime($_GET['from_entry_date']));
                $subsql .= " AND pm.pm_entry_date >= '".$from_entry_date."'";
            }
            if(isset($_GET['to_entry_date']) && !empty($_GET['to_entry_date'])){
                $to_entry_date = date('Y-m-d', strtotime($_GET['to_entry_date']));
                $subsql .= " AND pm.pm_entry_date <= '".$to_entry_date."'";
            }
            if(isset($_GET['bill_no']) && !empty($_GET['bill_no'])){
                $subsql .=" AND pm.pm_id = ".$_GET['bill_no'];
                $record['search']['bill_no'] = $this->get_bill_no(['pm_id' => $_GET['bill_no']]);
            }
            if(isset($_GET['from_bill_date']) && !empty($_GET['from_bill_date'])){
                $from_bill_date = date('Y-m-d', strtotime($_GET['from_bill_date']));
                $subsql .= " AND pm.pm_bill_date >= '".$from_bill_date."'";
            }
            if(isset($_GET['to_bill_date']) && !empty($_GET['to_bill_date'])){
                $to_bill_date = date('Y-m-d', strtotime($_GET['to_bill_date']));
                $subsql .= " AND pm.pm_bill_date <= '".$to_bill_date."'";
            }
            if(isset($_GET['acc_id']) && !empty($_GET['acc_id'])){
                $subsql .=" AND pm.pm_acc_id = ".$_GET['acc_id'];
                $record['search']['acc_id'] = $this->Accountmdl->get_search(['account_id' => $_GET['acc_id']]);
            }
            if(isset($_GET['from_qty'])){
                if($_GET['from_qty'] != ''){
                    $subsql .=" AND pm.pm_total_qty >= ".$_GET['from_qty'];
                }
            }
            if(isset($_GET['to_qty'])){
                if($_GET['to_qty'] != ''){
                    $subsql .=" AND pm.pm_total_qty <= ".$_GET['to_qty'];
                }
            }
            if(isset($_GET['from_bill_amt'])){
                if($_GET['from_bill_amt'] != ''){
                    $subsql .=" AND pm.pm_final_amt >= ".$_GET['from_bill_amt'];
                }
            }
            if(isset($_GET['to_bill_amt'])){
                if($_GET['to_bill_amt'] != ''){
                    $subsql .=" AND pm.pm_final_amt <= ".$_GET['to_bill_amt'];
                }
            }
			$query ="
						SELECT pm.*, acc.account_name
						FROM ".$this->master." pm
						LEFT JOIN account_master acc ON(acc.account_id = pm.pm_acc_id)
						WHERE pm.pm_branch_id = ".$_SESSION['user_branch_id']."
                        AND pm.pm_fin_year = '".$_SESSION['fin_year']."'
						$subsql
						ORDER BY pm.pm_id DESC
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
					$record['data'][$key]['isExist'] = $this->isExist($value['pm_id']);
				}
			}
			return $record;
		}
		public function get_data_for_add(){
			$record['pm_entry_no'] 	= $this->db_operations->get_fin_year_branch_max_id('purchase_master', 'pm_entry_no', 'pm_fin_year', $_SESSION['fin_year'], 'pm_branch_id', $_SESSION['user_branch_id']);
			
			// $record['styles'] 		= $this->Stylemdl->get_record(['style_status' => true], true);
			// $record['brands'] 		= $this->Brandmdl->get_record(['brand_status' => true], true);
			// $record['ages'] 		= $this->Agemdl->get_record(['age_status' => true], true);
			return $record;
		}
		public function get_data_for_edit($pm_id){
			$master_query ="
                    SELECT pm.*, acc.account_name, acc.account_code
                    FROM purchase_master pm
                    LEFT JOIN account_master acc ON(acc.account_id = pm.pm_acc_id)
                    WHERE pm.pm_id = $pm_id";
            // echo "<pre>"; print_r($master_query);exit();
            $record['master_data'] = $this->db->query($master_query)->result_array();
            if(!empty($record['master_data'])){
                foreach ($record['master_data'] as $key => $value) {
                    $record['master_data'][$key]['isExist'] = $this->isExist($value['pm_id']);
                }
            }

            $trans_query ="
                            SELECT pt.*,
                            design.design_name, 
                            style.style_name, 
                            brand.brand_name,
                            hsn.hsn_name,
                            SUM(bm.bm_st_qty) bal_qty
                            FROM purchase_trans pt
                            LEFT JOIN design_master design ON(design.design_id = pt.pt_design_id)
                            LEFT JOIN style_master style ON(style.style_id = pt.pt_style_id)
                            LEFT JOIN brand_master brand ON(brand.brand_id = pt.pt_brand_id)
                            LEFT JOIN hsn_master hsn ON(hsn.hsn_id = pt.pt_hsn_id)
                            INNER JOIN barcode_master bm ON(bm.bm_pt_id = pt.pt_id)
                            WHERE pt.pt_pm_id = $pm_id
                            GROUP BY pt.pt_id
                            ORDER BY pt.pt_serial_no DESC
                          ";
            // echo "<pre>"; print_r($trans_query);exit();
            $record['trans_data'] = $this->db->query($trans_query)->result_array();

            if(!empty($record['trans_data'])){
                foreach ($record['trans_data'] as $key => $value) {
                    $record['trans_data'][$key]['isExist'] = $this->isExist($pm_id);
                }
            }
            // echo "<pre>"; print_r($record);exit();

			// $record['designs'] 		= $this->designmdl->get_record(['design_status' => true], true);
			// $record['styles'] 		= $this->Stylemdl->get_record(['style_status' => true], true);
			// $record['brands'] 		= $this->Brandmdl->get_record(['brand_status' => true], true);
			// $record['ages'] 		= $this->Agemdl->get_record(['age_status' => true], true);
			return $record;
		}
		public function get_data_for_print($pm_id){
			$barcode_query ="
                                SELECT pm.pm_entry_no, pt.pt_serial_no, bm.bm_item_code, bm.bm_sp_amt, bm.bm_cp_code, bm.bm_mrp,
                                design.design_name, brand.brand_name, acc.account_code
                                FROM purchase_master pm
                                LEFT JOIN purchase_trans pt ON(pt.pt_pm_id = pm.pm_id)
                                LEFT JOIN barcode_master bm ON(bm.bm_pt_id = pt.pt_id)
                                LEFT JOIN design_master design ON(design.design_id = bm.bm_design_id)
                                LEFT JOIN brand_master brand ON(brand.brand_id = bm.bm_brand_id)
                                LEFT JOIN account_master acc ON(acc.account_id = bm.bm_acc_id)
                                WHERE bm.bm_delete_status = 0 
                                AND bm.bm_pm_id = $pm_id
                             ";
            // echo "<pre>"; print_r($barcode_query); exit();
            $record['barcode_data'] = $this->db->query($barcode_query)->result_array();

            // echo "<pre>"; print_r($record); exit();

            return $record;   
		}
        public function get_trans_data_for_print($pt_id){
            $barcode_query ="
                SELECT pm.pm_entry_no, pt.pt_serial_no, bm.bm_item_code, bm.bm_sp_amt, bm.bm_cp_code, bm.bm_mrp,
                design.design_name, brand.brand_name, acc.account_code
                FROM purchase_master pm
                LEFT JOIN purchase_trans pt ON(pt.pt_pm_id = pm.pm_id)
                LEFT JOIN barcode_master bm ON(bm.bm_pt_id = pt.pt_id)
                LEFT JOIN design_master design ON(design.design_id = bm.bm_design_id)
                LEFT JOIN brand_master brand ON(brand.brand_id = bm.bm_brand_id)
                LEFT JOIN account_master acc ON(acc.account_id = bm.bm_acc_id)
                WHERE bm.bm_delete_status = 0 
                AND bm.bm_pt_id = $pt_id";
            // echo "<pre>"; print_r($barcode_query); exit();
            $record['barcode_data'] = $this->db->query($barcode_query)->result_array();

            // echo "<pre>"; print_r($record); exit();

            return $record;   
        }
        public function get_data_for_bill_print($pm_id){
			$query="SELECT pm.*,
                    DATE_FORMAT(pm.pm_entry_date, '%d-%m-%Y') as entry_date,
                    DATE_FORMAT(pm.pm_bill_date, '%d-%m-%Y') as bill_date,
                    UPPER(supplier.account_code) as supplier_code
                    FROM purchase_master pm
                    INNER JOIN account_master supplier ON(supplier.account_id = pm.pm_acc_id)
                    WHERE pm.pm_id = $pm_id ";
            // echo "<pre>"; print_r($query); exit();
            $record['master_data'] = $this->db->query($query)->result_array();

            $query="SELECT pt.*,
                    IFNULL(UPPER(design.design_name), '') as design_name,
                    IFNULL(UPPER(style.style_name), '') as style_name,
                    IFNULL(UPPER(brand.brand_name), '') as brand_name
                    FROM purchase_trans pt
                    LEFT JOIN design_master design ON(design.design_id = pt.pt_design_id)
                    LEFT JOIN style_master style ON(style.style_id = pt.pt_style_id)
                    LEFT JOIN brand_master brand ON(brand.brand_id = pt.pt_brand_id)
                    WHERE pt.pt_pm_id = $pm_id ";
            // echo "<pre>"; print_r($query); exit();
            $record['trans_data'] = $this->db->query($query)->result_array();

            // echo "<pre>"; print_r($record); exit();

            return $record;   
		}
		public function generate_barcode(){
            
            $year   = date('Y');
            $month  = date('m');
            $barcode_query = "
                                SELECT bm.bm_counter as max_id 
                                FROM barcode_master bm 
                                WHERE bm.bm_barcode_year = '$year' 
                                AND bm.bm_barcode_month = '$month'
                                ORDER BY bm.bm_counter DESC
                                LIMIT 1
                            ";
            // echo "<pre>"; print_r($barcode_query); exit;
            $barcode_data = $this->db->query($barcode_query)->result_array();
            
            if(empty($barcode_data[0]['max_id']))
            {
                return 10000001;
            }
            else
            {
                return $barcode_data[0]['max_id'] + 1;
            }
        }
        public function get_data_for_payment($acc_id){
        	$pur_query = "
                        SELECT pm.*,(pm.pm_final_amt - (pm.pm_allocated_amt + pm.pm_allocated_round_off)) as bal_amt,
                        	DATE_FORMAT(pm.pm_bill_date,'%d-%m-%Y') AS pm_bill_date
                        FROM purchase_master pm 
                        WHERE pm.pm_acc_id = $acc_id 
                        HAVING bal_amt > 0
                    ";

            $pur_data = $this->db->query($pur_query)->result_array();

            $ret_query = "
                        SELECT pm.*, SUM(prt.prt_qty) as return_qty,
                            DATE_FORMAT(pm.pm_bill_date,'%d-%m-%Y') AS pm_bill_date
                        FROM purchase_master pm 
                        INNER JOIN purchase_return_trans prt ON(prt.prt_pm_id = pm.pm_id)
                        WHERE pm.pm_acc_id = $acc_id 
                        AND pm.pm_return_amt = 0
                        GROUP BY pm.pm_id
                    ";
            // echo "<pre>"; print_r($query);exit;

            $ret_data = $this->db->query($ret_query)->result_array();

            return ['pur_data' => $pur_data, 'ret_data' => $ret_data];
        }
        public function get_balance($acc_id){
        	$bill_amt 	= 0;
        	$return_amt = $this->PurchaseReturnmdl->get_sum_final_amt($acc_id);
        	$debit_note = 0;
        	$pur_query = "
                        SELECT (SUM(pm.pm_final_amt) - (SUM(pm.pm_allocated_amt) + SUM(pm.pm_allocated_round_off))) as bal_amt, 
                        SUM(pm.pm_return_amt) as pm_return_amt 
                        FROM purchase_master pm 
                        WHERE pm.pm_acc_id = $acc_id
                        GROUP BY pm.pm_acc_id
                    ";
            $pur_data = $this->db->query($pur_query)->result_array();
            if (!empty($pur_data)){
            	$bill_amt  = $pur_data[0]['bal_amt'];
            	$debit_note= $pur_data[0]['pm_return_amt'];
            }
        	$return_amt  = $return_amt - $debit_note;
        	return ['bill_amt' => $bill_amt, 'return_amt' => $return_amt];
        }
        public function get_credit_balance($id, $date){
            $subsql = '';
            if(!empty($id)){
                $subsql .=" AND pm.pm_acc_id = $id";
            }
            $query ="
                        SELECT IFNULL(SUM(pm.pm_final_amt), 0) as amt
                        FROM purchase_master pm
                        WHERE pm.pm_entry_date < '$date'
                        AND pm.pm_branch_id = '".$_SESSION['user_branch_id']."'
                        $subsql
                    ";
            // echo "<pre>"; print_r($query); exit;
            $data = $this->db->query($query)->result_array();
            // echo "<pre>"; print_r($data); exit;

            if(empty($data)) return 0;
            return $data[0]['amt'];
        }
        public function get_supplier_cnt(){
            $query =" SELECT COUNT(DISTINCT pm.pm_acc_id) as cnt
                      FROM purchase_master pm
                      WHERE pm.pm_fin_year = '".$_SESSION['fin_year']."'
                      AND pm.pm_branch_id = ".$_SESSION['user_branch_id']."    
                    ";
            $data = $this->db->query($query)->result_array();
            if(empty($data)) return 0;
            return $data[0]['cnt'];
        }
        public function get_style_cnt(){
            $query =" SELECT COUNT(DISTINCT pt.pt_style_id) as cnt
                      FROM purchase_master pm
                      LEFT JOIN purchase_trans pt ON(pt.pt_pm_id = pm.pm_id)
                      WHERE pm.pm_fin_year = '".$_SESSION['fin_year']."'
                      AND pm.pm_branch_id = ".$_SESSION['user_branch_id']."    
                    ";
            $data = $this->db->query($query)->result_array();
            if(empty($data)) return 0;
            return $data[0]['cnt'];
        }
        public function get_design_cnt(){
            $query =" SELECT COUNT(DISTINCT pt.pt_design_id) as cnt
                      FROM purchase_master pm
                      LEFT JOIN purchase_trans pt ON(pt.pt_pm_id = pm.pm_id)
                      WHERE pm.pm_fin_year = '".$_SESSION['fin_year']."'
                      AND pm.pm_branch_id = ".$_SESSION['user_branch_id']."    
                    ";
            $data = $this->db->query($query)->result_array();
            if(empty($data)) return 0;
            return $data[0]['cnt'];
        }
        public function get_pur_qty(){
            $query =" SELECT SUM(pm.pm_total_qty) as cnt
                      FROM purchase_master pm
                      WHERE pm.pm_fin_year = '".$_SESSION['fin_year']."'
                      AND pm.pm_branch_id = ".$_SESSION['user_branch_id']."    
                    ";
            $data = $this->db->query($query)->result_array();
            if(empty($data)) return 0;
            return $data[0]['cnt'];
        }
        public function get_purchase_rate($bm_id){
            $data = $this->db_operations->get_record('barcode_master', ['bm_id' => $bm_id]);
            if(empty($data)) return 0;
            return ($data[0]['bm_pt_rate'] - $data[0]['bm_pt_disc']);
        }
        public function get_select2_entry_no(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (pm.pm_entry_no LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT pm_id as id, pm_entry_no as name
                        FROM ".$this->master." pm
                        WHERE pm.pm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        ORDER BY pm_entry_no ASC
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
                        SELECT pm_id as id, pm_bill_no as name
                        FROM ".$this->master." pm
                        WHERE pm.pm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        ORDER BY pm_bill_no ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
        public function get_select2_acc_id(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (acc.account_name LIKE '%".$name."%' OR acc.account_code LIKE '%".$name."%' OR acc.account_mobile LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT acc.account_id as id, CONCAT(UPPER(acc.account_code),' - ', UPPER(acc.account_name), ' - ', acc.account_mobile) as name
                        FROM ".$this->master." pm
                        INNER JOIN account_master acc ON(acc.account_id = pm.pm_acc_id)
                        WHERE pm.pm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        GROUP BY acc.account_id 
                        ORDER BY acc.account_name ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
	}