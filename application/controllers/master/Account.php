<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Account extends CI_Controller{
	protected $table;
	protected $term;
	protected $session_expired;
	public function __construct(){
		parent::__construct();

		$this->table = 'account_master';
		$this->term  = 'account';
		$this->session_expired  = ['status' => false, 'flag' => -1, 'data' => [], 'msg' => ''];

		$this->load->model('master/Accountmdl', 'model');
		$this->load->model('master/Citymdl');
		$this->load->model('master/Countrymdl');
		$this->load->model('master/Statemdl');
		$this->load->model('master/Groupmdl');
		$this->load->model('purchase/Purchasemdl');
		$this->load->model('sales/Salesmdl');
		$this->load->library('pagination');
		$this->config->load('extra');
	}
	public function index(){	
		if(sessionExist()){
			if(isset($_GET['action']) && (isset($_GET['type']) && !empty($_GET['type'])) ){
				if($_GET['action'] == 'view'){
					$type = $_GET['type'];
					$config 				= array();
					$config 				= $this->config->item('pagination');	
					$config['total_rows'] 	= $this->model->get_master(true, $type);
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
					$record['data']			= $this->model->get_master(false, $type, $config['per_page'], $offset);
					// echo "<pre>"; print_r($record); exit;
					
					$this->load->view('pages/master/'.$this->table, $record);
				}else if($_GET['action'] == 'add'){
					$record = $this->model->get_data_for_add();
					// echo "<pre>"; print_r($record); exit;
					$this->load->view('pages/master/account_form', $record);
				}else if($_GET['action'] == 'edit'){
					if(isset($_GET['id']) && !empty($_GET['id'])){
						$id = encrypt_decrypt("decrypt", $_GET['id'], SECRET_KEY);
						$record = $this->model->get_data_for_edit($id);
						// echo "<pre>"; print_r($record); exit;
						$this->load->view('pages/master/account_form', $record);	
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
		$post_data['account_name'] 			= trim($post_data['account_name']);
		$post_data['account_code'] 			= trim($post_data['account_code']);
		$post_data['account_mobile'] 		= trim($post_data['account_mobile']);
		$post_data['account_tel1'] 			= trim($post_data['account_tel1']);
		$post_data['account_tel2'] 			= trim($post_data['account_tel2']);
		$post_data['account_email']			= trim($post_data['account_email']);
		$post_data['account_address']		= trim($post_data['account_address']);
		$post_data['account_gst_type']		= $post_data['account_state_id'] == 1 ? 'WITHIN' : 'OUTSIDE';
		$post_data['account_gst_no']		= trim($post_data['account_gst_no']);
		$post_data['account_pan_no']		= trim($post_data['account_pan_no']);
		$post_data['account_disc_per']		= trim($post_data['account_disc_per']);
		$post_data['account_credit_days']	= trim($post_data['account_credit_days']);
		$post_data['account_open_bal']		= trim($post_data['account_open_bal']);
		$post_data['account_reference']		= trim($post_data['account_reference']);
		$post_data['account_status']		= isset($post_data['account_status']);
		$post_data['account_updated_by'] 	= $_SESSION['user_id'];

		if(!empty($post_data['account_code'])){
			$data = $this->model->get_record(['account_id !=' => $id, 'account_type' => $post_data['account_type'], 'account_code' => $post_data['account_code'], 'account_branch_id' => $_SESSION['user_branch_id']]);	
			if(!empty($data)){
				echo json_encode(['status' => true, 'flag' => 2, 'data' => [], 'msg' => strtoupper($post_data['account_code']).' already added']);
				return;	
			}
		}
		if(!empty($post_data['account_mobile'])){
			if($post_data['account_type'] == 'CUSTOMER'){
				$data = $this->model->get_record(['account_id !=' => $id, 'account_type' => $post_data['account_type'], 'account_mobile' => $post_data['account_mobile']]);	

			}else{
				$data = $this->model->get_record(['account_id !=' => $id, 'account_type' => $post_data['account_type'], 'account_mobile' => $post_data['account_mobile'], 'account_branch_id' => $_SESSION['user_branch_id']]);	
			}

			if(!empty($data)){
				echo json_encode(['status' => true, 'flag' => 2, 'data' => [], 'msg' => 'Mobile No already added']);
				return;	
			}
		}else{
			$data = $this->model->get_record(['account_id !=' => $id, 'account_type' => $post_data['account_type'], 'account_name' => $post_data['account_name'], 'account_branch_id' => $_SESSION['user_branch_id']]);	
			if(!empty($data)){
				echo json_encode(['status' => true, 'flag' => 2, 'data' => [], 'msg' => ucfirst($post_data['account_name']).' already added']);
				return;	
			}
		}

		if($id == 0){
			$post_data['account_branch_id'] 	= $_SESSION['user_branch_id'];
			$post_data['account_created_by'] 	= $_SESSION['user_id'];
			$post_data['account_created_at'] 	= date('Y-m-d H:i:s');
			$id = $this->db_operations->data_insert($this->table, $post_data);
			$msg = 'Added successfully';
			if($id < 1){
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not inserted']);
				return;
			}
		}else{
			$prev_data = $this->db_operations->get_record('account_master', ['account_id' => $id]);
			if(empty($prev_data)){
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not found']);
				return;
			}

			if(!empty($prev_data[0]['account_constant'])){
				$post_data['account_status'] = true;
			}

			$msg = 'Updated successfully';
			if($this->db_operations->data_update($this->table, $post_data, 'account_id', $id) < 1){
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not updated']);
				return;
			}
		}
		$data['id'] 	= $id;
		$data['name'] 	= strtoupper($post_data['account_name']).' - '.strtoupper($post_data['account_code']).' - '.$post_data['account_mobile'];
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data,  'msg' => $msg]);
	}
	public function add_customer($id){
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
		$post_data['account_name'] 			= trim($post_data['account_name']);
		$post_data['account_mobile'] 		= trim($post_data['account_mobile']);
		$post_data['account_gst_type']		= 'WITHIN';
		$post_data['account_city_id']		= 1;
		$post_data['account_state_id']		= 1;
		$post_data['account_country_id']	= 1;
		// $post_data['account_disc_per']		= trim($post_data['account_disc_per']);
		// $post_data['account_open_bal']		= trim($post_data['account_open_bal']);
		$post_data['account_status']		= isset($post_data['account_status']);
		$post_data['account_updated_by'] 	= $_SESSION['user_id'];

		$data = $this->model->get_record(['account_id !=' => $id, 'account_type' => $post_data['account_type'], 'account_mobile' => $post_data['account_mobile']]);	
		
		if(!empty($data)){
			echo json_encode(['status' => true, 'flag' => 2, 'data' => [], 'msg' => 'Mobile No. already exist.']);
			return;	
		}
		
		if($id == 0){
			$post_data['account_branch_id'] 	= $_SESSION['user_branch_id'];
			$post_data['account_created_by'] 	= $_SESSION['user_id'];
			$post_data['account_created_at'] 	= date('Y-m-d H:i:s');
			$id = $this->db_operations->data_insert($this->table, $post_data);
			$msg = 'Added successfully';
			if($id < 1){
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not inserted']);
				return;
			}
		}
		$data['id'] 	= $id;
		$data['name'] 	= strtoupper($post_data['account_name']).' - '.$post_data['account_mobile'];
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data,  'msg' => $msg]);
	}
	public function get_data($id){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data 	= $this->model->get_record(['account_id' => $id]);
		if(empty($data)){
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => ucfirst($this->term).' not found']);
			return;	
		}
		$isExist= $this->model->isExist($id);
		$data[0]['isExist'] = $isExist;
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data, 'msg' => 'Data fetched successfully.']);
	}
	public function get_customer_data_with_loyalty($id){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data = $this->model->get_customer_data_with_loyalty($id);
		if(empty($data)){
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => ucfirst($this->term).' not found']);
			return;	
		}
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data, 'msg' => 'Data fetched successfully.']);
	}
	public function get_customer_data($mobile){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data = $this->model->get_record(['account_mobile' => $mobile]);
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data, 'msg' => 'Data fetched successfully.']);
	}
	public function get_walkin_data(){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data = $this->db_operations->get_record('account_master', ['account_constant' => 'WALKIN', 'account_branch_id' => $_SESSION['user_branch_id']]);
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data, 'msg' => 'Data fetched successfully.']);
	}
	public function get_data_for_account(){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data['cities'] 	= $this->Citymdl->get_record(['city_status' => true], true);
		$data['states'] 	= $this->Statemdl->get_record(['state_status' => true], true);
		$data['countries'] 	= $this->Countrymdl->get_record(['country_status' => true], true);
		$data['groups'] 	= $this->Groupmdl->get_record(['group_status' => true], true);
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data, 'msg' => 'Data fetched successfully.']);
	}
	public function get_data_for_message($type){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data = $this->model->get_record(['account_type' => $type, 'account_mobile !=' => '',  'account_status' => true]);
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
		if(!empty($data[0]['account_constant'])){
			echo json_encode(['status' => true, 'flag' => 2, 'data' => [], 'msg' => 'Not allowed.']);
			return;	
		}
		$isExist = $this->model->isExist($id);
		if($isExist){
			echo json_encode(['status' => true, 'flag' => 2, 'data' => [], 'msg' => 'Not Allowed.']);
			return;	
		}
		if($this->db_operations->delete_record($this->table, ['account_id' => $id]) < 1){
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not deleted']);
			return;
		}
		echo json_encode(['status' => true, 'flag' => 1, 'data' => [], 'msg' => 'Deleted successfully']);
	}
	public function get_account_balance($id){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$temp = $this->model->get_record(['account_id' => $id]);
		if(empty($temp)){
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => ucfirst($this->term).' not found']);
			return;	
		}
		$data['bal_data'] 	= $this->model->get_account_balance($id);
		$data['pur_data'] 	= [];
		$data['sales_data'] = [];
		$data['ret_data'] 	= [];
		if($temp[0]['account_type'] == 'SUPPLIER'){
			$balance_data  		= $this->Purchasemdl->get_balance($id);
			$purchase_data 		= $this->Purchasemdl->get_data_for_payment($id);

			$data['bill_amt']	= $balance_data['bill_amt'];
			$data['return_amt']	= $balance_data['return_amt'];
			
			$data['pur_data'] 	= $purchase_data['pur_data'];
			$data['ret_data'] 	= $purchase_data['ret_data'];
		}else if($temp[0]['account_type'] == 'CUSTOMER'){
			$balance_data  		= $this->Salesmdl->get_balance($id);
			$sales_data 		= $this->Salesmdl->get_data_for_payment($id);

			$data['bill_amt']	= $balance_data['bill_amt'];
			$data['return_amt']	= $balance_data['return_amt'];
			
			$data['sales_data'] = $sales_data['sales_data'];
			$data['ret_data'] 	= $sales_data['ret_data'];

			// echo "<pre>"; print_r($data);die;
		}
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data, 'msg' => 'Data fetched successfully.']);
	}
	public function get_select2_supplier(){
		$json = [];
		$data = $this->model->get_select2_supplier();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['account_id'], 'text'=>$value['account_name']];
		}
		echo json_encode($json);
	}
	public function get_select2_customer(){
		$json = [];
		$data = $this->model->get_select2_customer();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['account_id'], 'text'=>$value['account_name']];
		}
		echo json_encode($json);
	}
	public function get_account_select2(){
		$json = [];
		$data = $this->model->get_account_select2();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
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
