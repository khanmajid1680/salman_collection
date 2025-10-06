<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Purchase extends CI_Controller{
	protected $master;
	protected $trans;
	protected $session_expired;
	public function __construct(){
		parent::__construct();

		$this->master 			= 'purchase_master';
		$this->trans 			= 'purchase_trans';
		$this->session_expired  = ['status' => false, 'flag' => -1, 'data' => [], 'msg' => ''];

		$this->load->model('purchase/Purchasemdl', 'model');
		$this->load->model('master/Accountmdl');
		$this->load->model('master/Barcodemdl');
		$this->load->library('pagination');
		$this->config->load('extra');
	}
	public function index(){	
		if(sessionExist()){
			if(isset($_GET['action'])){
				if($_GET['action'] == 'view'){
					$config 				= array();
					$config 				= $this->config->item('pagination');	
					$config['total_rows'] 	= $this->model->get_data(true);
					$config['base_url'] 	= base_url("purchase?search=true");

					foreach ($_GET as $key => $value) 
					{
						if($key != 'search' && $key != 'offset')
						{
							$config['base_url'] .= "&" . $key . "=" .$value;
						}
					}

					$offset = (!empty($_GET['offset'])) ? $_GET['offset'] : 0;
					$this->pagination->initialize($config);

					$record['count']		= $offset;
					$record['total_rows'] 	= $config['total_rows'];
					$record['data']			= $this->model->get_data(false, $config['per_page'], $offset);
					// echo "<pre>"; print_r($record); exit;
					
					$this->load->view('pages/purchase/'.$this->master, $record);
				}else if($_GET['action'] == 'add'){
					$record = $this->model->get_data_for_add();
					$this->load->view('pages/purchase/purchase_form', $record);
				}else if($_GET['action'] == 'edit'){
					if(isset($_GET['id']) && !empty($_GET['id'])){
						$record = $this->model->get_data_for_edit($_GET['id']);
						$this->load->view('pages/purchase/purchase_form', $record);	
					}else{
						$this->load->view('errors/error');
					}
				}else if($_GET['action'] == 'print'){
					if(isset($_GET['id']) && !empty($_GET['id'])){
						$record = $this->model->get_data_for_print($_GET['id']);
						$this->load->view('pdfs/barcode_pdf', $record);
					}else{
						$this->load->view('errors/error');
					}
				}else if($_GET['action'] == 'trans_print'){
					if(isset($_GET['id']) && !empty($_GET['id'])){
						$record = $this->model->get_trans_data_for_print($_GET['id']);
						$this->load->view('pdfs/barcode_pdf', $record);
					}else{
						$this->load->view('errors/error');
					}
				}else if($_GET['action'] == 'bill'){
					if(isset($_GET['id']) && !empty($_GET['id'])){
						$record = $this->model->get_data_for_bill_print($_GET['id']);
						// echo "<pre>"; print_r($record); exit;
						$this->load->view('pdfs/purchase/bill', $record);
					}else{
						$this->load->view('errors/error');
					}
				}else{
					$this->load->view('errors/error');
				}
			}else{
				$this->load->view('errors/error');
			}
		}else{
			redirect('login/logout');	
		}
	}

	public function get_supplier_data($id){ 
		$supplier_data = $this->model->get_supplier_state($id);
		$data = ($supplier_data[0]['gst_type_id'] > 1) ? 1 : 0;
        echo json_encode(['status' => TRUE, 'data' => $data, 'msg' => 'Supplier fetched successfully.']);
	}

	public function add_update($id){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$post_data = $this->input->post();
		// echo "<pre>"; print_r($post_data); exit;
		if(empty($post_data)){
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Form data is empty.']);
			return;
		}
		$master_data['pm_entry_no']		= trim($post_data['pm_entry_no']);
		$master_data['pm_entry_date'] 	= date('Y-m-d', strtotime($post_data['pm_entry_date']));
		$master_data['pm_bill_no']		= trim($post_data['pm_bill_no']);
		$master_data['pm_bill_date'] 	= date('Y-m-d', strtotime($post_data['pm_bill_date']));
		$master_data['pm_acc_id']		= trim($post_data['pm_acc_id']);
		$master_data['pm_gst_type']		= trim($post_data['pm_gst_type']);
		$master_data['pm_notes']		= trim($post_data['pm_notes']);
		$master_data['pm_total_qty']	= trim($post_data['pm_total_qty']);
		$master_data['pm_sub_total']	= trim($post_data['pm_sub_total']);
		$master_data['pm_total_disc']	= trim($post_data['pm_total_disc']);
		$master_data['pm_round_off']	= trim($post_data['pm_round_off']);
		$master_data['pm_bill_disc']	= trim($post_data['pm_bill_disc']);
		$master_data['pm_taxable_amt']	= trim($post_data['pm_taxable_amt']);
		$master_data['pm_sgst_amt']		= trim($post_data['pm_sgst_amt']);
		$master_data['pm_cgst_amt']		= trim($post_data['pm_cgst_amt']);
		$master_data['pm_igst_amt']		= trim($post_data['pm_igst_amt']);
		$master_data['pm_gst_amt']		= trim($post_data['pm_gst_amt']);

		$master_data['pm_final_amt']	= trim($post_data['pm_final_amt']);
		$master_data['pm_updated_by'] 	= $_SESSION['user_id'];
		$temp = $this->model->get_record(['pm_id !=' => $id,'pm_entry_no' => $master_data['pm_entry_no'],'pm_fin_year' => $_SESSION['fin_year'] ,'pm_branch_id' => $_SESSION['user_branch_id']], false);
		if(!empty($temp)){
			$master_data['pm_entry_no'] = $this->db_operations->get_fin_year_branch_max_id($this->master, 'pm_entry_no', 'pm_fin_year', $_SESSION['fin_year'], 'pm_branch_id', $_SESSION['user_branch_id']);
		}
		
		$temp1 = $this->model->get_record(['pm_id !=' => $id,'pm_bill_no' => $master_data['pm_bill_no'],'pm_acc_id' => $master_data['pm_acc_id'],'pm_fin_year' => $_SESSION['fin_year']], false);
		if(!empty($temp1)){
			echo json_encode(['status' => true, 'flag' => 2, 'data' => [], 'msg' => 'Bill No of same supplier already exist.']);
			return;
		}

		if($id == 0){
			$this->db->trans_begin();
			$master_data['pm_created_by'] 	= $_SESSION['user_id'];
			$master_data['pm_created_at'] 	= date('Y-m-d H:i:s');
			$master_data['pm_fin_year'] 	= $_SESSION['fin_year'];
			$master_data['pm_branch_id'] 	= $_SESSION['user_branch_id'];
			$id  = $this->db_operations->data_insert($this->master, $master_data);
			$msg = 'Added successfully';
			if($id < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Master data not inserted']);
				return;
			}

			if($this->insert_update_trans($post_data, $id) < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Transaction data not inserted']);
				return;
			}
			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			    echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not inserted']);
				return;
		    }
		    $this->db->trans_commit();
		}else{
			$prev_data = $this->model->get_record(['pm_id' => $id], false);
			if(empty($prev_data)){
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Record not found.']);
				return;
			}
			$this->db->trans_begin();
			$msg = 'Updated successfully';
			if($this->db_operations->data_update($this->master, $master_data, 'pm_id', $id) < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Master data not inserted']);
				return;
			}

			if($this->insert_update_trans($post_data, $id) < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Transaction data not inserted']);
				return;
			}

			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			    echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not updated']);
				return;
		    }
		    $this->db->trans_commit();
		}
		$data['id'] = $id;
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data,  'msg' => $msg]);
	}
	public function insert_update_trans($post_data, $id){
		$trans_db_data = $this->db_operations->get_record($this->trans, ['pt_pm_id' => $id]);
		if(!empty($trans_db_data)){
			foreach ($trans_db_data as $key => $value){
				if(!in_array($value['pt_id'], $post_data['pt_id'])){
					if($this->db_operations->delete_record($this->trans,array('pt_id' =>$value['pt_id'])) < 1){
						return 0;
					}
				} 
			}
		}
		foreach ($post_data['pt_id'] as $key => $value){ 
			$trans_data['pt_pm_id'] 	= $id;
			$trans_data['pt_serial_no'] = $post_data['pt_serial_no'][$key];
			$trans_data['pt_design_id'] = $post_data['pt_design_id'][$key];
			$trans_data['pt_style_id'] 	= $post_data['pt_style_id'][$key];
			$trans_data['pt_brand_id'] 	= $post_data['pt_brand_id'][$key];
			$trans_data['pt_hsn_id'] 	= $post_data['pt_hsn_id'][$key];

			$trans_data['pt_desc'] 		= $post_data['pt_desc'][$key];
			$trans_data['pt_qty'] 		= $post_data['pt_qty'][$key];
			$trans_data['pt_rate'] 		= $post_data['pt_rate'][$key];
			$trans_data['pt_sp_amt'] 	= $post_data['pt_sp_amt'][$key];
			$trans_data['pt_sp_per'] 	= $post_data['pt_sp_per'][$key];
			$trans_data['pt_sub_total'] = $post_data['pt_sub_total'][$key];
			$trans_data['pt_disc_amt'] 	= $post_data['pt_disc_amt'][$key];
			$trans_data['pt_disc_per'] 	= $post_data['pt_disc_per'][$key];
			
			$trans_data['pt_taxable_amt'] 	= $post_data['pt_taxable_amt'][$key];
			$trans_data['pt_sgst_per'] 	= $post_data['pt_sgst_per'][$key];
			$trans_data['pt_sgst_amt'] 	= $post_data['pt_sgst_amt'][$key];
			$trans_data['pt_cgst_per'] 	= $post_data['pt_cgst_per'][$key];
			$trans_data['pt_cgst_amt'] 	= $post_data['pt_cgst_amt'][$key];
			$trans_data['pt_igst_per'] 	= $post_data['pt_igst_per'][$key];
			$trans_data['pt_igst_amt'] 	= $post_data['pt_igst_amt'][$key];

			$trans_data['pt_sub_total_amt']	= $post_data['pt_sub_total_amt'][$key];
			$trans_data['pt_cp_code'] 	= $post_data['pt_cp_code'][$key];
			$trans_data['pt_mrp'] 		= $post_data['pt_mrp'][$key];

			if($value == 0){
				$pt_id = $this->db_operations->data_insert($this->trans, $trans_data);
				if($pt_id < 1){
					return 0;
				}

				for($i = 0;$i < $trans_data['pt_qty'];$i++){
					$year  = date('y');
					$month = date('m');
					$barcode_array['bm_barcode_year'] 		= date('Y');
					$barcode_array['bm_barcode_month'] 		= $month;
					$barcode_array['bm_counter']			= $this->model->generate_barcode();
					$barcode_array['bm_item_code'] 			= $year.''.$month.''.$barcode_array['bm_counter'];
					$barcode_array['bm_pm_id']				= $id;
					$barcode_array['bm_pt_id']				= $pt_id;
					$barcode_array['bm_acc_id']				= $post_data['pm_acc_id'];
					$barcode_array['bm_serial_no']			= $trans_data['pt_serial_no'];
					$barcode_array['bm_design_id']			= $trans_data['pt_design_id'];
					$barcode_array['bm_style_id']			= $trans_data['pt_style_id'];
					$barcode_array['bm_brand_id']			= $trans_data['pt_brand_id'];
					$barcode_array['bm_hsn_id']				= $trans_data['pt_hsn_id'];
					$barcode_array['bm_desc']				= $trans_data['pt_desc'];
					$barcode_array['bm_pt_qty']				= 1;
					$barcode_array['bm_pt_rate']			= $trans_data['pt_rate'];
					$barcode_array['bm_pt_disc']			= $trans_data['pt_disc_amt'] / $trans_data['pt_qty'];
				
					$barcode_array['bm_taxable_amt']		= $trans_data['pt_taxable_amt'] / $trans_data['pt_qty'];
					$barcode_array['bm_sgst_amt']			= $trans_data['pt_sgst_amt'] / $trans_data['pt_qty'];
					$barcode_array['bm_cgst_amt']			= $trans_data['pt_cgst_amt'] / $trans_data['pt_qty'];
					$barcode_array['bm_igst_amt']			= $trans_data['pt_igst_amt'] / $trans_data['pt_qty'];
					$barcode_array['bm_total_amt']			= $trans_data['pt_sub_total_amt'] / $trans_data['pt_qty'];

					$barcode_array['bm_sp_amt']				= $trans_data['pt_sp_amt'];
					$barcode_array['bm_cp_code']			= $trans_data['pt_cp_code'];
					$barcode_array['bm_mrp']				= $trans_data['pt_mrp'];
					$barcode_array['bm_branch_id']			= $_SESSION['user_branch_id'];
					$barcode_array['bm_fin_year']			= $_SESSION['fin_year'];
					if($this->db_operations->data_insert('barcode_master',$barcode_array) < 1){
						return 0;
					}
				}
			}else{
				if($this->db_operations->data_update($this->trans, $trans_data, 'pt_id', $value) < 1){
					return 0;
				}
				$barcode_data = $this->Barcodemdl->get_record(['bm_pt_id' => $value]);
				if(empty($barcode_data)){
					return 0;
				}
				$temp['bm_serial_no'] 	= $trans_data['pt_serial_no'];
				$temp['bm_pt_rate']		= $trans_data['pt_rate'];
				$temp['bm_pt_disc']		= $trans_data['pt_disc_amt'] / $trans_data['pt_qty'];
				$temp['bm_taxable_amt']	= $trans_data['pt_taxable_amt'] / $trans_data['pt_qty'];
				$temp['bm_sgst_amt']	= $trans_data['pt_sgst_amt'] / $trans_data['pt_qty'];
				$temp['bm_cgst_amt']	= $trans_data['pt_cgst_amt'] / $trans_data['pt_qty'];
				$temp['bm_igst_amt']	= $trans_data['pt_igst_amt'] / $trans_data['pt_qty'];
				$temp['bm_total_amt']	= $trans_data['pt_sub_total_amt'] / $trans_data['pt_qty'];

				$temp['bm_sp_amt']		= $trans_data['pt_sp_amt'];
				$temp['bm_cp_code']		= $trans_data['pt_cp_code'];
				$temp['bm_mrp']			= $trans_data['pt_mrp'];
				if($this->db_operations->data_update('barcode_master', $temp, 'bm_pt_id', $value) < 1){
					return 0;
				}
			}

		}
		return 1;
	}
	public function get_data($id){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data = $this->model->get_record(['pm_id' => $id], false);
		if(empty($data)){
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Master not found']);
			return;	
		}
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data, 'msg' => 'Data fetched successfully.']);
	}
	public function get_data_for_payment($id){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data = $this->Accountmdl->get_record(['account_id' => $id]);
		if(empty($data)){
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Master not found']);
			return;	
		}
		$data = $this->model->get_data_for_payment($id);
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data, 'msg' => 'Data fetched successfully.']);
	}
	public function remove($id){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data = $this->model->get_record(['pm_id' => $id], false);
		if(empty($data)){
			echo json_encode(['status' => true, 'flag' => 2, 'data' => [], 'msg' => ' Already deleted']);
			return;	
		}
		$isExist = $this->model->isExist($id);
		if($isExist){
			echo json_encode(['status' => true, 'flag' => 2, 'data' => [], 'msg' => 'Already exist.']);
			return;	
		}
		$this->db->trans_begin();
		if($this->db_operations->delete_record($this->trans, ['pt_pm_id' => $id]) < 1){
			$this->db->trans_rollback();
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Transaction not deleted']);
			return;
		}
		if($this->db_operations->delete_record($this->master, ['pm_id' => $id]) < 1){
			$this->db->trans_rollback();
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Master not deleted']);
			return;
		}
		if ($this->db->trans_status() === FALSE){
		    $this->db->trans_rollback();
		    echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Rollback']);
			return;
	    }
	    $this->db->trans_commit();
		echo json_encode(['status' => true, 'flag' => 1, 'data' => [], 'msg' => 'Deleted successfully']);
	}
	public function get_select2_entry_no(){
		$json = [];
		$data = $this->model->get_select2_entry_no();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
	public function get_select2_bill_no(){
		$json = [];
		$data = $this->model->get_select2_bill_no();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
	public function get_select2_acc_id(){
		$json = [];
		$data = $this->model->get_select2_acc_id();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
	public function get_select2_bm_id(){
		$json = [];
		$data = $this->Barcodemdl->get_select2();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
}
?>
