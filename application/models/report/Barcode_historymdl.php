<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Barcode_historymdl extends CI_model{
		public function __construct(){
			parent::__construct();
		}
		public function get_data(){
			$record = [];
			$subsql = "";
			if(isset($_GET['_item_code']) && !empty($_GET['_item_code'])){
				$subsql .=" AND bm.bm_item_code = '".$_GET['_item_code']."'";
				$record['search']['_item_code']['value'] = $_GET['_item_code'];
				$record['search']['_item_code']['text']  = $_GET['_item_code'];
			}
			$query="SELECT bm.bm_id, bm.bm_pt_id as pt_id, bm.bm_item_code, bm.bm_delete_status, bm.bm_pt_qty as pt_qty,
					ROUND(bm.bm_pt_rate) as pt_rate, ROUND(bm.bm_sp_amt) as st_rate,
					((SUM(bm.bm_pt_qty) + SUM(bm.bm_srt_qty)) - (SUM(bm.bm_st_qty) + SUM(bm.bm_prt_qty))) as bal_qty,
					UPPER(bm.bm_desc) as description,
					IFNULL(UPPER(design.design_name), '') as design_name,
					IFNULL(UPPER(style.style_name), '') as style_name,
					IFNULL(UPPER(brand.brand_name), '') as brand_name,
					IFNULL(UPPER(age.age_name), '') as age_name,
					IFNULL(UPPER(account.account_name), '') as account_name,
					IFNULL(UPPER(branch.branch_name), '') as branch_name
					FROM barcode_master bm
					LEFT JOIN branch_master branch ON(branch.branch_id = bm.bm_branch_id)
					LEFT JOIN design_master design ON(design.design_id = bm.bm_design_id)
					LEFT JOIN style_master style ON(style.style_id = bm.bm_style_id)
					LEFT JOIN brand_master brand ON(brand.brand_id = bm.bm_brand_id)
					LEFT JOIN age_master age ON(age.age_id = bm.bm_age_id)
					LEFT JOIN account_master account ON(account.account_id = bm.bm_acc_id)
					WHERE 1
					$subsql
					GROUP BY bm.bm_id
					LIMIT 1";
			// echo "<pre>"; print_r($query); exit();
			$data = (!empty($_GET['_item_code'])) ? $this->db->query($query)->result_array() : [];
			// echo "<pre>"; print_r($data); exit();
			if(!empty($data)){
				foreach ($data as $key => $value) {
					$record['data'][$key] 					= $value;
					$record['data'][$key]['history_data'] 	= $this->get_history_data($value['bm_id']);
				}
			}
			// echo "<pre>"; print_r($record); exit();

			return $record;
		}
		public function get_history_data($bm_id){
			$record = [];
			$subsql = "";
			$query="
						SELECT 'PURCHASE' as module, 
						pm.pm_entry_no as entry_no, 
						DATE_FORMAT(pm.pm_entry_date, '%d-%m-%Y') as entry_date,
						UPPER(user.user_fullname) as user_name,
						UPPER(branch.branch_name) as branch_name,
						pm.pm_created_at as created_at
						FROM barcode_master bm
						INNER JOIN purchase_master pm ON(pm.pm_id = bm.bm_pm_id)
						INNER JOIN user_master user ON(user.user_id = pm.pm_created_by)
						INNER JOIN purchase_trans pt ON(pt.pt_id = bm.bm_pt_id)
						INNER JOIN branch_master branch ON(branch.branch_id = pm.pm_branch_id)
						WHERE bm.bm_id = $bm_id
						$subsql
					";
			// echo "<pre>"; print_r($query); exit();
			$data = $this->db->query($query)->result_array();
			// echo "<pre>"; print_r($data); exit();

			if(!empty($data)){
				foreach ($data as $key => $value) {
					array_push($record, $value);
				}
			}

			$query="
						SELECT 'PURCHASE RETURN' as module, 
						prm.prm_entry_no as entry_no, 
						DATE_FORMAT(prm.prm_entry_date, '%d-%m-%Y') as entry_date,
						UPPER(user.user_fullname) as user_name,
						UPPER(branch.branch_name) as branch_name,
						prm.prm_created_at as created_at
						FROM purchase_return_master prm
						INNER JOIN user_master user ON(user.user_id = prm.prm_created_by)
						INNER JOIN purchase_return_trans prt ON(prt.prt_prm_id = prm.prm_id)
						INNER JOIN branch_master branch ON(branch.branch_id = prm.prm_branch_id)
						WHERE prt.prt_bm_id = $bm_id
						$subsql
					";
			// echo "<pre>"; print_r($query); exit();
			$data = $this->db->query($query)->result_array();
			// echo "<pre>"; print_r($data); exit();

			if(!empty($data)){
				foreach ($data as $key => $value) {
					array_push($record, $value);
				}
			}

			$query="
						SELECT 'SALES' as module, 
						sm.sm_bill_no as entry_no, 
						DATE_FORMAT(sm.sm_bill_date, '%d-%m-%Y') as entry_date,
						UPPER(user.user_fullname) as user_name,
						UPPER(branch.branch_name) as branch_name,
						sm.sm_created_at as created_at
						FROM sales_master sm
						INNER JOIN user_master user ON(user.user_id = sm.sm_created_by)
						INNER JOIN sales_trans st ON(st.st_sm_id = sm.sm_id)
						INNER JOIN branch_master branch ON(branch.branch_id = sm.sm_branch_id)
						WHERE st.st_bm_id = $bm_id
						$subsql
					";
			// echo "<pre>"; print_r($query); exit();
			$data = $this->db->query($query)->result_array();
			// echo "<pre>"; print_r($data); exit();

			if(!empty($data)){
				foreach ($data as $key => $value) {
					array_push($record, $value);
				}
			}

			$query="
						SELECT 'SALES RETURN' as module, 
						srm.srm_entry_no as entry_no, 
						DATE_FORMAT(srm.srm_entry_date, '%d-%m-%Y') as entry_date,
						UPPER(user.user_fullname) as user_name,
						UPPER(branch.branch_name) as branch_name,
						srm.srm_created_at as created_at
						FROM sales_return_master srm
						INNER JOIN user_master user ON(user.user_id = srm.srm_created_by)
						INNER JOIN sales_return_trans srt ON(srt.srt_srm_id = srm.srm_id)
						INNER JOIN branch_master branch ON(branch.branch_id = srm.srm_branch_id)
						WHERE srt.srt_bm_id = $bm_id
						$subsql
					";
			// echo "<pre>"; print_r($query); exit();
			$data = $this->db->query($query)->result_array();
			// echo "<pre>"; print_r($data); exit();

			if(!empty($data)){
				foreach ($data as $key => $value) {
					array_push($record, $value);
				}
			}

			$query="
						SELECT 'OUTWARD' as module, 
						om.om_entry_no as entry_no, 
						DATE_FORMAT(om.om_entry_date, '%d-%m-%Y') as entry_date,
						UPPER(user.user_fullname) as user_name,
						UPPER(branch.branch_name) as branch_name,
						om.om_created_at as created_at
						FROM outward_master om
						INNER JOIN user_master user ON(user.user_id = om.om_created_by)
						INNER JOIN outward_trans ot ON(ot.ot_om_id = om.om_id)
						INNER JOIN branch_master branch ON(branch.branch_id = om.om_branch_id)
						INNER JOIN branch_master obranch ON(obranch.branch_id = om.om_branch)
						WHERE ot.ot_bm_id = $bm_id
						$subsql
					";
			// echo "<pre>"; print_r($query); exit();
			$data = $this->db->query($query)->result_array();
			// echo "<pre>"; print_r($data); exit();

			if(!empty($data)){
				foreach ($data as $key => $value) {
					array_push($record, $value);
				}
			}

			$query="
						SELECT 'INWARD' as module, 
						gm.gm_entry_no as entry_no, 
						DATE_FORMAT(gm.gm_entry_date, '%d-%m-%Y') as entry_date,
						UPPER(user.user_fullname) as user_name,
						UPPER(branch.branch_name) as branch_name,
						gm.gm_created_at as created_at
						FROM grn_master gm
						INNER JOIN user_master user ON(user.user_id = gm.gm_created_by)
						INNER JOIN grn_trans gt ON(gt.gt_gm_id = gm.gm_id)
						INNER JOIN branch_master branch ON(branch.branch_id = gm.gm_branch_id)
						INNER JOIN outward_master om ON(om.om_id = gt.gt_om_id)
						INNER JOIN branch_master ibranch ON(ibranch.branch_id = om.om_branch_id)
						WHERE gt.gt_bm_id = $bm_id
						$subsql
					";
			// echo "<pre>"; print_r($query); exit();
			$data = $this->db->query($query)->result_array();
			// echo "<pre>"; print_r($data); exit();

			if(!empty($data)){
				foreach ($data as $key => $value) {
					array_push($record, $value);
				}
			}
			
			if(!empty($record)){
				usort($record, function($a, $b) {
                    return strtotime($a['created_at']) - strtotime($b['created_at']);
                });
			}

			return $record;
		}
		public function _item_code(){
            $subsql = "";
            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (bm.bm_item_code = '".$name."') ";
            }
            $query ="
                        SELECT bm.bm_item_code as id, bm.bm_item_code as name
                        FROM barcode_master bm
                        WHERE 1
                        $subsql
                        GROUP BY bm.bm_item_code ASC
                        LIMIT 1
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
	}
?>