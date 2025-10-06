<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Invoice extends CI_Controller{
	protected $master;
	protected $trans;
	protected $session_expired;
	public function __construct(){
		parent::__construct();

		$this->master 			= 'invoice_master';
		$this->trans 			= 'invoice_trans';
		$this->session_expired  = ['status' => false, 'flag' => -1, 'data' => [], 'msg' => ''];

		$this->load->model('invoice/Invoicemdl', 'model');
		$this->load->library('pagination');
		$this->config->load('extra');
	}
	public function index(){	
		if(sessionExist()){
			if(isset($_GET['action'])){
				if($_GET['action'] == 'view'){
					$config 				= array();
					$config 				= $this->config->item('pagination');	
					$config['total_rows'] 	= $this->model->get_data(true);
					$config['base_url'] 	= base_url("invoice?search=true");

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
					$record['data']			= $this->model->get_data(false, $config['per_page'], $offset);
					// echo "<pre>"; print_r($record); exit;
					
					$this->load->view('pages/invoice/'.$this->master, $record);
				}else if($_GET['action'] == 'add'){
					// $record = $this->model->get_data_for_add();
					$this->load->view('pages/invoice/invoice_form');
				}else if($_GET['action'] == 'edit'){
					if(isset($_GET['id']) && !empty($_GET['id'])){
						$record = $this->model->get_data_for_edit($_GET['id']);
						// echo "<pre>"; print_r($record); exit;
						$this->load->view('pages/invoice/invoice_form', $record);	
					}else{
						$this->load->view('errors/error');
					}
				}else if($_GET['action'] == 'print'){
					if(isset($_GET['id']) && !empty($_GET['id'])){
						$record = $this->model->get_data_for_edit($_GET['id']);
						// echo "<pre>"; print_r($record); exit;
						
						$this->load->view('pdfs/invoice/invoice', $record);
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
	public function get_sales_data($from_date, $to_date){
		if(!sessionExist()){
			echo json_encode($this->session_expired);
			return;
		}
		$data = $this->model->get_sales_data($from_date, $to_date);
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data, 'msg' => 'Data fetched successfully.']);
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
		$master_data['im_from_bill_date'] 	= date('Y-m-d',strtotime($post_data['im_from_bill_date']));				
		$master_data['im_to_bill_date'] 	= date('Y-m-d',strtotime($post_data['im_to_bill_date']));				
		$master_data['im_sm_bill_count'] 	= $post_data['im_sm_bill_count'];
		$master_data['im_sm_bill_amt'] 		= $post_data['im_sm_bill_amt'];
		$master_data['im_total_qty'] 		= $post_data['im_total_qty'];
		$master_data['im_final_amt']		= $post_data['im_final_amt'];
		$master_data['im_notes']			= $post_data['im_notes'];
		$master_data['im_updated_by'] 		= $_SESSION['user_id'];
		
		if($id == 0){
			$this->db->trans_begin();
			$master_data['im_entry_no'] 		= $this->db_operations->get_fin_year_branch_max_id($this->master, 'im_entry_no', 'im_fin_year', $_SESSION['fin_year'], 'im_branch_id', $_SESSION['user_branch_id']);
			$master_data['im_entry_date'] 		= date('Y-m-d');				
			$master_data['im_created_by'] 		= $_SESSION['user_id'];
			$master_data['im_created_at'] 		= date('Y-m-d H:i:s');
			$master_data['im_fin_year'] 		= $_SESSION['fin_year'];
			$master_data['im_branch_id'] 		= $_SESSION['user_branch_id'];
			// echo "<pre>"; print_r($post_data); exit;

			$id  = $this->db_operations->data_insert($this->master, $master_data);
			$msg = 'Added successfully';
			if($id < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Master data not inserted']);
				return;
			}

			if($this->insert_update_trans($post_data, $id) < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Transaction data not inserted']);
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
			if($this->db_operations->data_update($this->master, $master_data, 'im_id', $id) < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Master data not inserted']);
				return;
			}

			if($this->insert_update_trans($post_data, $id) < 1){
				$this->db->trans_rollback();
				echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Transaction data not inserted']);
				return;
			}
			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback();
			    echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Data not updated']);
				return;
		    }
		    $this->db->trans_commit();
		}
		$data['id'] = $id;
		echo json_encode(['status' => true, 'flag' => 1, 'data' => $data,  'msg' => $msg]);
	}
	public function insert_update_trans($post_data, $id){
		$trans_db_data = $this->db_operations->get_record($this->trans, ['it_im_id' => $id]);
		if(!empty($trans_db_data)){
			foreach ($trans_db_data as $key => $value){
				if(!in_array($value['it_id'], $post_data['it_id'])){
					if($this->db_operations->delete_record($this->trans,array('it_id' =>$value['it_id'])) < 1){
						return 0;
					}
				} 
			}
		}
		foreach ($post_data['it_id'] as $key => $value){
			if($post_data['it_generate'][$key] == 1){			
				$trans_data['it_im_id']			= $id;
				$trans_data['it_sm_id'] 		= $post_data['it_sm_id'][$key];
				$trans_data['it_bill_no'] 		= $post_data['it_bill_no'][$key];
				$trans_data['it_bill_date'] 	= date('Y-m-d', strtotime($post_data['it_bill_date'][$key]));
				$trans_data['it_acc_id'] 		= $post_data['it_acc_id'][$key];
				$trans_data['it_user_id'] 		= $post_data['it_user_id'][$key];
				$trans_data['it_payment_mode']	= $post_data['it_payment_mode'][$key];
				$trans_data['it_total_qty']		= $post_data['it_total_qty'][$key];
				$trans_data['it_sub_amt']		= $post_data['it_sub_amt'][$key];
				$trans_data['it_disc_amt']		= $post_data['it_disc_amt'][$key];
				$trans_data['it_promo_amt']		= $post_data['it_promo_amt'][$key];
				$trans_data['it_point_amt']		= $post_data['it_point_amt'][$key];
				$trans_data['it_round_off']		= $post_data['it_round_off'][$key];
				$trans_data['it_final_amt']		= $post_data['it_final_amt'][$key];
				$trans_data['it_fin_year']		= $_SESSION['fin_year'];
				$trans_data['it_branch_id']		= $_SESSION['user_branch_id'];
				if($value == 0){
					$trans_data['it_invoice_no'] 	= $this->db_operations->get_fin_year_branch_max_id($this->trans, 'it_invoice_no', 'it_fin_year', $_SESSION['fin_year'], 'it_branch_id', $_SESSION['user_branch_id']);
					$st_id = $this->db_operations->data_insert($this->trans, $trans_data);
					if($st_id < 1){
						return 0;
					}
				}else{
					if($this->db_operations->data_update($this->trans, $trans_data, 'it_id', $value) < 1){
						return 0;
					}
				}
			}else{
				if($value != 0){
					if($this->db_operations->delete_record($this->trans,array('it_id' =>$value)) < 1){
						return 0;
					}
				}
			}
		}
		return 1;
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
		$data = $this->model->get_record(['im_id' => $id], false);
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
		if($this->db_operations->delete_record($this->trans, ['it_im_id' => $id]) < 1){
			$this->db->trans_rollback();
			echo json_encode(['status' => true, 'flag' => 0, 'data' => [], 'msg' => 'Transaction not deleted']);
			return;
		}
		if($this->db_operations->delete_record($this->master, ['im_id' => $id]) < 1){
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
	public function get_select2_entry_no(){
		$json = [];
		$data = $this->model->get_select2_entry_no();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
}
?>
