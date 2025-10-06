<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Receipt extends CI_Controller{
	protected $master;
	protected $trans;
	protected $session_expired;
	public function __construct(){
		parent::__construct();

		$this->master 			= 'voucher_master';
		$this->trans 			= 'voucher_trans';
		$this->session_expired  = ['status' => false, 'flag' => -1, 'data' => [], 'msg' => ''];
		
		$this->load->model('voucher/Receiptmdl', 'model');
		$this->load->model('purchase/Purchasemdl');
		$this->load->model('sales/Salesmdl');
		$this->load->model('master/Accountmdl');
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
					$config['base_url'] 	= base_url("voucher/receipt?search=true");

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
					
					$this->load->view('pages/voucher/receipt_master', $record);
				}else if($_GET['action'] == 'add'){
					$record = $this->model->get_data_for_add();
					$this->load->view('pages/voucher/receipt_form', $record);
				}else if($_GET['action'] == 'edit'){
					if(isset($_GET['id']) && !empty($_GET['id'])){
						$record = $this->model->get_data_for_edit($_GET['id']);
						$this->load->view('pages/voucher/receipt_form', $record);	
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
		$master_data['vm_entry_no']			= trim($post_data['vm_entry_no']);
		$master_data['vm_entry_date'] 		= date('Y-m-d',strtotime($post_data['vm_entry_date']));	
		$master_data['vm_type']				= trim($post_data['vm_type']);
		$master_data['vm_constant']			= trim($post_data['vm_constant']);
		$master_data['vm_acc_id']			= trim($post_data['vm_acc_id']);
		$master_data['vm_party_id']			= trim($post_data['vm_party_id']);
		$master_data['vm_group']			= trim($post_data['vm_group']);
		$master_data['vm_bill_amt']			= trim($post_data['vm_bill_amt']);
		$master_data['vm_return_amt']		= trim($post_data['vm_return_amt']);
		$master_data['vm_total_amt']		= trim($post_data['vm_total_amt']);
		$master_data['vm_round_off']		= trim($post_data['vm_round_off']);
		$master_data['vm_cheque_no']		= trim($post_data['vm_cheque_no']);
		$master_data['vm_cheque_date']		= !empty($post_data['vm_cheque_date']) ? date('Y-m-d', strtotime($post_data['vm_cheque_date'])) : '0000-00-00';
		$master_data['vm_notes']			= trim($post_data['vm_notes']);
		$master_data['vm_updated_by'] 		= $_SESSION['user_id'];				

		$temp = $this->model->get_record(['vm_id !=' => $id,'vm_entry_no' => $master_data['vm_entry_no'],'vm_fin_year' => $_SESSION['fin_year'],'vm_branch_id' => $_SESSION['user_branch_id']]);
		if(!empty($temp)){
			$master_data['vm_entry_no'] = $this->db_operations->get_fin_year_branch_max_id($this->master, 'vm_entry_no', 'vm_fin_year', $_SESSION['fin_year'], 'vm_branch_id', $_SESSION['user_branch_id']);
		}
		if($id == 0){
			$this->db->trans_begin();
			$master_data['vm_created_by'] 	= $_SESSION['user_id'];
			$master_data['vm_created_at'] 	= date('Y-m-d H:i:s');
			$master_data['vm_fin_year'] 	= $_SESSION['fin_year'];
			$master_data['vm_branch_id'] 	= $_SESSION['user_branch_id'];
			$id  = $this->db_operations->data_insert($this->master, $master_data);
			$msg = 'Added successfully';
			if($id < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Master data not inserted']);
				return;
			}
			
		}else{
			$prev_data = $this->model->get_record(['vm_id' => $id]);
			if(empty($prev_data)){
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Record not found.']);
				return;
			}
			$this->db->trans_begin();
			$msg = 'Updated successfully';
			if($this->db_operations->data_update($this->master, $master_data, 'vm_id', $id) < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Master data not updated']);
				return;
			}
		}

		if(isset($post_data['vt_bill_clear']) && in_array('YES', $post_data['vt_bill_clear'])){
			if($this->insert_update_trans($post_data, $id) < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Transaction data not inserted']);
				return;
			}
		}

		$result = $this->add_update_payment_mode($post_data, $id);
		if(!$result['status']){
			$this->db->trans_rollback();
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => $result['msg']]);
			return;
		}

		if ($this->db->trans_status() === FALSE){
		    $this->db->trans_rollback();
		    echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not inserted']);
			return;
	    }
	    $this->db->trans_commit();

		$data['id'] = $id;
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data,  'msg' => $msg]);
	}
	public function insert_update_trans($post_data, $id){
		foreach ($post_data['vt_id'] as $key => $value){
			if($post_data['vt_bill_clear'][$key] == 'YES'){
				$trans_data['vt_vm_id'] 		= $id;
				$trans_data['vt_pm_id'] 	 	= 0;
				$trans_data['vt_sm_id'] 	 	= isset($post_data['vt_sm_id'][$key]) ? $post_data['vt_sm_id'][$key] : 0;;
				$trans_data['vt_bill_no'] 	 	= $post_data['vt_bill_no'][$key];
				$trans_data['vt_bill_date'] 	= date('Y-m-d', strtotime($post_data['vt_bill_date'][$key]));
				$trans_data['vt_total_qty'] 	= $post_data['vt_total_qty'][$key];
				$trans_data['vt_total_amt'] 	= $post_data['vt_total_amt'][$key];
				
				$trans_data['vt_allocated_amt'] = $post_data['vt_allocated_amt'][$key];
				$trans_data['vt_allocated_round_off']= $post_data['vt_allocated_round_off'][$key];
				
				$trans_data['vt_adjust_amt'] 	= $post_data['vt_adjust_amt'][$key];
				$trans_data['vt_adjust_round_off']= $post_data['vt_adjust_round_off'][$key];
				
				$trans_data['vt_return_qty'] 	= isset($post_data['vt_return_qty'][$key])?$post_data['vt_return_qty'][$key]:0;
				$trans_data['vt_return_amt'] 	= isset($post_data['vt_return_amt'][$key])?$post_data['vt_return_amt'][$key]:0;
				if($value == 0){
					$vt_id = $this->db_operations->data_insert($this->trans, $trans_data);
					if($vt_id < 1) return 0;
				}
			}
		}
		return 1;
	}

	public function add_update_payment_mode($post_data, $id){
			$trans_db_data = $this->db_operations->get_record('receipt_payment_mode_trans', ['rpmt_receipt_id' => $id, 'rpmt_delete_status' => false]);
			if(!empty($trans_db_data)){
				foreach ($trans_db_data as $key => $value){
					if(!in_array($value['rpmt_id'], $post_data['rpmt_id'])){
						$update_data 						= [];
						$update_data['rpmt_delete_status'] 	= true;
						$update_data['rpmt_updated_by'] 	= $_SESSION['user_id'];
						$update_data['rpmt_updated_at'] 	= date('Y-m-d H:i:s');
						if($this->db_operations->data_update('receipt_payment_mode_trans', $update_data, 'rpmt_id', $value['rpmt_id']) < 1){
							return ['status' => FALSE, 'data' => FALSE, 'msg' => '1. Payment mode not deleted.'];
						}
					}
				}
				foreach ($post_data['rpmt_amt'] as $key => $value) {
					if($value <= 0){
						$update_data 						= [];
						$update_data['rpmt_delete_status'] 	= true;
						$update_data['rpmt_updated_by'] 	= $_SESSION['user_id'];
						$update_data['rpmt_updated_at'] 	= date('Y-m-d H:i:s');
						if($this->db_operations->data_update('receipt_payment_mode_trans', $update_data, 'rpmt_id', $post_data['rpmt_id'][$key]) < 1){
							return ['status' => FALSE, 'data' => FALSE, 'msg' => '1. Payment mode not deleted.'];
						}
					}
				}
			}
			foreach ($post_data['rpmt_amt'] as $key => $value){

				if($value > 0){
					$trans_data							= [];
					$trans_data['rpmt_receipt_id']		= $id;
					// $trans_data['rpmt_receipt_uuid']	= $post_data['receipt_uuid'];
					$trans_data['rpmt_payment_mode_id']	= $post_data['rpmt_payment_mode_id'][$key];
					$trans_data['rpmt_amt']				= $post_data['rpmt_amt'][$key];
					$trans_data['rpmt_delete_status']	= false;
					$trans_data['rpmt_updated_by'] 		= $_SESSION['user_id'];
					$trans_data['rpmt_updated_at'] 		= date('Y-m-d H:i:s');
					
					if(empty($post_data['rpmt_id'][$key])){
						$trans_data['rpmt_created_by'] 	= $_SESSION['user_id'];
						$trans_data['rpmt_created_at'] 	= date('Y-m-d H:i:s');
						if($this->db_operations->data_insert('receipt_payment_mode_trans', $trans_data) < 1){
							return ['status' => FALSE, 'data' => FALSE, 'msg' => '1. Payment mode not added.'];
						}
					}else{
						if($this->db_operations->data_update('receipt_payment_mode_trans', $trans_data, 'rpmt_id', $post_data['rpmt_id'][$key]) < 1){
							return ['status' => FALSE, 'data' => FALSE, 'msg' => '1. Payment mode not updated.'];
						}
					}
				}
			}
			return ['status' => TRUE, 'data' => TRUE, 'msg' => ''];
		}
	public function remove($id){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data = $this->model->get_record(['vm_id' => $id]);
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
		if($this->db_operations->delete_record($this->trans, ['vt_vm_id' => $id]) < 1){
			$this->db->trans_rollback();
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Transaction not deleted']);
			return;
		}


		$prev_data = $this->db_operations->get_record('receipt_payment_mode_trans', ['rpmt_receipt_id' => $id, 'rpmt_delete_status' => false]);
		if(!empty($prev_data)){
			$update_data 						= [];
			$update_data['rpmt_delete_status'] 	= true; 
			$update_data['rpmt_updated_by'] 	= $_SESSION['user_id']; 
			$update_data['rpmt_updated_at'] 	= date('Y-m-d H:i:s'); 
			if($this->db_operations->data_update('receipt_payment_mode_trans', $update_data, 'rpmt_receipt_id', $id) < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => '1. Payment not deleted.']);
				return;
			}
		}

		if($this->db_operations->delete_record($this->master, ['vm_id' => $id]) < 1){
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

	public function get_payment_mode_data($receipt_id){ 
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data['payment_mode_data'] = $this->model->get_payment_mode_data($receipt_id);
		echo json_encode(['session' => TRUE, 'status' => TRUE, 'data' => $data, 'msg' => 'Data fetched successfully.']);
	}

	public function get_data($id){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data = $this->model->get_record(['vm_id' => $id]);
		if(empty($data)){
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Master not found']);
			return;	
		}
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data, 'msg' => 'Data fetched successfully.']);
	}
	public function get_select2_entry_no(){
		$json = [];
		$data = $this->model->get_select2_entry_no();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
	public function get_select2_account_id(){
		$json = [];
		$data = $this->model->get_select2_account_id();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
	public function get_select2_party_id(){
		$json = [];
		$data = $this->model->get_select2_party_id();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
}
?>
