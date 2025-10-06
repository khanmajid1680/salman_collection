<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Home extends CI_Controller {
		protected $session_expired;
		public function __construct(){
			parent::__construct();
			
			$this->config->load('extra');
			$this->load->model('Homemdl', 'model');
			$this->load->model('report/Daily_transactionmdl');
			$this->session_expired  = ['status' => false, 'flag' => -1, 'data' => [], 'msg' => ''];
		}
		public function index(){
			if(sessionExist()){
				if($_SESSION['user_role'] == SALES){
					// redirect(base_url('sales?action=view'));
					$record['cash'] = $this->Daily_transactionmdl->get_data('CASH');
					$record['bank'] = $this->Daily_transactionmdl->get_data('BANK');
					// echo "<pre>"; print_r($record); exit;

					$this->load->view('pages/home/sales_dashboard', $record);
				}else if($_SESSION['user_role'] == PURCHASE){
					redirect(base_url('purchase?action=view'));	
				}else{
					$record['first'] 	= $this->model->get_first();
					// echo "<pre>"; print_r($record); exit;
					$this->load->view('pages/home/admin_dashboard', $record);
				}
			}else{
				redirect('login/logout');	
			}
		}
		public function get_data(){
			if(!sessionExist()){
				echo json_encode($this->session_expired);
				return;
			}
			$data['first_data'] 	= $this->model->get_first();
			$data['second_data'] 	= $this->model->get_second();
			$data['third_data'] 	= $this->model->get_third();
			$data['fourth_data'] 	= $this->model->get_fourth();
			echo json_encode(['status' => true, 'flag' => 1, 'data' => $data, 'msg' => 'Data fetched successfully.']);
		}
		public function sales(){
			if(!sessionExist()){
				echo json_encode($this->session_expired);
				return;
			}
			$data['time'] = date('d-m-Y h:i:s a');
			$data['cash'] = $this->Daily_transactionmdl->get_data('CASH');
			$data['bank'] = $this->Daily_transactionmdl->get_data('BANK');
			echo json_encode(['status' => true, 'flag' => 1, 'data' => $data, 'msg' => 'Data fetched successfully.']);
		}
	}
?>