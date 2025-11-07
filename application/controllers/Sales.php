<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Sales extends CI_Controller{
	protected $master;
	protected $trans;
	protected $session_expired;
	protected $menu;
    protected $sub_menu; 
	public function __construct(){
		$this->menu     = 'sales'; 
        $this->sub_menu = 'sales'; 
		parent::__construct();
		$this->master 			= 'sales_master';
		$this->trans 			= 'sales_trans';
		$this->session_expired  = ['status' => false, 'flag' => -1, 'data' => [], 'msg' => ''];

		$this->load->model('sales/Salesmdl', 'model');
		$this->load->model('purchase/Purchasemdl');
		$this->load->model('master/Loyaltymdl');
		$this->load->model('master/Accountmdl');
		$this->load->library('pagination');
		$this->config->load('extra');
	}
	public function index(){ 	 
		if(sessionExist()){
			if(isset($_GET['action'])){
				if($_GET['action'] == 'view'){
					$config 				= array();
					$config 				= $this->config->item('pagination');	
					$config['total_rows'] 	= $this->model->get_data(true,$this->menu);
					$config['base_url'] 	= base_url("sales?search=true");

					foreach ($_GET as $key => $value) 
					{
						if($key != 'search' && $key != 'offset')
						{
							$config['base_url'] .= "&" . $key . "=" .$value;
						}
					}

					$offset = (!empty($_GET['offset'])) ? $_GET['offset'] : 0;
					$this->pagination->initialize($config);
					
					$record['menu']		    = $this->menu;
                    $record['sub_menu']		= $this->sub_menu;

					$record['count']		= $offset;
					$record['total_rows'] 	= $config['total_rows'];
					$record['data']			= $this->model->get_data(false,$this->menu, $config['per_page'], $offset);
					// echo "<pre>"; print_r($record); exit;
					
					$this->load->view('pages/sales/'.$this->master, $record);
				}else if($_GET['action'] == 'add'){
					$record = $this->model->get_data_for_add();
					$record['menu']		    = $this->menu;
                    $record['sub_menu']		= $this->sub_menu;
					$this->load->view('pages/sales/sales_form', $record);
				}else if($_GET['action'] == 'edit'){
					if(isset($_GET['id']) && !empty($_GET['id'])){
						$record = $this->model->get_data_for_edit($_GET['id']);
						$record['menu']		    = $this->menu;
                    	$record['sub_menu']		= $this->sub_menu;
						$this->load->view('pages/sales/sales_form', $record);	
					}else{
						$this->load->view('errors/error');
					}
				}else if($_GET['action'] == 'print'){  
					if(isset($_GET['id']) && !empty($_GET['id'])){
						$record = $this->model->get_data_for_print($_GET['id']);
						$this->load->view('pdfs/sale_print_mini', $record);
					}else{
						$this->load->view('errors/error');
					}
				}else if($_GET['action'] == 'print2'){  
					if(isset($_GET['id']) && !empty($_GET['id'])){
						$record = $this->model->get_data_for_print($_GET['id']);
						// echo "<pre>"; print_r($record);die;
						$this->load->view('pdfs/sale_print_large', $record);
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

	public function print_api_pdf($id){ 
		$record = $this->model->get_data_for_print($id);
		$this->load->view('pdfs/sale_print_mini', $record);
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
		$master_data['sm_bill_no']			= $post_data['sm_bill_no'];
		$master_data['sm_bill_date'] 		= date('Y-m-d',strtotime($post_data['sm_bill_date']));				
		$master_data['sm_with_gst']    		= isset($post_data['sm_with_gst']) ? 1 : 0;
		$master_data['sm_bill_type']   		= isset($post_data['sm_bill_type']);

		$master_data['sm_acc_id'] 			=  $post_data['sm_acc_id'];
		$master_data['sm_shipping_acc_id'] 			=  isset($post_data['sm_shipping_acc_id'])?$post_data['sm_shipping_acc_id']:0;
		$master_data['sm_transport_id'] 			=  isset($post_data['sm_transport_id'])?$post_data['sm_transport_id']:0;

		$master_data['sm_user_id']			= $post_data['sm_user_id'];				

		$master_data['sm_total_qty'] 		= $post_data['sm_total_qty'];
		$master_data['sm_sub_total'] 		= $post_data['sm_sub_total'];

		$master_data['sm_hidden_disc_amt']	= $post_data['sm_hidden_disc_amt'];
		$master_data['sm_disc_per']			= $post_data['sm_disc_per'];	
		$master_data['sm_total_disc'] 		= $post_data['sm_total_disc'];
		
		$master_data['sm_taxable_amt'] 		= $post_data['sm_taxable_amt'];
		$master_data['sm_sgst_amt'] 		= $post_data['sm_sgst_amt'];
		$master_data['sm_cgst_amt'] 		= $post_data['sm_cgst_amt'];
		$master_data['sm_igst_amt'] 		= $post_data['sm_igst_amt'];
		$master_data['sm_round_off']		= $post_data['sm_round_off'];
		$master_data['sm_final_amt']		= $post_data['sm_final_amt'];
		$master_data['sm_collected_amt']	= $post_data['sm_collected_amt'];
		$master_data['sm_balance_amt']		= $post_data['sm_balance_amt'];		
		$master_data['sm_notes'] 			= trim($post_data['sm_notes']);
		$master_data['sm_allocated_amt']	= $master_data['sm_collected_amt'];

		$master_data['sm_sales_type'] 	   = trim($post_data['sm_sales_type']);

		$master_data['sm_updated_by'] 		= $_SESSION['user_id'];
		$this->db->trans_begin();

		
		if($id == 0){
			$master_data['sm_bill_no'] = $this->db_operations->get_order_fin_year_branch_max_id($this->master, 'sm_bill_no', 'sm_fin_year', $_SESSION['fin_year'], 'sm_branch_id', $_SESSION['user_branch_id'], 'sm_with_gst', $master_data['sm_with_gst'], 'sm_sales_type', $master_data['sm_sales_type']);
			
			$master_data['sm_created_by'] 		= $_SESSION['user_id'];
			$master_data['sm_created_at'] 		= date('Y-m-d H:i:s');
			$master_data['sm_fin_year'] 		= $_SESSION['fin_year'];
			$master_data['sm_branch_id'] 		= $_SESSION['user_branch_id'];
			// echo "<pre>"; print_r($master_data);die;
			$id  = $this->db_operations->data_insert($this->master, $master_data);
			$msg = 'Added successfully';
			if($id < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Master data not inserted']);
				return;
			}
			
		}else{
			$prev_data = $this->model->get_record(['sm_id' => $id], false);
			if(empty($prev_data)){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Record not found.']);
				return;
			}
		
			$msg = 'Updated successfully';
			if($this->db_operations->data_update($this->master, $master_data, 'sm_id', $id) < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Master data not inserted']);
				return;
			}

		}

			if($this->insert_update_trans($post_data, $id) < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Transaction data not inserted']);
				return;
			}

			$result = $this->add_update_payment_mode($post_data, $id);
			if(!$result['status']){
				$this->db->trans_rollback();
				echo json_encode(['session' => TRUE, 'status' => FALSE, 'data' => [], 'msg' => $result['msg']]);
				return;
			}
			
			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			    echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not inserted']);
				return;
		    }

		    $this->db->trans_commit();

		$data['id'] = $id;
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data,  'msg' => $msg]);
	}
	public function insert_update_trans($post_data, $id){ 
		$trans_db_data = $this->db_operations->get_record($this->trans, ['st_sm_id' => $id]);
		if(!empty($trans_db_data)){
			foreach ($trans_db_data as $key => $value){
				if(!in_array($value['st_id'], $post_data['st_id'])){
					if($this->db_operations->delete_record($this->trans,array('st_id' =>$value['st_id'])) < 1){
						return 0;
					}
				} 
			}
		}
		foreach ($post_data['st_id'] as $key => $value){ 
			$trans_data['st_sm_id']			= $id;
			$trans_data['st_bm_id'] 		= $post_data['st_bm_id'][$key];
			$trans_data['st_style_id'] 		= $post_data['st_style_id'][$key];
			$trans_data['st_brand_id'] 		= $post_data['st_brand_id'][$key];
			$trans_data['st_qty'] 			= $post_data['st_qty'][$key];
			$trans_data['st_rate'] 			= $post_data['st_rate'][$key];
			$trans_data['st_sub_total']		= $post_data['st_sub_total'][$key];
			
			$trans_data['st_disc_per']		= $post_data['st_disc_per'][$key];
			$trans_data['st_disc_amt']		= $post_data['st_disc_amt'][$key];

			$trans_data['st_taxable_amt']	= $post_data['st_taxable_amt'][$key];
			
			$trans_data['st_cgst_per']	= $post_data['st_cgst_per'][$key];
			$trans_data['st_sgst_per']	= $post_data['st_sgst_per'][$key];
			$trans_data['st_igst_per']	= $post_data['st_igst_per'][$key];

			$trans_data['st_cgst_amt']	= $post_data['st_cgst_amt'][$key];
			$trans_data['st_sgst_amt']	= $post_data['st_sgst_amt'][$key];
			$trans_data['st_igst_amt']	= $post_data['st_igst_amt'][$key];
			$trans_data['st_sub_total_amt']	= $post_data['st_sub_total_amt'][$key];
			
			$trans_data['st_trial']			= isset($post_data['st_trial'][$key]);
			$trans_data['st_dispatch_date']	= $post_data['st_dispatch_date'][$key];

			$trans_data['st_pt_rate']= $this->Purchasemdl->get_purchase_rate($post_data['st_bm_id'][$key]);
			if($value == 0){
				$st_id = $this->db_operations->data_insert($this->trans, $trans_data);
				if($st_id < 1){
					return 0;
				}
			}else{
				if($this->db_operations->data_update($this->trans, $trans_data, 'st_id', $value) < 1){
					return 0;
				}
			}
		}
		return 1;
	}

	public function add_update_payment_mode($post_data, $id){
		$trans_db_data = $this->db_operations->get_record('sales_payment_mode_trans', ['spmt_sm_id' => $id, 'spmt_delete_status' => false]);
		if(!empty($trans_db_data)){
			foreach ($trans_db_data as $key => $value){
				if(!in_array($value['spmt_id'], $post_data['spmt_id'])){
					$update_data 						= [];
					$update_data['spmt_delete_status'] 	= true;
					$update_data['spmt_updated_by'] 	= $_SESSION['user_id'];
					$update_data['spmt_updated_at'] 	= date('Y-m-d H:i:s');
					if($this->db_operations->data_update('sales_payment_mode_trans', $update_data, 'spmt_id', $value['spmt_id']) < 1){
						return ['status' => FALSE, 'data' => FALSE, 'msg' => '1. Payment mode not deleted.'];
					}
				}
			}
			foreach ($post_data['spmt_amt'] as $key => $value) {
				if($value <= 0){
					$update_data 						= [];
					$update_data['spmt_delete_status'] 	= true;
					$update_data['spmt_updated_by'] 	= $_SESSION['user_id'];
					$update_data['spmt_updated_at'] 	= date('Y-m-d H:i:s');
					if($this->db_operations->data_update('sales_payment_mode_trans', $update_data, 'spmt_id', $post_data['spmt_id'][$key]) < 1){
						return ['status' => FALSE, 'data' => FALSE, 'msg' => '1. Payment mode not deleted.'];
					}
				}
			}
		}
		foreach ($post_data['spmt_amt'] as $key => $value){
			if($value > 0){
				$trans_data							= [];
				$trans_data['spmt_sm_id']			= $id;
				$trans_data['spmt_sm_uuid']			= time().''.$_SESSION['user_id'];
				$trans_data['spmt_payment_mode_id']	= $post_data['spmt_payment_mode_id'][$key];
				$trans_data['spmt_amt']				= $post_data['spmt_amt'][$key];
				$trans_data['spmt_delete_status']	= false;
				$trans_data['spmt_updated_by'] 		= $_SESSION['user_id'];
				$trans_data['spmt_updated_at'] 		= date('Y-m-d H:i:s');
				
				if(empty($post_data['spmt_id'][$key])){
					$trans_data['spmt_created_by'] 	= $_SESSION['user_id'];
					$trans_data['spmt_created_at'] 	= date('Y-m-d H:i:s');
					if($this->db_operations->data_insert('sales_payment_mode_trans', $trans_data) < 1){
						return ['status' => FALSE, 'data' => FALSE, 'msg' => '1. Payment mode not added.'];
					}
				}else{
					if($this->db_operations->data_update('sales_payment_mode_trans', $trans_data, 'spmt_id', $post_data['spmt_id'][$key]) < 1){
						return ['status' => FALSE, 'data' => FALSE, 'msg' => '1. Payment mode not updated.'];
					}
				}
			}
		}
		return ['status' => TRUE, 'data' => TRUE, 'msg' => ''];
	}

	public function get_entry_no(){  
	    $post_data  = $this->input->post();
	    $id         = $post_data['id'];
	    $gst_type   = $post_data['sm_with_gst'];
	    $sales_type   = $post_data['sm_sales_type'];
	    $data['sm_bill_no']  = $this->model->get_entry_no($id,$gst_type,$sales_type);
		echo json_encode(['status' => true, 'data' => $data,  'msg' => 'Record fetched successfully.']);
	}
	public function get_data($id){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data = $this->model->get_record(['sm_id' => $id], false);
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
		$data = $this->model->get_record(['sm_id' => $id], false);
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
		if($this->db_operations->delete_record('loyalty_point_master', ['lpm_sm_id' => $id]) < 1){
			$this->db->trans_rollback();
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Transaction not deleted']);
			return;
		}
		if($this->db_operations->delete_record($this->trans, ['st_sm_id' => $id]) < 1){
			$this->db->trans_rollback();
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Transaction not deleted']);
			return;
		}
		
		$prev_data = $this->db_operations->get_record('sales_payment_mode_trans', ['spmt_sm_id' => $id, 'spmt_delete_status' => false]);
		if(!empty($prev_data)){
			$update_data 						= [];
			$update_data['spmt_delete_status'] 	= true; 
			$update_data['spmt_updated_by'] 	= $_SESSION['user_id']; 
			$update_data['spmt_updated_at'] 	= date('Y-m-d H:i:s'); 
			if($this->db_operations->data_update('sales_payment_mode_trans', $update_data, 'spmt_sm_id', $id) < 1){
				$this->db->trans_rollback();
				echo json_encode(['session' => TRUE, 'status' => FALSE, 'data' => [], 'msg' => '1. Payment not deleted.']);
				return;
			}
		}

		if($this->db_operations->delete_record($this->master, ['sm_id' => $id]) < 1){
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
	public function insert_update_loyalty_point($post_data, $sm_id){	
		$loyalty_points_data = $this->db_operations->get_record('loyalty_point_master', ['lpm_acc_id' => $post_data['sm_acc_id'], 'lpm_sm_id' => $sm_id]);
		
		// echo "<pre>";print_r($post_data); exit;
		$data_array = array();
		$data_array['lpm_acc_id'] 	= $post_data['sm_acc_id'];
		$data_array['lpm_sm_id'] 	= $sm_id;
		$data_array['lpm_point'] 	= floor($post_data['sm_final_amt']/100);
		$data_array['lpm_exp_date'] = date('Y-m-d', strtotime('+364 days', strtotime($post_data['sm_bill_date'])));

		if($data_array['lpm_point'] > 0){
			if(empty($loyalty_points_data)){ // insert new data
				$lpm_id = $this->db_operations->data_insert('loyalty_point_master', $data_array);
				if($lpm_id < 1) return 0;
				if($this->db_operations->data_update('sales_master', ['sm_lpm_id' => $lpm_id], 'sm_id', $sm_id) < 1) return 0;
			}else{ //update
				if($this->db_operations->data_update('loyalty_point_master', $data_array, 'lpm_id', $loyalty_points_data[0]['lpm_id']) < 1) return 0;
			}
		}
		if(isset($post_data['avail']) && $post_data['sm_point_used'] > 0){
			$used_loyalty_data = $this->Loyaltymdl->used_loyalty_data($post_data['sm_acc_id']);
			if(!empty($used_loyalty_data)){
				$used_point = 0;
				$remaining_form_point = $post_data['sm_point_used'];
				foreach ($used_loyalty_data as $key => $value){
					$remaining_form_point 	= $remaining_form_point - $used_point; // 200 - 180
					$lpm_point 				= $value['lpm_point']; // 17
					$lpm_point_used 		= $value['lpm_point_used']; // 0
					$remaining_point 		= $lpm_point - $lpm_point_used; // 17
					$used_point 			= $remaining_form_point <= $remaining_point ? $remaining_form_point : $remaining_point; // 17

					if($this->db_operations->data_update('loyalty_point_master', ['lpm_point_used' => $used_point], 'lpm_id', $value['lpm_id']) < 0) return 0;
				}
			}
		}
		return 1;
	}
	public function update_account_sales_date($post_data, $id, $remove = false){
		if($remove){
		}else{
			$account_data['account_date'] = date('Y-m-d', strtotime($post_data['sm_bill_date']));
			if($post_data['sm_disc_per'] >  $post_data['sm_promo_per']){
				$account_data['account_disc_per'] 	= $post_data['sm_disc_per'];
			}
			if($this->db_operations->data_update('account_master', $account_data, 'account_id', $post_data['sm_acc_id'] ) < 1){
				return 0;		
			}
		}
		return 1;
	}
	public function add_customer($mobile, $name){
		if(empty($mobile)) return 0;
		$post_data['account_type']			= 'CUSTOMER';
		$post_data['account_group_id']		= 5;
		$post_data['account_name'] 			= trim($name);
		$post_data['account_mobile'] 		= trim($mobile);
		$post_data['account_gst_type']		= 'WITHIN';
		$post_data['account_city_id']		= 1;
		$post_data['account_state_id']		= 1;
		$post_data['account_country_id']	= 1;
		$post_data['account_drcr']			= 'DR';
		$post_data['account_status']		= true;
		$post_data['account_updated_by'] 	= $_SESSION['user_id'];

		$data = $this->Accountmdl->get_record(['account_type' => $post_data['account_type'], 'account_mobile' => $post_data['account_mobile']]);
		if(!empty($data)){
			return $data[0]['account_id'];
		}
		$post_data['account_branch_id'] 	= $_SESSION['user_branch_id'];
		$post_data['account_created_by'] 	= $_SESSION['user_id'];
		$post_data['account_created_at'] 	= date('Y-m-d H:i:s');
		return $this->db_operations->data_insert('account_master', $post_data);
	}

	public function get_payment_mode_data($sm_id){
		$data = $this->model->get_payment_mode_data($sm_id);
		if(empty($data)) {
			echo json_encode(['session' => TRUE, 'status' => FALSE, 'data' => [], 'msg' => '1. Payment mode not define.']);
			return;
		}
		// echo "<pre>"; print_r($data);die;
		echo json_encode(['session' => TRUE, 'status' => TRUE, 'data' => $data, 'msg' => 'Data fetched successfully.']);
	}

	public function get_select2_bill_no(){
		$json = [];
		$data = $this->model->get_select2_bill_no();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
	public function get_select2_acc_id(){
		$json = [];
		$data = $this->model->get_select2_acc_id();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
	public function get_select2_user_id(){
		$json = [];
		$data = $this->model->get_select2_user_id();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
}
?>
