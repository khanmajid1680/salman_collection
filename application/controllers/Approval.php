<?php defined('BASEPATH') OR exit('No direct script access allowed');
class approval extends CI_Controller{
	protected $master;
	protected $trans;
	protected $session_expired;
	protected $menu;
    protected $sub_menu; 
	public function __construct(){
		$this->menu     = 'approval'; 
        $this->sub_menu = 'approval'; 
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

	public function add_edit_order($id){   
        $post_data  = $this->input->post();
        // $id         = $post_data['id'];
        // echo "<pre>"; print_r($post_data); exit;
        $prev_data = $this->model->get_approval_data($id); 
        if(empty($prev_data)){
        	echo json_encode(['status'=>true,  'flag' => 0, 'data' => [],'msg' => '1. Estimate not found.']);
        	return;	
        } 
        // if($prev_data[0]['sm_allocated_amt']>0){ 
        // 	echo json_encode(['status'=>true,  'flag' => 0, 'data' => [],'msg' => '1. Can not tranfer to order.']);
        // 	return;	
        // } 
        // master_data
           $master_data['sm_bill_no'] = $this->db_operations->get_order_fin_year_branch_max_id('sales_master', 'sm_bill_no', 'sm_fin_year', $_SESSION['fin_year'], 'sm_branch_id', $_SESSION['user_branch_id'], 'sm_with_gst', $prev_data[0]['sm_with_gst'], 'sm_sales_type',0); 

            $master_data['sm_bill_date'] 		= date('Y-m-d'); 
            $master_data['sm_sales_type']   	= 0; 
            $master_data['sm_updated_by'] 		= $_SESSION['user_id'];
			$master_data['sm_updated_at'] 		= date('Y-m-d H:i:s');
        	
	        $this->db->trans_begin();
	        $sm_id = $this->db_operations->data_update('sales_master', $master_data,['sm_id'=>$id]);
	        if($sm_id < 1){
	            $this->db->trans_rollback();
	            echo json_encode(['status'=>true,  'flag' => 0, 'data' => [],'msg' => '1. Sales not added.']);
	            return;
	        }
	        if ($this->db->trans_status() === FALSE){
	            $this->db->trans_rollback();
	            echo json_encode(['status'=>true,  'flag' => 0, 'data' => [],'msg' => '1. Transaction Rollback.']);
	            return;
	        }

        $this->db->trans_commit();
        echo json_encode(['status' => true, 'flag' => 1, 'data' => [],  'msg' => 'Estimate converted to Sales successfully.']);
    }

	
}
?>
