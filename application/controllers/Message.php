<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Message extends CI_Controller{
	protected $master;
	protected $trans;
	protected $session_expired;
	public function __construct(){
		parent::__construct();

		$this->master 			= 'message_master';
		$this->trans 			= 'message_trans';
		$this->session_expired  = ['status' => false, 'flag' => -1, 'data' => [], 'msg' => ''];
		
		$this->load->model('utility/Messagemdl', 'model');
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
					$config['base_url'] 	= base_url("message?search=true");

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
					
					$this->load->view('pages/utility/'.$this->master, $record);
				}else if($_GET['action'] == 'add'){
					$record = $this->model->get_data_for_add();
					$this->load->view('pages/utility/message_form', $record);
				}else if($_GET['action'] == 'edit'){
					if(isset($_GET['id']) && !empty($_GET['id'])){
						$record = $this->model->get_data_for_edit($_GET['id']);
						$this->load->view('pages/utility/message_form', $record);	
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
	public function send_sms($mobile, $message){
		return 1;
	}
	public function send_trial_message(){
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
		$mobile	= trim($post_data['mobile']);
		$message= trim($post_data['message']);

		if(empty($mobile) || empty($message)){
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Mobile or Message should not be blank.']);
			return;
		}

		if($this->send_sms($mobile, $message) < 1){
			echo json_encode(['status' => true, 'flag' => 2, 'data' => [], 'msg' => 'SMS failed.']);
			return;
		}
		echo json_encode(['status' => true, 'flag' => 1, 'data' => [],  'msg' => 'SMS send successfully.']);
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
		$master_data['mm_entry_no']			= trim($post_data['mm_entry_no']);
		$master_data['mm_entry_date'] 		= date('Y-m-d',strtotime($post_data['mm_entry_date']));	
		$master_data['mm_account_type']		= trim($post_data['mm_account_type']);
		$master_data['mm_description']		= trim($post_data['mm_description']);
		$master_data['mm_trial_mobile']		= trim($post_data['mm_trial_mobile']);
		$master_data['mm_total_qty']		= trim($post_data['mm_total_qty']);
		$master_data['mm_total_sent_qty']	= trim($post_data['mm_total_sent_qty']);
		$master_data['mm_total_failed_qty']	= trim($post_data['mm_total_failed_qty']);
		$master_data['mm_updated_by'] 		= $_SESSION['user_id'];

		$temp = $this->model->get_record(['mm_id !=' => $id,'mm_entry_no' => $master_data['mm_entry_no'],'mm_fin_year' => $_SESSION['fin_year'],'mm_branch_id' => $_SESSION['user_branch_id']]);
		if(!empty($temp)){
			$master_data['mm_entry_no'] = $this->db_operations->get_fin_year_branch_max_id($this->master, 'mm_entry_no', 'mm_fin_year', $_SESSION['fin_year'], 'mm_branch_id', $_SESSION['user_branch_id']);
		}
		if($id == 0){
			$this->db->trans_begin();
			$master_data['mm_created_by'] 	= $_SESSION['user_id'];
			$master_data['mm_created_at'] 	= date('Y-m-d H:i:s');
			$master_data['mm_fin_year'] 	= $_SESSION['fin_year'];
			$master_data['mm_branch_id'] 	= $_SESSION['user_branch_id'];
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
			$this->db->trans_begin();
			$msg = 'Updated successfully';
			if($this->db_operations->data_update($this->master, $master_data, 'mm_id', $id) < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Master data not updated']);
				return;
			}

			if($this->insert_update_trans($post_data, $id) < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Transaction data not updated']);
				return;
			}
			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			    echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Rollback']);
				return;
		    }
		    $this->db->trans_commit();
		}
		$data['id'] = $id;
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data,  'msg' => $msg]);
	}
	public function insert_update_trans($post_data, $id){
		$success_cnt= 0;
		$failed_cnt = 0;
		foreach ($post_data['mt_id'] as $key => $value){
			if($post_data['mt_status'][$key] == 0){
				if($post_data['send_sms'][$key] == 'YES'){
					$trans_data['mt_mm_id'] 		= $id;
					$trans_data['mt_account_name'] 	= $post_data['mt_account_name'][$key];
					$trans_data['mt_account_mobile']= $post_data['mt_account_mobile'][$key];
					if($this->send_sms($trans_data['mt_account_mobile'], $post_data['mm_description']) < 1){
						$trans_data['mt_status'] 	= 0;
						$failed_cnt = $failed_cnt + 1;
					}else{
						$trans_data['mt_status'] 	= 1;
						$success_cnt = $success_cnt + 1;
					}
					if($value == 0){
						$mt_id = $this->db_operations->data_insert($this->trans, $trans_data);
						if($mt_id < 1) return 0;
					}else{
						if($this->db_operations->data_update($this->trans, $trans_data, 'mt_id', $value) < 1) return 0;
					}
				}else{
					if($value != 0){
						if($this->db_operations->delete_record($this->trans, ['mt_id' => $value]) < 1) return 0;
					}
				}
			}
		}
		if($this->db_operations->data_update($this->master, ['mm_total_failed_qty' => $failed_cnt, 'mm_total_sent_qty' => $success_cnt], 'mm_id', $id) < 1){
			return 0;
		}
		return 1;
	}
	public function get_data($id){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data = $this->model->get_record(['mm_id' => $id]);
		if(empty($data)){
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Master not found']);
			return;	
		}
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data, 'msg' => 'Data fetched successfully.']);
	}
	public function remove($id){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data = $this->model->get_record(['mm_id' => $id]);
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
		if($this->db_operations->delete_record($this->trans, ['mt_mm_id' => $id]) < 1){
			$this->db->trans_rollback();
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Transaction not deleted']);
			return;
		}
		if($this->db_operations->delete_record($this->master, ['mm_id' => $id]) < 1){
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

}
?>
