<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Best_personmdl extends CI_model{
		protected $start_date;
		protected $end_date;
		public function __construct(){
			parent::__construct();

			$this->start_date 	= isset($_SESSION['start_year']) ? $_SESSION['start_year']." 00:00:01" : date('Y-m-d H:i:s');
			$this->end_date 	= isset($_SESSION['end_year']) ? $_SESSION['end_year']." 23:59:59" : date('Y-m-d H:i:s');

			$this->load->model('master/Usermdl');
		}
		public function get_data(){
			$record 	= [];
			$subsql 	= "";
			$having 	= "";
			$per_page 	= isset($_GET['per_page']) && !empty($_GET['per_page']) ? $_GET['per_page'] : PER_PAGE;
			$offset 	= isset($_GET['offset']) && !empty($_GET['offset']) ? $_GET['offset'] : OFFSET;
			$limit  	= " LIMIT $per_page";
			$ofset  	= " OFFSET $offset";
			$from_date 	= $_SESSION['start_year'];
			$to_date 	= $_SESSION['end_year'];
			if(isset($_GET['from_date']) && !empty($_GET['from_date'])){
				$from_date = date('Y-m-d', strtotime($_GET['from_date']));
				$subsql .= " AND sm.sm_bill_date >= '".$from_date."'";
			}else{
				$subsql .= " AND sm.sm_bill_date >= '".$from_date."'";
			}
			if(isset($_GET['to_date']) && !empty($_GET['to_date'])){
				$to_date = date('Y-m-d', strtotime($_GET['to_date']));
				$subsql .= " AND sm.sm_bill_date <= '".$to_date."'";
			}else{
				$subsql .= " AND sm.sm_bill_date <= '".$to_date."'";
			}
			if(isset($_GET['user_id']) && !empty($_GET['user_id'])){
				$subsql .=" AND sm.sm_user_id = ".$_GET['user_id'];
				$record['search']['user_id'] = $this->Usermdl->get_search(['user_id' => $_GET['user_id']]);
			}
			if(isset($_GET['sm_qty_frm'])){
				if($_GET['sm_qty_frm'] != ''){
					$having .=" AND sm_qty >= ".$_GET['sm_qty_frm'];
				}
			}
			if(isset($_GET['sm_qty_to'])){
				if($_GET['sm_qty_to'] != ''){
					$having .=" AND sm_qty <= ".$_GET['sm_qty_to'];
				}
			}
			if(isset($_GET['srm_qty_frm'])){
				if($_GET['srm_qty_frm'] != ''){
					$having .=" AND srm_qty >= ".$_GET['srm_qty_frm'];
				}
			}
			if(isset($_GET['srm_qty_to'])){
				if($_GET['srm_qty_to'] != ''){
					$having .=" AND srm_qty <= ".$_GET['srm_qty_to'];
				}
			}
			if(isset($_GET['sale_qty_frm'])){
				if($_GET['sale_qty_frm'] != ''){
					$having .=" AND sale_qty >= ".$_GET['sale_qty_frm'];
				}
			}
			if(isset($_GET['sale_qty_to'])){
				if($_GET['sale_qty_to'] != ''){
					$having .=" AND sale_qty <= ".$_GET['sale_qty_to'];
				}
			}
			if(isset($_GET['sm_amt_frm'])){
				if($_GET['sm_amt_frm'] != ''){
					$having .=" AND sm_amt >= ".$_GET['sm_amt_frm'];
				}
			}
			if(isset($_GET['sm_amt_to'])){
				if($_GET['sm_amt_to'] != ''){
					$having .=" AND sm_amt <= ".$_GET['sm_amt_to'];
				}
			}
			if(isset($_GET['srm_amt_frm'])){
				if($_GET['srm_amt_frm'] != ''){
					$having .=" AND srm_amt >= ".$_GET['srm_amt_frm'];
				}
			}
			if(isset($_GET['srm_amt_to'])){
				if($_GET['srm_amt_to'] != ''){
					$having .=" AND srm_amt <= ".$_GET['srm_amt_to'];
				}
			}
			if(isset($_GET['sale_amt_frm'])){
				if($_GET['sale_amt_frm'] != ''){
					$having .=" AND sale_amt >= ".$_GET['sale_amt_frm'];
				}
			}
			if(isset($_GET['sale_amt_to'])){
				if($_GET['sale_amt_to'] != ''){
					$having .=" AND sale_amt <= ".$_GET['sale_amt_to'];
				}
			}
			$query 	="
						SELECT UPPER(user.user_fullname) as user_fullname, SUM(st.st_qty) as sm_qty, SUM(st.st_return_qty) as srm_qty,
						SUM(st.st_qty - st.st_return_qty) as sale_qty,
						SUM(st.st_sub_total_amt) as sm_amt, SUM(st.st_return_qty * st.st_sub_total_amt) as srm_amt, 
						SUM(st.st_sub_total_amt - (st.st_return_qty * st.st_sub_total_amt)) as sale_amt
						FROM sales_master sm
						INNER JOIN sales_trans st ON(st.st_sm_id = sm.sm_id)
						INNER JOIN user_master user ON(user.user_id = sm.sm_user_id)
						WHERE sm.sm_branch_id = ".$_SESSION['user_branch_id']."
						AND sm.sm_created_at <= '".$this->end_date."' 
						$subsql
						GROUP BY user.user_id
						HAVING 1
						$having
						ORDER BY sale_qty DESC, sale_amt DESC
					 ";
			// echo $query;exit;
			$record['data'] = $this->db->query($query)->result_array();
			// echo "<pre>";print_r($record);exit;

			$sm_qty  		= 0;
			$srm_qty  		= 0;
			$sale_qty  		= 0;
			$sm_amt  		= 0;
			$srm_amt  		= 0;
			$sale_amt   	= 0;
			
			if(!empty($record['data'])){
				foreach ($record['data'] as $key => $value) {
					$sm_qty 		= $sm_qty + $value['sm_qty'];
					$srm_qty 		= $srm_qty + $value['srm_qty'];
					$sale_qty 		= $sale_qty + $value['sale_qty'];
					$sm_amt 		= $sm_amt + $value['sm_amt'];
					$srm_amt 		= $srm_amt + $value['srm_amt'];
					$sale_amt 		= $sale_amt + $value['sale_amt'];
				}
			}
			$record['totals']['sm_qty'] 		= $sm_qty;
			$record['totals']['srm_qty'] 		= $srm_qty;
			$record['totals']['sale_qty'] 		= $sale_qty;
			$record['totals']['sm_amt'] 		= $sm_amt;
			$record['totals']['srm_amt'] 		= $srm_amt;
			$record['totals']['sale_amt'] 		= $sale_amt;

			return $record;
		}
		
	}
?>