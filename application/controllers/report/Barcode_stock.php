<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Barcode_stock extends CI_Controller{
	protected $session_expired;
	public function __construct(){
		parent::__construct();

		$this->session_expired  = ['status' => false, 'flag' => -1, 'data' => [], 'msg' => ''];
		
		$this->load->model('report/Barcode_stockmdl', 'model');
		$this->load->library('pagination');
		$this->config->load('extra');
	}
	public function index(){	
		if(sessionExist()){
			$record['data']	= $this->model->get_data();
			// echo "<pre>"; print_r($record); exit;
			if(isset($_GET['submit']) && !empty($_GET['submit']) && $_GET['submit'] == 'PDF'){
				$this->load->view('pdfs/report/barcode_stock', $record);
			}else{
				$record['total_rows']	= !empty($record['data']['data']) ? count($record['data']['data']) : 0;
				$this->load->view('pages/report/barcode_stock', $record);
			}
		}else{
			redirect('login/logout');	
		}
	}
	public function update_barcode($id){
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
		$prev_data = $this->db_operations->get_record('barcode_master', ['bm_id' => $id]);
		// echo "<pre>"; print_r($prev_data); exit;
		if(empty($prev_data)){
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Barcode not found.']);
			return;	
		}
		if($this->db_operations->data_update('barcode_master', $post_data, 'bm_id', $id) < 1){
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Barcode not updated.']);
			return;	
		}
		echo json_encode(['status' => true, 'flag' => 1, 'data' => [],  'msg' => 'Barcode update successfully.']);
	}
	public function get_data(){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data = $this->model->get_data();
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data, 'msg' => 'Data fetched successfully.']);
	}
}
?>
