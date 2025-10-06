<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Invoicemdl extends CI_model{
		protected $master;
		public function __construct(){
			parent::__construct();

            $this->master = 'invoice_master';
			$this->trans  = 'invoice_trans';
			$this->config->load('extra');
            $this->load->model('master/Accountmdl');
            $this->load->model('master/Usermdl');
            $this->load->model('sales/SalesReturnmdl');
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
					$record[$value['im_id']] = strtoupper($value['im_entry_no']);
				}
			}
			return $record;
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
			
			if(isset($_GET['im_entry_no']) && !empty($_GET['im_entry_no'])){
                $subsql .=" AND im.im_entry_no = ".$_GET['im_entry_no'];
                $record['search']['im_entry_no']['text'] = $_GET['im_entry_no'];
                $record['search']['im_entry_no']['value'] = $_GET['im_entry_no'];
            }
            if(isset($_GET['from_date']) && !empty($_GET['from_date'])){
                $from_date = date('Y-m-d', strtotime($_GET['from_date']));
                $subsql .= " AND im.im_entry_date >= '".$from_date."'";
            }
            if(isset($_GET['to_date']) && !empty($_GET['to_date'])){
                $to_date = date('Y-m-d', strtotime($_GET['to_date']));
                $subsql .= " AND im.im_entry_date <= '".$to_date."'";
            }
            if(isset($_GET['from_qty'])){
                if($_GET['from_qty'] != ''){
                    $subsql .=" AND im.im_total_qty >= ".$_GET['from_qty'];
                }
            }
            if(isset($_GET['to_qty'])){
                if($_GET['to_qty'] != ''){
                    $subsql .=" AND im.im_total_qty <= ".$_GET['to_qty'];
                }
            }
            if(isset($_GET['from_bill_amt'])){
                if($_GET['from_bill_amt'] != ''){
                    $subsql .=" AND im.im_final_amt >= ".$_GET['from_bill_amt'];
                }
            }
            if(isset($_GET['to_bill_amt'])){
                if($_GET['to_bill_amt'] != ''){
                    $subsql .=" AND im.im_final_amt <= ".$_GET['to_bill_amt'];
                }
            }
			$query ="
						SELECT im.*
						FROM ".$this->master." im
						WHERE im.im_branch_id = ".$_SESSION['user_branch_id']."
                        AND im.im_fin_year = '".$_SESSION['fin_year']."'
						$subsql
						ORDER BY im.im_id DESC
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
					$record['data'][$key]['isExist'] = $this->isExist($value['im_id']);
				}
			}
			return $record;
		}
		public function get_data_for_add(){
			$record['im_entry_no'] 	= $this->db_operations->get_fin_year_branch_max_id($this->master, 'im_entry_no', 'im_fin_year', $_SESSION['fin_year'], 'im_branch_id', $_SESSION['user_branch_id']);
            // echo "<pre>"; print_r($record);exit;
			return $record;
		}
		public function get_data_for_edit($im_id){
			$master_query ="
                            SELECT im.*
                            FROM invoice_master im
                            WHERE im.im_id = $im_id
                         ";
            $record['master_data'] = $this->db->query($master_query)->result_array();

            $trans_query ="
                            SELECT it.*, CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as account_name,
                            UPPER(user.user_fullname) as user_fullname
                            FROM invoice_trans it
                            INNER JOIN account_master acc ON(acc.account_id = it.it_acc_id)
                            INNER JOIN user_master user ON(user.user_id = it.it_user_id)
                            WHERE it.it_im_id = $im_id
                          ";
            $record['trans_data'] = $this->db->query($trans_query)->result_array();

            return $record;   
		}

        public function get_sales_data($from_date, $to_date){
            $query ="
                        SELECT sm.sm_id, sm.sm_bill_no, DATE_FORMAT(sm.sm_bill_date, '%d-%m-%Y') as sm_bill_date, sm.sm_payment_mode, sm.sm_total_qty,
                        sm.sm_sub_total, sm.sm_total_disc, sm.sm_promo_disc, sm.sm_point_used, sm.sm_round_off, sm.sm_final_amt, sm.sm_acc_id, sm.sm_user_id,
                        IF(acc.account_mobile != '', acc.account_mobile, UPPER(acc.account_name)) as account_name,
                        UPPER(user.user_fullname) as user_fullname
                        FROM sales_master sm
                        INNER JOIN account_master acc ON(acc.account_id = sm.sm_acc_id)
                        INNER JOIN user_master user ON(user.user_id = sm.sm_user_id)
                        WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
                        AND sm.sm_fin_year = '".$_SESSION['fin_year']."'
                        AND sm.sm_bill_date >= '".date('Y-m-d', strtotime($from_date))."'
                        AND sm.sm_bill_date <= '".date('Y-m-d', strtotime($to_date))."'
                        AND sm.sm_invoice_no = 0
                        ORDER BY sm.sm_id, sm.sm_payment_mode ASC
                    ";
            // echo "<pre>"; print_r($query); exit();
            return $this->db->query($query)->result_array();
        }

		public function get_data_for_print($sm_id){
			$sales_query ="
                            SELECT sm.sm_bill_no, sm.sm_bill_date, sm.sm_total_qty, sm.sm_disc_per, sm.sm_promo_per,
                            sm.sm_point_used, sm.sm_total_disc, sm.sm_promo_disc, sm.sm_sub_total, sm.sm_final_amt,
                            sm.sm_collected_amt, sm.sm_to_pay,
                            acc.account_name, acc.account_mobile, 
                            user.user_fullname
                            FROM sales_master sm
                            LEFT JOIN account_master acc ON(acc.account_id = sm.sm_acc_id)
                            LEFT JOIN user_master user ON(user.user_id = sm.sm_user_id)
                            WHERE sm.sm_id = $sm_id
                         ";
            $record['sales_data'] = $this->db->query($sales_query)->result_array();

            $trans_query ="
                            SELECT style.style_name, st.st_qty, st.st_rate, st.st_disc_amt, st.st_sub_total_amt
                            FROM sales_trans st
                            LEFT JOIN style_master style ON(style.style_id = st.st_style_id)
                            WHERE st.st_sm_id = $sm_id
                          ";
            $record['trans_data'] = $this->db->query($trans_query)->result_array();
            
            // echo "<pre>"; print_r($record); exit();

            return $record;   
		}
        public function get_select2_entry_no(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (im.im_entry_no LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT im_entry_no as id, im_entry_no as name
                        FROM ".$this->master." im
                        WHERE im.im_branch_id = ".$_SESSION['user_branch_id']."
                        AND im.im_fin_year = '".$_SESSION['fin_year']."'
                        $subsql
                        ORDER BY im_entry_no ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
        public function get_select2_acc_id(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (acc.account_name LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT acc.account_id as id, CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as name
                        FROM ".$this->master." sm
                        INNER JOIN account_master acc ON(acc.account_id = sm.sm_acc_id)
                        WHERE 1
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
                        WHERE 1
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