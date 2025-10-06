<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class PurchaseReturnmdl extends CI_model{
		protected $master;
		protected $trans;
		public function __construct(){
			parent::__construct();
			$this->master = 'purchase_return_master';
			$this->trans  = 'purchase_return_trans';
			$this->load->model('master/Accountmdl');
		}
        public function isExist($id){
            return false;
        }
		public function get_entry_no($condition){
            $data   = $this->db->get_where($this->master,$condition)->result_array();
            if(empty($data)) return ['value' => '', 'text' => ''];
            $value  = $data[0]['prm_id'];
            $text   = $data[0]['prm_entry_no'];
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
			
			if(isset($_GET['entry_no']) && !empty($_GET['entry_no'])){
				$subsql .=" AND prm.prm_id = ".$_GET['entry_no'];
				$record['search']['entry_no'] = $this->get_entry_no(['prm_id' => $_GET['entry_no']]);
			}
			if(isset($_GET['from_entry_date']) && !empty($_GET['from_entry_date'])){
                $from_entry_date = date('Y-m-d', strtotime($_GET['from_entry_date']));
                $subsql .= " AND prm.prm_entry_date >= '".$from_entry_date."'";
            }
            if(isset($_GET['to_entry_date']) && !empty($_GET['to_entry_date'])){
                $to_entry_date = date('Y-m-d', strtotime($_GET['to_entry_date']));
                $subsql .= " AND prm.prm_entry_date <= '".$to_entry_date."'";
            }
			if(isset($_GET['acc_id']) && !empty($_GET['acc_id'])){
				$subsql .=" AND prm.prm_acc_id = ".$_GET['acc_id'];
				$record['search']['acc_id'] = $this->Accountmdl->get_search(['account_id' => $_GET['acc_id']]);
			}
			if(isset($_GET['from_qty'])){
                if($_GET['from_qty'] != ''){
                    $subsql .=" AND prm.prm_total_qty >= ".$_GET['from_qty'];
                }
            }
            if(isset($_GET['to_qty'])){
                if($_GET['to_qty'] != ''){
                    $subsql .=" AND prm.prm_total_qty <= ".$_GET['to_qty'];
                }
            }
            if(isset($_GET['from_bill_amt'])){
                if($_GET['from_bill_amt'] != ''){
                    $subsql .=" AND prm.prm_final_amt >= ".$_GET['from_bill_amt'];
                }
            }
            if(isset($_GET['to_bill_amt'])){
                if($_GET['to_bill_amt'] != ''){
                    $subsql .=" AND prm.prm_final_amt <= ".$_GET['to_bill_amt'];
                }
            }
			$query ="
						SELECT prm.*, acc.account_name
						FROM ".$this->master." prm
						LEFT JOIN account_master acc ON(acc.account_id = prm.prm_acc_id)
						WHERE prm.prm_branch_id = ".$_SESSION['user_branch_id']."
						AND prm.prm_fin_year = '".$_SESSION['fin_year']."'
						$subsql
						ORDER BY prm.prm_id DESC
						$limit
						$ofset";
			// echo "<pre>"; print_r($query); exit;
			if($wantCount){
				return $this->db->query($query)->num_rows();
			}
			$record['data'] = $this->db->query($query)->result_array();

			if(!empty($record['data'])){
				foreach ($record['data'] as $key => $value) {
					$record['data'][$key]['isExist'] = $this->isExist($value['prm_id']);
				}
			}
			return $record;
		}
		public function get_data_for_add(){
			$record['prm_entry_no'] = $this->db_operations->get_fin_year_branch_max_id($this->master, 'prm_entry_no', 'prm_fin_year', $_SESSION['fin_year'], 'prm_branch_id', $_SESSION['user_branch_id']);
			return $record;
		}
		public function get_data_for_edit($prm_id){
			$master_query = "
                                SELECT prm.*, CONCAT(UPPER(acc.account_name), ' - ', UPPER(acc.account_code)) as account_name
                                FROM ".$this->master." prm
                                LEFT JOIN account_master acc ON(acc.account_id = prm.prm_acc_id)
                                WHERE prm.prm_id = $prm_id";
            $record['master_data'] = $this->db->query($master_query)->result_array();
            if(!empty($record['master_data'])){
                foreach ($record['master_data'] as $key => $value) {
                    $record['master_data'][$key]['isExist'] = $this->isExist($value['prm_id']);
                }
            }

            $trans_query = "
                                SELECT prt.*, bm.bm_item_code, 
                                UPPER(design.design_name) as design_name,
                                UPPER(style.style_name) as style_name,
                                UPPER(brand.brand_name) as brand_name,
                                UPPER(age.age_name) as age_name
                                FROM ".$this->trans." prt
                                LEFT JOIN design_master design ON(design.design_id = prt.prt_design_id)
                                LEFT JOIN style_master style ON(style.style_id = prt.prt_style_id)
                                LEFT JOIN brand_master brand ON(brand.brand_id = prt.prt_brand_id)
                                LEFT JOIN age_master age ON(age.age_id = prt.prt_age_id)
                                LEFT JOIN barcode_master bm ON(bm.bm_id = prt.prt_bm_id)
                                WHERE prt.prt_prm_id = $prm_id";
            $record['trans_data'] = $this->db->query($trans_query)->result_array();
            if(!empty($record['trans_data'])){
                foreach ($record['trans_data'] as $key => $value) {
                    $record['trans_data'][$key]['isExist'] = $this->isExist($prm_id);
                }
            }
            return $record; 
		}
		public function get_data_for_print($prm_id){
			$record = [];
			$master ="
                        SELECT prm.prm_entry_no, DATE_FORMAT(prm.prm_entry_date, '%d-%m-%Y') as prm_entry_date, prm.prm_total_qty, prm.prm_sub_total,
                        prm.prm_round_off, prm.prm_bill_disc, prm.prm_gst_amt, prm.prm_final_amt,
                        UPPER(acc.account_name) as account_name
                        FROM purchase_return_master prm
                        INNER JOIN account_master acc ON(acc.account_id = prm.prm_acc_id)
                        WHERE prm.prm_id = $prm_id
                     ";
		    // echo "<pre>"; print_r($master); exit();
		    $record['master_data'] = $this->db->query($master)->result_array();

		    $trans ="
                        SELECT prt.prt_bill_no, DATE_FORMAT(prt.prt_bill_date, '%d-%m-%Y') as prt_bill_date, SUM(prt.prt_qty) as prt_qty,
                        prt.prt_rate, prt.prt_disc, SUM(prt.prt_qty * (prt.prt_rate - prt.prt_disc)) as prt_sub_total,
                        UPPER(design.design_name) as design_name,
                        UPPER(style.style_name) as style_name,
                        UPPER(brand.brand_name) as brand_name,
                        IFNULL(UPPER(age.age_name), '') as age_name
                        FROM purchase_return_trans prt
                        INNER JOIN design_master design ON(design.design_id = prt.prt_design_id)
                        INNER JOIN style_master style ON(style.style_id = prt.prt_style_id)
                        INNER JOIN brand_master brand ON(brand.brand_id = prt.prt_brand_id)
                        LEFT JOIN age_master age ON(age.age_id = prt.prt_age_id)
                        WHERE prt.prt_prm_id = $prm_id
                        GROUP BY prt.prt_pm_id, prt.prt_design_id, prt.prt_style_id, prt.prt_brand_id, prt.prt_age_id, prt.prt_rate, prt.prt_disc ASC
                     ";
		    // echo "<pre>"; print_r($trans); exit();
		    $record['trans_data'] = $this->db->query($trans)->result_array();

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
					$record[$value['prm_id']] = strtoupper($value['prm_entry_no']);
				}
			}
			return $record;
		}
		
		public function get_sum_final_amt($acc_id){
			$query = "
                        SELECT SUM(prm.prm_final_amt) as bal_amt
                        FROM purchase_return_master prm 
                        WHERE prm.prm_acc_id = $acc_id
                        GROUP BY prm.prm_acc_id
                    ";
            $data = $this->db->query($query)->result_array();
            if (empty($data)) return 0;
            return $data[0]['bal_amt'];
		}
		public function get_debit_balance($id, $date){
            $subsql = '';
            if(!empty($id)){
                $subsql .=" AND prm.prm_acc_id = $id";
            }
            $query ="
                        SELECT IFNULL(SUM(prm.prm_final_amt), 0) as amt
                        FROM purchase_return_master prm
                        WHERE prm.prm_entry_date < '$date'
                        AND prm.prm_branch_id = '".$_SESSION['user_branch_id']."'
                        $subsql
                    ";
            // echo "<pre>"; print_r($query); exit;
            $data = $this->db->query($query)->result_array();
            if(empty($data)) return 0;
            return $data[0]['amt'];
        }
		public function get_select2_entry_no(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (prm.prm_entry_no LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT prm_id as id, prm_entry_no as name
                        FROM ".$this->master." prm
                        WHERE prm.prm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        ORDER BY prm_entry_no ASC
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
                        FROM ".$this->master." prm
                        INNER JOIN account_master acc ON(acc.account_id = prm.prm_acc_id)
                        WHERE prm.prm_branch_id = ".$_SESSION['user_branch_id']."
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