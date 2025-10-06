<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Common extends CI_Controller{
	protected $session_expired;
	public function __construct(){
		parent::__construct();

		$this->config->load('extra');
	}
	public function index(){	
		if(sessionExist()){
			$this->load->view('errors/error');
		}else{
			redirect('login/logout');	
		}
	}
	public function get_select2_role(){
		$json = [];
		$data = $this->config->item('role');
		foreach ($data as $key => $value){
			$json[] = ['id'=>$key, 'text'=>$value];
		}
		echo json_encode($json);
	}
	public function get_select2_mode(){
		$json = [];
		$data = $this->config->item('payment_mode');
		foreach ($data as $key => $value){
			$json[] = ['id'=>$key, 'text'=>$value];
		}
		echo json_encode($json);
	}
	public function get_select2_status(){
		$json = [];
		$data = $this->config->item('status');
		foreach ($data as $key => $value){
			$json[] = ['id'=>$key, 'text'=>$value];
		}
		echo json_encode($json);
	}
	public function get_select2_drcr(){
		$json = [];
		$data = $this->config->item('drcr');
		foreach ($data as $key => $value){
			$json[] = ['id'=>$key, 'text'=>$value];
		}
		echo json_encode($json);
	}
}
?>
