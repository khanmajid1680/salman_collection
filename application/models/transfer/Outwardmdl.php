<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Outwardmdl extends CI_model{
		protected $master;
		protected $trans;
		public function __construct(){
			parent::__construct();

			$this->master = 'outward_master';
			$this->trans  = 'outward_trans';

			$this->load->model('master/Branchmdl');
		}
		public function isExist($id, $trans = false){
            $inward = $trans ? " AND ot_id = $id" : " AND ot_om_id = $id";
            $data = $this->db->query("SELECT ot_id FROM outward_trans WHERE ot_gt_qty = 1 $inward LIMIT 1")->result_array();
            if(!empty($data)) return true;

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
            $value  = $data[0]['om_id'];
            $text   = $data[0]['om_entry_no'];
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
                $subsql .=" AND om.om_id = ".$_GET['entry_no'];
                $record['search']['entry_no'] = $this->get_entry_no(['om_id' => $_GET['entry_no']]);
            }
            if(isset($_GET['from_entry_date']) && !empty($_GET['from_entry_date'])){
                $from_entry_date = date('Y-m-d', strtotime($_GET['from_entry_date']));
                $subsql .= " AND om.om_entry_date >= '".$from_entry_date."'";
            }
            if(isset($_GET['to_entry_date']) && !empty($_GET['to_entry_date'])){
                $to_entry_date = date('Y-m-d', strtotime($_GET['to_entry_date']));
                $subsql .= " AND om.om_entry_date <= '".$to_entry_date."'";
            }
            if(isset($_GET['branch_id']) && !empty($_GET['branch_id'])){
                $subsql .=" AND om.om_branch = ".$_GET['branch_id'];
                $record['search']['branch_id'] = $this->Branchmdl->get_search(['branch_id' => $_GET['branch_id']]);
            }
            if(isset($_GET['from_qty'])){
                if($_GET['from_qty'] != ''){
                    $subsql .=" AND om.om_total_qty >= ".$_GET['from_qty'];
                }
            }
            if(isset($_GET['to_qty'])){
                if($_GET['to_qty'] != ''){
                    $subsql .=" AND om.om_total_qty <= ".$_GET['to_qty'];
                }
            }
            if(isset($_GET['from_bill_amt'])){
                if($_GET['from_bill_amt'] != ''){
                    $subsql .=" AND om.om_final_amt >= ".$_GET['from_bill_amt'];
                }
            }
            if(isset($_GET['to_bill_amt'])){
                if($_GET['to_bill_amt'] != ''){
                    $subsql .=" AND om.om_final_amt <= ".$_GET['to_bill_amt'];
                }
            }
			$query ="
						SELECT om.*, branch.branch_name
						FROM ".$this->master." om
						LEFT JOIN branch_master branch ON(branch.branch_id = om.om_branch)
						WHERE om.om_branch_id = ".$_SESSION['user_branch_id']."
                        AND om.om_fin_year = '".$_SESSION['fin_year']."'
						$subsql
						ORDER BY om.om_id ASC
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
					$record['data'][$key]['isExist'] 	= $this->isExist($value['om_id']);
					$record['data'][$key]['mis_qty'] 	= $value['om_total_qty'] - $value['om_gm_total_qty'];
					$record['data'][$key]['mis_amt'] 	= $value['om_final_amt'] - $value['om_gm_final_amt'];
				}
			}
			return $record;
		}
		public function get_data_for_add(){
			$record['om_entry_no'] = $this->db_operations->get_fin_year_branch_max_id($this->master, 'om_entry_no', 'om_fin_year', $_SESSION['fin_year'], 'om_branch_id', $_SESSION['user_branch_id']);
			$record['branches'] = $this->Branchmdl->get_record(['branch_id !=' => $_SESSION['user_branch_id'],'branch_status' => true], true);
			return $record;
		}
		public function get_data_for_edit($om_id){
			$master_query = "
                                SELECT om.*, UPPER(branch.branch_name) as branch_name
                                FROM ".$this->master." om
                                LEFT JOIN branch_master branch ON(branch.branch_id = om.om_branch)
                                WHERE om.om_id = $om_id
                            ";
            $record['master_data'] = $this->db->query($master_query)->result_array();

            $trans_query = "
                                SELECT ot.*, bm.bm_item_code, 
                                UPPER(design.design_name) as design_name,
                                UPPER(style.style_name) as style_name,
                                UPPER(brand.brand_name) as brand_name
                                FROM ".$this->trans." ot
                                LEFT JOIN design_master design ON(design.design_id = ot.ot_design_id)
                                LEFT JOIN style_master style ON(style.style_id = ot.ot_style_id)
                                LEFT JOIN brand_master brand ON(brand.brand_id = ot.ot_brand_id)
                                LEFT JOIN barcode_master bm ON(bm.bm_id = ot.ot_bm_id)
                                WHERE ot.ot_om_id = $om_id
                            ";
            $record['trans_data'] = $this->db->query($trans_query)->result_array();
            if(!empty($record['trans_data'])){
                foreach ($record['trans_data'] as $key => $value) {
                    $record['trans_data'][$key]['isExist'] = $this->isExist($value['ot_id'], true);
                }
            }

			$record['branches'] = $this->Branchmdl->get_record(['branch_id !=' => $_SESSION['user_branch_id'],'branch_status' => true], true);
            
            return $record; 
		}
		public function get_latest_outward($om_id, $bm_id){
			$query="
						SELECT ot.*
						FROM outward_trans ot
						WHERE ot.ot_om_id != $om_id
						AND ot.ot_bm_id = $bm_id
						ORDER BY ot.ot_id DESC
						LIMIT 1
					";
			return $this->db->query($query)->result_array();
		}
		public function get_select2_entry_no(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (om.om_entry_no LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT om_id as id, om_entry_no as name
                        FROM ".$this->master." om
                        WHERE om.om_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        ORDER BY om_entry_no ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
        public function get_select2_branch_id(){
            $subsql = "";

            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (branch.branch_name LIKE '%".$name."%') ";
            }
            $query ="
                        SELECT branch.branch_id as id, UPPER(branch.branch_name) as name
                        FROM ".$this->master." om
                        INNER JOIN branch_master branch ON(branch.branch_id = om.om_branch)
                        WHERE om.om_branch_id = ".$_SESSION['user_branch_id']."
                        $subsql
                        GROUP BY branch.branch_id 
                        ORDER BY branch.branch_name ASC
                        LIMIT 10
                    ";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
	}
?>