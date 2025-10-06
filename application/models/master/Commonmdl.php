<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Commonmdl extends CI_model{
		public function __construct(){
			parent::__construct();
		}
		public function get_role($role){
			$data 	= $this->config->item('role');
			if(empty($data)) return ['value' => '', 'text' => ''];
			foreach ($data as $key => $value) {
				if($role == $key){
					return ['value' => $key, 'text' => $value];
				}
			}
		}
		public function get_mode($mode){
			$data 	= $this->config->item('payment_mode');
			if(empty($data)) return ['value' => '', 'text' => ''];
			foreach ($data as $key => $value) {
				if($mode == $key){
					return ['value' => $key, 'text' => $value];
				}
			}
		}
		public function get_status($status){
			$data 	= $this->config->item('status');
			if(empty($data)) return ['value' => '', 'text' => ''];
			foreach ($data as $key => $value) {
				if($status == $key){
					return ['value' => $key, 'text' => $value];
				}
			}
		}
		public function get_drcr($status){
			$data 	= $this->config->item('drcr');
			if(empty($data)) return ['value' => '', 'text' => ''];
			foreach ($data as $key => $value) {
				if($status == $key){
					return ['value' => $key, 'text' => $value];
				}
			}
		}
	}
?>