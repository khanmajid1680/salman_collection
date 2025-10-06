<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Branch extends CI_Controller{
	protected $table;
	protected $term;
	protected $session_expired;
	public function __construct(){
		parent::__construct();

		$this->table 			= 'branch_master';
		$this->term  			= 'branch';
		$this->session_expired  = ['status' => false, 'flag' => -1, 'data' => [], 'msg' => ''];

		$this->load->model('master/Branchmdl', 'model');
		$this->load->library('pagination');
		$this->config->load('extra');
	}
	public function index(){	
		if(sessionExist()){
			if(isset($_GET['action'])){
				if($_GET['action'] == 'view'){
					$config 				= array();
					$config 				= $this->config->item('pagination');	
					$config['total_rows'] 	= $this->model->get_master(true);
					$config['base_url'] 	= base_url("master/".$this->term."?search=true");

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
					$record['data']			= $this->model->get_master(false, $config['per_page'], $offset);
					// echo "<pre>"; print_r($record); exit;
					
					$this->load->view('pages/master/'.$this->table, $record);
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
		$post_data[$this->term.'_name'] 		= trim($post_data[$this->term.'_name']);
		$post_data[$this->term.'_status'] 		= isset($post_data[$this->term.'_status']);
		$post_data[$this->term.'_updated_by'] 	= $_SESSION['user_id'];
		$temp = $this->db_operations->get_record($this->table, [$this->term.'_id !=' => $id, $this->term.'_name' => $post_data[$this->term.'_name']]);
		if(!empty($temp)){
			echo json_encode(['status' => true, 'flag' => 2, 'data' => [], 'msg' => ucfirst($this->term).' already added']);
			return;	
		}
		if($id == 0){
			$this->db->trans_begin();
			$post_data[$this->term.'_created_by'] = $_SESSION['user_id'];
			$post_data[$this->term.'_created_at'] = date('Y-m-d H:i:s');
			$id = $this->db_operations->data_insert($this->table, $post_data);
			$msg = 'Added successfully';
			if($id < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not inserted']);
				return;
			}
			$result = $this->add_default_account($post_data, $id);
			if(!$result['status']){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => $result['msg']]);
				return;
			}
			$result = $this->add_default_user($post_data, $id);
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
		}else{
			$this->db->trans_begin();
			$msg = 'Updated successfully';
			if($this->db_operations->data_update($this->table, $post_data, $this->term.'_id', $id) < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not updated']);
				return;
			}
			$result = $this->update_default_account($post_data, $id);
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
		}
		$data['id'] 	= $id;
		$data['name'] 	= strtoupper($post_data[$this->term.'_name']);
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data,  'msg' => $msg]);
	}
	public function add_default_account($post_data, $id){
		$cash['account_type'] 		= 'GENERAL';
		$cash['account_group_id'] 	= 9;
		$cash['account_name'] 		= 'CASH A/C';
		$cash['account_city_id'] 	= 1;
		$cash['account_state_id'] 	= 1;
		$cash['account_country_id'] = 1;
		$cash['account_gst_type'] 	= 'WITHIN';
		$cash['account_drcr'] 		= 'CR';
		$cash['account_status'] 	= true;
		$cash['account_default'] 	= 1;
		$cash['account_constant'] 	= 'CASH';
		$cash['account_branch_id'] 	= $id;
		$cash['account_created_by'] = $_SESSION['user_id'];
		$cash['account_created_at'] = date('Y-m-d H:i:s');
		$cash['account_updated_by'] = $_SESSION['user_id'];
		$cash['account_updated_at'] = date('Y-m-d H:i:s');
		if($this->db_operations->data_insert('account_master', $cash) < 1){
			return ['status' => FALSE, 'data' => FALSE, 'msg' => 'CASH A/C not added.'];
		}

		$bank['account_type'] 		= 'GENERAL';
		$bank['account_group_id'] 	= 9;
		$bank['account_name'] 		= 'BANK A/C';
		$bank['account_city_id'] 	= 1;
		$bank['account_state_id'] 	= 1;
		$bank['account_country_id'] = 1;
		$bank['account_gst_type'] 	= 'WITHIN';
		$bank['account_drcr'] 		= 'CR';
		$bank['account_status'] 	= true;
		$bank['account_constant'] 	= 'BANK';
		$bank['account_branch_id'] 	= $id;
		$bank['account_created_by'] = $_SESSION['user_id'];
		$bank['account_created_at'] = date('Y-m-d H:i:s');
		$bank['account_updated_by'] = $_SESSION['user_id'];
		$bank['account_updated_at'] = date('Y-m-d H:i:s');
		if($this->db_operations->data_insert('account_master', $bank) < 1){
			return ['status' => FALSE, 'data' => FALSE, 'msg' => 'BANK A/C not added.'];
		}

		$debit['account_type'] 		= 'GENERAL';
		$debit['account_group_id'] 	= 9;
		$debit['account_name'] 		= 'DEBIT NOTE A/C';
		$debit['account_city_id'] 	= 1;
		$debit['account_state_id'] 	= 1;
		$debit['account_country_id']= 1;
		$debit['account_gst_type'] 	= 'WITHIN';
		$debit['account_drcr'] 		= 'CR';
		$debit['account_status'] 	= true;
		$debit['account_constant'] 	= 'DEBIT_NOTE';
		$debit['account_branch_id'] = $id;
		$debit['account_created_by']= $_SESSION['user_id'];
		$debit['account_created_at']= date('Y-m-d H:i:s');
		$debit['account_updated_by']= $_SESSION['user_id'];
		$debit['account_updated_at']= date('Y-m-d H:i:s');
		if($this->db_operations->data_insert('account_master', $debit) < 1){
			return ['status' => FALSE, 'data' => FALSE, 'msg' => 'DEBIT NOTE A/C not added.'];
		}

		$credit['account_type'] 		= 'GENERAL';
		$credit['account_group_id'] 	= 9;
		$credit['account_name'] 		= 'CREDIT NOTE A/C';
		$credit['account_city_id'] 		= 1;
		$credit['account_state_id'] 	= 1;
		$credit['account_country_id']	= 1;
		$credit['account_gst_type'] 	= 'WITHIN';
		$credit['account_drcr'] 		= 'DR';
		$credit['account_status'] 		= true;
		$credit['account_constant'] 	= 'CREDIT_NOTE';
		$credit['account_branch_id'] 	= $id;
		$credit['account_created_by']	= $_SESSION['user_id'];
		$credit['account_created_at']	= date('Y-m-d H:i:s');
		$credit['account_updated_by']	= $_SESSION['user_id'];
		$credit['account_updated_at']	= date('Y-m-d H:i:s');
		if($this->db_operations->data_insert('account_master', $credit) < 1){
			return ['status' => FALSE, 'data' => FALSE, 'msg' => 'CREDIT NOTE A/C not added.'];
		}

		$walkin['account_type'] 		= 'CUSTOMER';
		$walkin['account_group_id'] 	= 5;
		$walkin['account_name'] 		= 'WALKIN';
		$walkin['account_city_id'] 		= 1;
		$walkin['account_state_id'] 	= 1;
		$walkin['account_country_id']	= 1;
		$walkin['account_gst_type'] 	= 'WITHIN';
		$walkin['account_drcr'] 		= 'DR';
		$walkin['account_status'] 		= true;
		$walkin['account_constant'] 	= 'WALKIN';
		$walkin['account_branch_id'] 	= $id;
		$walkin['account_created_by']	= $_SESSION['user_id'];
		$walkin['account_created_at']	= date('Y-m-d H:i:s');
		$walkin['account_updated_by']	= $_SESSION['user_id'];
		$walkin['account_updated_at']	= date('Y-m-d H:i:s');
		if($this->db_operations->data_insert('account_master', $walkin) < 1){
			return ['status' => FALSE, 'data' => FALSE, 'msg' => 'WALKIN A/C not added.'];
		}

		$branch['account_type'] 		= 'BRANCH';
		$branch['account_group_id'] 	= 24;
		$branch['account_name'] 		= strtoupper($post_data[$this->term.'_name']);
		$branch['account_code'] 		= strtoupper($post_data[$this->term.'_name']);
		$branch['account_city_id'] 		= 1;
		$branch['account_state_id'] 	= 1;
		$branch['account_country_id']	= 1;
		$branch['account_gst_type'] 	= 'WITHIN';
		$branch['account_drcr'] 		= 'CR';
		$branch['account_status'] 		= true;
		$branch['account_constant'] 	= 'BRANCH';
		$branch['account_branch_id'] 	= $id;
		$branch['account_created_by']	= $_SESSION['user_id'];
		$branch['account_created_at']	= date('Y-m-d H:i:s');
		$branch['account_updated_by']	= $_SESSION['user_id'];
		$branch['account_updated_at']	= date('Y-m-d H:i:s');
		if($this->db_operations->data_insert('account_master', $branch) < 1){
			return ['status' => FALSE, 'data' => FALSE, 'msg' => 'BRANCH A/C not added.'];
		}

		return ['status' => TRUE, 'data' => TRUE, 'msg' => ''];
	}
	public function update_default_account($post_data, $id){
		$prev_data = $this->db_operations->get_record('account_master', ['account_branch_id' => $id, 'account_type' => 'BRANCH']);
		if(empty($prev_data)){
			// return ['status' => FALSE, 'data' => FALSE, 'msg' => 'Account not found.'];
			$branch['account_type'] 		= 'BRANCH';
			$branch['account_group_id'] 	= 24;
			$branch['account_name'] 		= strtoupper($post_data[$this->term.'_name']);
			$branch['account_code'] 		= strtoupper($post_data[$this->term.'_name']);
			$branch['account_city_id'] 		= 1;
			$branch['account_state_id'] 	= 1;
			$branch['account_country_id']	= 1;
			$branch['account_gst_type'] 	= 'WITHIN';
			$branch['account_drcr'] 		= 'CR';
			$branch['account_status'] 		= true;
			$branch['account_constant'] 	= 'BRANCH';
			$branch['account_branch_id'] 	= $id;
			$branch['account_created_by']	= $_SESSION['user_id'];
			$branch['account_created_at']	= date('Y-m-d H:i:s');
			$branch['account_updated_by']	= $_SESSION['user_id'];
			$branch['account_updated_at']	= date('Y-m-d H:i:s');
			$prev_data[0]['account_id'] = $this->db_operations->data_insert('account_master', $branch);
			if($prev_data[0]['account_id'] < 1){
				return ['status' => FALSE, 'data' => FALSE, 'msg' => 'BRANCH A/C not added.'];
			}
		}

		$prev_walkin = $this->db_operations->get_record('account_master', ['account_branch_id' => $id, 'account_constant' => 'WALKIN']);
		
		if(empty($prev_walkin)){ 
			$walkin['account_type'] 		= 'CUSTOMER';
			$walkin['account_group_id'] 	= 5;
			$walkin['account_name'] 		= 'WALKIN';
			$walkin['account_city_id'] 		= 1;
			$walkin['account_state_id'] 	= 1;
			$walkin['account_country_id']	= 1;
			$walkin['account_gst_type'] 	= 'WITHIN';
			$walkin['account_drcr'] 		= 'DR';
			$walkin['account_status'] 		= true;
			$walkin['account_constant'] 	= 'WALKIN';
			$walkin['account_branch_id'] 	= $id;
			$walkin['account_created_by']	= $_SESSION['user_id'];
			$walkin['account_created_at']	= date('Y-m-d H:i:s');
			$walkin['account_updated_by']	= $_SESSION['user_id'];
			$walkin['account_updated_at']	= date('Y-m-d H:i:s');
			if($this->db_operations->data_insert('account_master', $walkin) < 1){
				return ['status' => FALSE, 'data' => FALSE, 'msg' => 'WALKIN A/C not added.'];
			}
		}

		$branch['account_name'] 		= strtoupper($post_data[$this->term.'_name']);
		$branch['account_code'] 		= strtoupper($post_data[$this->term.'_name']);
		$branch['account_updated_by']	= $_SESSION['user_id'];
		$branch['account_updated_at']	= date('Y-m-d H:i:s');
		if($this->db_operations->data_update('account_master', $branch, 'account_id', $prev_data[0]['account_id']) < 1){
			return ['status' => FALSE, 'data' => FALSE, 'msg' => 'BRANCH A/C not added.'];
		}

		return ['status' => TRUE, 'data' => TRUE, 'msg' => ''];
	}
	public function add_default_user($post_data, $id){
		$sadmin['user_role'] 		= 'SUPER ADMIN';
		$sadmin['user_branch_id'] 	= $id;
		$sadmin['user_name'] 		= 'sadmin';
		$sadmin['user_password'] 	= md5('sadmin');
		$sadmin['user_fullname'] 	= 'SUPER ADMIN';
		$sadmin['user_status'] 		= 1;
		$sadmin['user_type'] 		= 1;
		$sadmin['user_created_by'] 	= $_SESSION['user_id'];
		$sadmin['user_created_at'] 	= date('Y-m-d H:i:s');
		$sadmin['user_updated_by']	= $_SESSION['user_id'];
		$sadmin['user_updated_at'] 	= date('Y-m-d H:i:s');
		if($this->db_operations->data_insert('user_master', $sadmin) < 1){
			return ['status' => FALSE, 'data' => FALSE, 'msg' => 'User not added.'];
		}
		return ['status' => TRUE, 'data' => TRUE, 'msg' => ''];
	}
	public function get_data($id){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data = $this->model->get_record([$this->term.'_id' => $id]);
		if(empty($data)){
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => $this->term.' not found.']);
			return;	
		}
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data, 'msg' => 'Data fetched successfully.']);
	}
	public function remove($id){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data = $this->model->get_record([$this->term.'_id' => $id]);
		if(empty($data)){
			echo json_encode(['status' => true, 'flag' => 2, 'data' => [], 'msg' => 'Not Found.']);
			return;	
		}
		$isExist = $this->model->isExist($id);
		if($isExist){
			echo json_encode(['status' => true, 'flag' => 2, 'data' => [], 'msg' => 'Not Allowed.']);
			return;	
		}
		if($this->db_operations->delete_record($this->table, [$this->term.'_id' => $id]) < 1){
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not deleted.']);
			return;
		}
		echo json_encode(['status' => true, 'flag' => 1, 'data' => [], 'msg' => 'Deleted successfully']);
	}
	public function get_select2(){
		$json = [];
		$data = $this->model->get_select2();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
}
?>
