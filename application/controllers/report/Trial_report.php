<?php defined('BASEPATH') OR exit('No direct script access allowed');
class trial_report extends CI_Controller{
	protected $session_expired;
	public function __construct(){
		parent::__construct();

		$this->session_expired  = ['status' => false, 'flag' => -1, 'data' => [], 'msg' => ''];
		$this->load->model('report/trial_report_model', 'model');
		$this->load->library('pagination');
		$this->config->load('extra');
	}
	public function index(){	
		if(sessionExist()){
			$record['data']	= $this->model->get_data();
			// echo "<pre>"; print_r($record); exit;
			if(isset($_GET['submit']) && !empty($_GET['submit']) && $_GET['submit'] == 'PDF'){
				$this->load->view('pdfs/report/trial_report', $record);
			}else{
				$record['total_rows']	= !empty($record['data']['data']) ? count($record['data']['data']) : 0;
				$this->load->view('pages/report/trial_report', $record);
			}
		}else{
			redirect('login/logout');	
		}
	}

	public function set_order_status($id,$status){
		
		if(empty($id) || $id<0){
			return ['msg' => '1. Order not found.'];
		}
		if($this->db_operations->data_update('sales_trans',['st_alter_status'=>$status], 'st_id', $id) < 1) return ['msg' => 'Order not updated.'];
		echo json_encode(['status' => TRUE, 'data' => $id,  'msg' => 'Order status updated successfully.']);
	}

}
?>
