<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Style extends CI_Controller{
	protected $table;
	protected $term;
	protected $session_expired;
	public function __construct(){
		parent::__construct();

		$this->table 			= 'style_master';
		$this->term  			= 'style';
		$this->session_expired  = ['status' => false, 'flag' => -1, 'data' => [], 'msg' => ''];

		$this->load->model('master/Stylemdl', 'model');
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
			$post_data[$this->term.'_created_by'] = $_SESSION['user_id'];
			$post_data[$this->term.'_created_at'] = date('Y-m-d H:i:s');
			$id = $this->db_operations->data_insert($this->table, $post_data);
			$msg = 'Added successfully';
			if($id < 1){
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not inserted']);
				return;
			}
		}else{
			$msg = 'Updated successfully';
			if($this->db_operations->data_update($this->table, $post_data, $this->term.'_id', $id) < 1){
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not updated']);
				return;
			}
		}
		$data['id'] 	= $id;
		$data['name'] 	= strtoupper($post_data[$this->term.'_name']);
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data,  'msg' => $msg]);
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
