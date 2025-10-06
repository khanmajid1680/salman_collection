<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Barcodemdl extends CI_model{
		protected $table;
		public function __construct(){
			parent::__construct();

			$this->table 	= 'barcode_master';
			$this->load->model('purchase/Purchasemdl');
		}
		public function get_supplier_cnt(){
			$query =" SELECT COUNT(DISTINCT bm_acc_id) as cnt
					  FROM barcode_master
					  WHERE bm_delete_status = 0
					  AND bm_branch_id = ".$_SESSION['user_branch_id']."	
					";
			$data = $this->db->query($query)->result_array();
			if(empty($data)) return 0;
            return $data[0]['cnt'];
        }
        public function get_search($condition){
            $data   = $this->db->get_where($this->table,$condition)->result_array();
            if(empty($data)) return ['value' => '', 'text' => ''];
            $value  = $data[0]['bm_id'];
            $text   = $data[0]['bm_item_code'];
            return ['value' => $value, 'text' => $text];
        }
		public function get_record($condition, $wantDropDown = false){
			$record = [];
			$data 	= $this->db->get_where($this->table,$condition)->result_array();
			if(!$wantDropDown) return $data;
			if(empty($data)){
				$record[0] = 'NO BARCODE ADDED';
			}else{
				$record[0] = 'SELECT';
				foreach ($data as $key => $value) {
					$record[$value['bm_id']] = strtoupper($value['bm_item_code']);
				}
			}
			return $record;
		}
		public function get_data_for_print($bm_id){
			$barcode_query ="
                                SELECT pm.pm_entry_no, pt.pt_serial_no, bm.bm_item_code, bm.bm_sp_amt, bm.bm_cp_code,
                                style.style_name, brand.brand_name, acc.account_code
                                FROM barcode_master bm
                                LEFT JOIN purchase_master pm ON(pm.pm_id = bm.bm_pm_id)
                                LEFT JOIN purchase_trans pt ON(pt.pt_id = bm.bm_pt_id)
                                LEFT JOIN style_master style ON(style.style_id = bm.bm_style_id)
                                LEFT JOIN brand_master brand ON(brand.brand_id = bm.bm_brand_id)
                                LEFT JOIN account_master acc ON(acc.account_id = bm.bm_acc_id)
                                WHERE bm.bm_delete_status = 0 
                                AND bm.bm_id = $bm_id
                             ";
            // echo "<pre>"; print_r($barcode_query); exit();
            $record['barcode_data'] = $this->db->query($barcode_query)->result_array();

            // echo "<pre>"; print_r($record); exit();

            return $record;   
		}
		public function get_state($data){
	    	if(
				$data[0]['bm_pt_qty'] == 0 && 
				$data[0]['bm_prt_qty'] == 0 && 
				$data[0]['bm_st_qty'] == 0 &&
				$data[0]['bm_srt_qty'] == 0 &&
				$data[0]['bm_ot_qty'] == 0 &&
				$data[0]['bm_gt_qty'] == 0
			){
				return['state' => 'INVALID', 'msg' => 'Invalid Barcode.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 0 && 
				$data[0]['bm_st_qty'] == 0 &&
				$data[0]['bm_srt_qty'] == 0 &&
				$data[0]['bm_ot_qty'] == 0 &&
				$data[0]['bm_gt_qty'] == 0
			){
				return['state' => 'PURCHASE', 'msg' => 'Barcode used in purchase.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 1 && 
				$data[0]['bm_st_qty'] == 0 &&
				$data[0]['bm_srt_qty'] == 0 &&
				$data[0]['bm_ot_qty'] == 0 &&
				$data[0]['bm_gt_qty'] == 0
			){
				return['state' => 'PURCHASE RETURN', 'msg' => 'Barcode used in purchase return.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 0 && 
				$data[0]['bm_st_qty'] == 1 &&
				$data[0]['bm_srt_qty'] == 0 &&
				$data[0]['bm_ot_qty'] == 0 &&
				$data[0]['bm_gt_qty'] == 0
			){
				return['state' => 'SALES', 'msg' => 'Barcode is sold.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 0 && 
				$data[0]['bm_st_qty'] == 0 &&
				$data[0]['bm_srt_qty'] == 0 &&
				$data[0]['bm_ot_qty'] == 1 &&
				$data[0]['bm_gt_qty'] == 0
			){
				return['state' => 'OUTWARD', 'msg' => 'Barcode used in outward.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 0 && 
				$data[0]['bm_st_qty'] == 1 &&
				$data[0]['bm_srt_qty'] == 1 &&
				$data[0]['bm_ot_qty'] == 1 &&
				$data[0]['bm_gt_qty'] == 0
			){
				return['state' => 'OUTWARD', 'msg' => 'Barcode used in outward.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 0 && 
				$data[0]['bm_st_qty'] == 1 &&
				$data[0]['bm_srt_qty'] == 1 &&
				$data[0]['bm_ot_qty'] == 0 &&
				$data[0]['bm_gt_qty'] == 0
			){
				return['state' => 'SALES RETURN', 'msg' => 'Barcode used in sales return.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 1 && 
				$data[0]['bm_st_qty'] == 1 &&
				$data[0]['bm_srt_qty'] == 1 &&
				$data[0]['bm_ot_qty'] == 0 &&
				$data[0]['bm_gt_qty'] == 0
			){
				return['state' => 'PURCHASE RETURN', 'msg' => 'Barcode used in purchase return.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 0 && 
				$data[0]['bm_st_qty'] == 0 &&
				$data[0]['bm_srt_qty'] == 0 &&
				$data[0]['bm_ot_qty'] == 1 &&
				$data[0]['bm_gt_qty'] == 1
			){
				return['state' => 'INWARD', 'msg' => 'Barcode used in inward.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 0 && 
				$data[0]['bm_st_qty'] == 1 &&
				$data[0]['bm_srt_qty'] == 0 &&
				$data[0]['bm_ot_qty'] == 1 &&
				$data[0]['bm_gt_qty'] == 1
			){
				return['state' => 'SALES', 'msg' => 'Barcode is sold.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 0 && 
				$data[0]['bm_st_qty'] == 1 &&
				$data[0]['bm_srt_qty'] == 1 &&
				$data[0]['bm_ot_qty'] == 1 &&
				$data[0]['bm_gt_qty'] == 1
			){
				return['state' => 'SALES RETURN', 'msg' => 'Barcode used in sales return.'];	
			}
			return['state' => 'INVALID STATE', 'msg' => 'Barcode is in invalid state.'];	
	    }
		public function get_select2_barcode(){
			$subsql = "";
			if(isset($_GET['name']) && !empty($_GET['name'])){
				$name 	= $_GET['name'];
				$subsql .= " AND (bm.bm_item_code LIKE '%".$name."%')";
			}
			if((isset($_GET['id']) && !empty($_GET['id'])) && (isset($_GET['acc_id']) && !empty($_GET['acc_id']))){
				$id 	 = $_GET['id'];
				$acc_id	 = $_GET['acc_id'];
				$subsql .= " AND (bm.bm_prm_id = ".$id.")";
				$subsql .= " AND (bm.bm_acc_id = ".$acc_id.")";
				$subsql .= $this->subsql0;
			}else{
				$subsql .= $this->subsql1;
			}
			$query ="
						SELECT bm.bm_id as id, bm.bm_item_code as name
						FROM barcode_master bm
						WHERE bm.bm_delete_status = 0 
						AND bm.bm_branch_id = ".$_SESSION['user_branch_id']."
						$subsql
						LIMIT 10
					";
			// echo $query; exit;
			return $this->db->query($query)->result_array();
		}
		public function get_subsql($value=''){
			$subsql = "";
			if(isset($_GET['name']) && !empty($_GET['name'])){
				$name 	= $_GET['name'];
				$subsql .= " AND (bm.bm_item_code LIKE '%".$name."%')";
			}
			if(isset($_GET['param']) && !empty($_GET['param']) && $_GET['param'] == 'PRETURN'){
				$subsql .= " AND ((bm.bm_pt_qty - bm.bm_prt_qty) = 1 AND (bm.bm_st_qty - bm.bm_srt_qty) = 0)";
				$subsql .=" AND bm.bm_branch_id = ".$_SESSION['user_branch_id'];
			}else if(isset($_GET['param']) && !empty($_GET['param']) && $_GET['param'] == 'SALES'){
				$subsql .= " AND ((bm.bm_pt_qty - bm.bm_prt_qty) = 1 AND (bm.bm_st_qty - bm.bm_srt_qty) = 0)";				
				$subsql .=" AND bm.bm_branch_id = ".$_SESSION['user_branch_id'];
			}else if(isset($_GET['param']) && !empty($_GET['param']) && $_GET['param'] == 'SRETURN'){
				$subsql .= " AND ((bm.bm_pt_qty - bm.bm_prt_qty) = 1 AND (bm.bm_st_qty - bm.bm_srt_qty) = 1)";
				$subsql .=" AND bm.bm_branch_id = ".$_SESSION['user_branch_id'];
			}else if(isset($_GET['param']) && !empty($_GET['param']) && $_GET['param'] == 'OUTWARD'){
				$subsql .= " AND ((bm.bm_pt_qty - bm.bm_prt_qty) = 1 AND (bm.bm_st_qty - bm.bm_srt_qty) = 0)";
				$subsql .=" AND bm.bm_branch_id = ".$_SESSION['user_branch_id'];
			}else if(isset($_GET['param']) && !empty($_GET['param']) && $_GET['param'] == 'GRN'){
				if(isset($_GET['param1']) && !empty($_GET['param1'])){
					$subsql .= " AND bm.bm_om_id = ".$_GET['param1'];
					// $subsql .= " AND bm.bm_branch_id = 0";
				}else{
					$subsql .= " AND bm_id = 0";
				}
				
			}
			return $subsql;
		}
		public function get_barcode_select2(){
			$subsql = $this->get_subsql();
			$query ="
						SELECT bm.bm_id as id, bm.bm_item_code as name
						FROM barcode_master bm
						WHERE bm.bm_delete_status = 0 
						$subsql
						LIMIT 10
					";
			// echo $query; exit;
			return $this->db->query($query)->result_array();
		}
		public function get_select2_sales_return(){
			$subsql = "";
			if(isset($_GET['name']) && !empty($_GET['name'])){
				$name 	= $_GET['name'];
				$subsql .= " AND (bm.bm_item_code LIKE '%".$name."%')";
			}
			$query ="
						SELECT bm.bm_id as id, bm.bm_item_code as name
						FROM barcode_master bm
						WHERE bm.bm_delete_status = 0 
						AND bm.bm_branch_id = ".$_SESSION['user_branch_id']."
						$subsql
						LIMIT 10
					";
			echo $query; exit;
			return $this->db->query($query)->result_array();
		}
		public function get_barcode_data($bm_id){  
	        $query ="
	                    SELECT acc.account_id, UPPER(acc.account_name) as account_name, UPPER(acc.account_code) as account_code,
	                    pm.pm_id, pm.pm_bill_no, DATE_FORMAT(pm.pm_bill_date, '%d-%m-%Y') as pm_bill_date,
	                    bm.bm_id, bm.bm_pt_id,
	                    bm.bm_pt_qty, bm.bm_pt_rate,
	                    bm.bm_pt_disc,
	                    pt.pt_taxable_amt as prt_taxable_amt,
	                    pt.pt_sgst_per as prt_sgst_per,
	                    pt.pt_cgst_per as prt_cgst_per,
	                    pt.pt_igst_per as prt_igst_per,
	                    0 as prt_sgst_amt,
	                    0 as prt_cgst_amt,
	                    0 as prt_igst_amt,
	                	IF(design.design_sgst_per > 0, design.design_sgst_per, 2.5) AS design_sgst_per,
	                	IF(design.design_cgst_per > 0, design.design_cgst_per, 2.5) AS design_cgst_per,
	                	IF(design.design_igst_per > 0, design.design_igst_per, 2.5) AS design_igst_per,
	                    bm.bm_item_code, bm.bm_sp_amt,
	                    design.design_id, UPPER(design.design_name) as design_name,
	                    style.style_id, UPPER(style.style_name) as style_name,
	                    brand.brand_id, UPPER(brand.brand_name) as brand_name,
	                    customer.account_id as customer_id, UPPER(customer.account_name) as customer_name, 
	                    user.user_id, UPPER(user.user_fullname) as user_name, 
	                    customer.account_mobile as customer_mobile,
	                    st.st_id, st.st_rate, st.st_sub_total, 
	                    st.st_disc_amt,
	                    st.st_taxable_amt as srt_taxable_amt,
	                    st.st_sgst_per as srt_sgst_per,
	                    st.st_cgst_per as srt_cgst_per,
	                    st.st_igst_per as srt_igst_per,
	                    0 as srt_sgst_amt,
	                    0 as srt_cgst_amt,
	                    0 as srt_igst_amt, 
	                    st.st_sub_total_amt, 
	                    sm.sm_id,
	                    sm.sm_with_gst, 
	                    sm.sm_bill_no, DATE_FORMAT(sm.sm_bill_date, '%d-%m-%Y') as sm_bill_date, sm.sm_payment_mode
	                    FROM barcode_master bm
	                    LEFT JOIN purchase_master pm ON(pm.pm_id = bm.bm_pm_id)
	                    LEFT JOIN purchase_trans pt ON(pt.pt_id = bm.bm_pt_id)
	                    LEFT JOIN sales_trans st ON(st.st_id = bm.bm_st_id)
	                    LEFT JOIN sales_master sm ON(sm.sm_id = st.st_sm_id)
	                    LEFT JOIN design_master design ON(design.design_id = bm.bm_design_id)
	                    LEFT JOIN style_master style ON(style.style_id = bm.bm_style_id)
	                    LEFT JOIN brand_master brand ON(brand.brand_id = bm.bm_brand_id)
	                    LEFT JOIN account_master acc ON(acc.account_id = bm.bm_acc_id)
	                    LEFT JOIN account_master customer ON(customer.account_id = sm.sm_acc_id)
	                    LEFT JOIN user_master user ON(user.user_id = sm.sm_user_id)
	                    WHERE bm.bm_id = $bm_id
	                    AND bm.bm_delete_status = 0 
	                    AND bm.bm_branch_id = ".$_SESSION['user_branch_id']."
	                ";
		        $data = $this->db->query($query)->result_array(); 
		        // echo "<pre>"; print_r($data); exit;
		        if(!empty($data)){
		            foreach ($data as $key => $value) {
		            	if(!empty($value['account_id'])){
		            		$gst_type = $this->model->get_account_state($value['account_id']);
			                $data[$key]['prt_sgst_per']     = $gst_type == 0 ? $value['prt_sgst_per']: 0;
			                $data[$key]['prt_cgst_per']     = $gst_type == 0 ? $value['prt_cgst_per']: 0;
			                $data[$key]['prt_igst_per']     = $gst_type == 1 ? $value['prt_igst_per']: 0;
		            	} 
		                if(!empty($value['customer_id'])){
		                	$gst_type = $this->model->get_account_state($value['customer_id']);
			                $data[$key]['srt_sgst_per']  = ($gst_type == 0 && $value['sm_with_gst']>0) ? $value['srt_sgst_per']: 0;
			                $data[$key]['srt_cgst_per']  = ($gst_type == 0 && $value['sm_with_gst']>0) ? $value['srt_cgst_per']: 0;
			                $data[$key]['srt_igst_per']  = ($gst_type == 1 && $value['sm_with_gst']>0) ? $value['srt_igst_per']: 0;
		                }
		                
		            }
		        }
		        return $data;
	    }

	    public function get_account_state($id){
	        $query="SELECT account_state_id as state_id
	                FROM account_master 
	                WHERE account_id = $id";
	        $supplier_data = $this->db->query($query)->result_array();
	        if(empty($supplier_data)) return 0;
	        return ($supplier_data[0]['state_id'] >1 ) ? 1 : 0;
	    }

	    public function get_select2(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (bm_item_code LIKE '%".$name."%') ";
            }
            if(isset($_GET['param']) && !empty($_GET['param'])){
                $subsql .= " AND (bm_pm_id = 0) ";
            }else{
            	$subsql .= " AND (bm_pm_id != 0) ";
            }
            $query ="
                        SELECT bm_id as id, bm_item_code as name
                        FROM barcode_master
                        WHERE bm_delete_status = 0
                        AND bm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        ORDER BY bm_item_code ASC
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
                        SELECT acc.account_id as id, UPPER(acc.account_name) as name
                        FROM barcode_master bm
                        INNER JOIN account_master acc ON(acc.account_id = bm.bm_acc_id)
                        WHERE bm.bm_delete_status = 0
                        AND acc.account_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        GROUP BY acc.account_id 
                        ORDER BY acc.account_name ASC
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
                        FROM barcode_master bm
                        INNER JOIN style_master style ON(style.style_id = bm.bm_style_id)
                        WHERE bm.bm_delete_status = 0
                        AND bm.bm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        GROUP BY style.style_id 
                        ORDER BY style.style_name ASC
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
                        FROM barcode_master bm
                        INNER JOIN design_master design ON(design.design_id = bm.bm_design_id)
                        WHERE bm.bm_delete_status = 0
                        AND bm.bm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        GROUP BY design.design_id 
                        ORDER BY design.design_name ASC
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
                        FROM barcode_master bm
                        INNER JOIN brand_master brand ON(brand.brand_id = bm.bm_brand_id)
                        WHERE bm.bm_delete_status = 0
                        AND bm.bm_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        GROUP BY brand.brand_id 
                        ORDER BY brand.brand_name ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
       
	}
?>