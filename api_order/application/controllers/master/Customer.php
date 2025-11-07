<?php defined('BASEPATH') or exit('No direct script access allowed');
    use Restserver\Libraries\REST_Controller;
    require_once APPPATH . 'core/MY_Controller.php';
    class customer extends my_controller{ 
        protected $name;
        public function __construct(){
            $this->name = 'account'; 
            parent::__construct([
                'model' => 'master/customer_model',
                'table' => 'customer_master',
                'label' => 'customer',
            ]);
        }

        public function store_master($id){ 
            $result = $this->validate_master($id);
            if(!isset($result['status']) || (isset($result['status']) && $result['status'] === FALSE)) return $result;
            // print_r($result);die;
            $form_data = $result['data'];
            $form_data[$this->name.'_updated_by']         = $this->user['id'];
            $form_data[$this->name.'_updated_at']         = date('Y-m-d H:i:s');
            if($id==0){
                $form_data[$this->name.'_created_by'] = $this->user['id'];
                $form_data[$this->name.'_created_at'] = date('Y-m-d H:i:s');
                $id = $this->db_operations->data_insert($this->name.'_master', $form_data);
                if($id < 1){ 
                    return ['message' => 'customer not added.'];
                }
                $message = ucfirst($this->name).' added successfully';  
            }else{
                $prev_data = $this->db_operations->get_record($this->name.'_master', [$this->name.'_id' => $id]);
                if(empty($prev_data)){
                    return['status' => FALSE, 'message' => ucfirst($this->name).' not found.'];
                }
                if($this->db_operations->data_update($this->name.'_master', $form_data, $this->name.'_id', $id) < 1){
                    $this->db->trans_rollback();
                    return ['message' => ucfirst($this->name).' not updated.'];
                }
                $message = ucfirst($this->name).' updated successfully';   
            }

            $data['account_id']= $id;
            $data['account_name']= $this->post_data['account_name'];
            $data['account_mobile']= $this->post_data['account_mobile'];

            return ['status' => TRUE,'data'=>$data,'message'=>$message];
        }

        protected function validate_master($id){ 
            $data = [];
            $this->post_data['account_type'] = 'CUSTOMER';
            if(!isset($this->post_data['account_name']) || (empty($this->post_data['account_name']))) return ['message' => 'customer Required']; 

            if(!isset($this->post_data['account_mobile']) || (empty($this->post_data['account_mobile']))) return ['message' => 'Mobile no Required'];

            if (strlen($this->post_data['account_mobile']) < 10)  return ['message' => 'Invalid Mobile Number Required'];

            if(!empty($this->post_data['account_mobile'])){
                $temp = $this->db_operations->get_record($this->name.'_master', ['account_id !=' => $id,'account_type' => $this->post_data['account_type'], 'account_mobile' => $this->post_data['account_mobile']]);
                if(!empty($temp)) return ['message' => 'Mobile no. already exist.'];    
            }

            $data['account_status'] = 1; 
            $post_data['account_gst_type']  = 'WITHIN';
            $data['account_name'] = $this->post_data['account_name'];
            $data['account_mobile'] = trim($this->post_data['account_mobile']);
            $data['account_address'] = trim($this->post_data['account_address']);
            $data['account_type']      = $this->post_data['account_type'];
            $data['account_drcr']      = 'DR';
            // $check = $this->model->check_duplicate($id,$data['account_mobile']);
            // if(!empty($check)) return ['message' => 'customer already exist.']; 

            return ['status' => TRUE, 'data' => $data];
        }

        public function remove_master($id){ 
            $prev_data = $this->db_operations->get_record($this->name.'_master', [$this->name.'_id' => $id]);
            if(empty($prev_data)){
                return['status' => FALSE, 'message' => ucfirst($this->name).' not found.'];
            }
            if($this->model->isExist($id)) return ['message' => 'Not allowed to delete.'];  
            
            if($this->db_operations->delete_record($this->name.'_master', [$this->name.'_id' => $id]) < 1)return ['msg' => ucfirst($this->name).' not deleted.'];

            return ['status' => TRUE,'data'=>[],'message'=>ucfirst($this->name).' deleted successfully'];
        }

        public function get_detail($id=0){  
            $this->allow_method(['GET']);
            if(!isset($id)) return $this->response(['message' => 'Customer Id not defined.']);
            if(empty($id)) return $this->response(['message' => 'Customer Id is empty.']);
            $data=$this->db_operations->get_record('account_master',['account_id'=>$id]); 
            if(empty($data)) return $this->response(['message' => 'customer Not Found ']);
            $data[0]['gst_type']=0;

            if($data[0]['account_state_id'] > 0){
                $data[0]['gst_type'] = ($data[0]['account_state_id']==1) ? 0 : 1;
            } 
          
            $record['account_id']      = $data[0]['account_id'];
            $record['account_name']    = strtoupper($data[0]['account_name']);
            $record['account_mobile']  = $data[0]['account_mobile'];
            $record['gst_type']         = $data[0]['gst_type'];
            return $this->response(['status' => TRUE,'data' => $record, 'message' => 'Data fetched successfully..', 'code' => REST_Controller::HTTP_OK]);
        }


}?>