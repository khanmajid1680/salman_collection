<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Purchase_summary extends CI_Controller{
	protected $session_expired;
	public function __construct(){
		parent::__construct();

		$this->session_expired  = ['status' => false, 'flag' => -1, 'data' => [], 'msg' => ''];
		
		$this->load->model('report/Purchase_summarymdl', 'model');
		$this->load->library('pagination');
		$this->config->load('extra');
	}
	public function index(){	
		if(sessionExist()){
			$record['data']	= $this->model->get_data();
			// echo "<pre>"; print_r($record); exit;
			if(isset($_GET['submit']) && !empty($_GET['submit']) && $_GET['submit'] == 'PDF'){
				$this->load->view('pdfs/report/purchase_summary', $record);
			}else{
				$record['total_rows']	= !empty($record['data']['data']) ? count($record['data']['data']) : 0;
				$this->load->view('pages/report/purchase_summary', $record);
			}
		}else{
			redirect('login/logout');	
		}
	}
}
?>
