<?php
    defined('BASEPATH') or exit('No direct script access allowed');
    use Restserver\Libraries\REST_Controller;
    require_once APPPATH . 'core/MY_Controller.php';
    class transport extends my_controller{ 
        protected $name;
        public function __construct(){
            $this->name = 'transport'; 
            parent::__construct([
                'model' => 'master/transport_model',
                'table' => 'transport_master',
                'label' => 'transport',
            ]);
        }

        public function store_master($id){ 
            $result = $this->validate_master($id);
            if(!isset($result['status']) || (isset($result['status']) && $result['status'] === FALSE)) return $result;
            // print_r($result);die;
            $form_data = $result['data'];
            if($id==0){
                $id = $this->db_operations->data_insert($this->name.'_master', $form_data);
                if($id < 1){ 
                    return ['message' => ucfirst($this->name).' not added.'];
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

            $data[$this->name.'_id']= $id;
            $data[$this->name.'_name']= $this->post_data[$this->name.'_name'];
            return ['status' => TRUE,'data'=>$data,'message'=>$message];
        }
        protected function validate_master($id){
            $data = [];
            if(!isset($this->post_data[$this->name.'_name']) || (empty($this->post_data[$this->name.'_name']))) return ['message' => ucfirst($this->name).' Required']; 

            $data[$this->name.'_status'] = 1;
            $data[$this->name.'_name']      =trim($this->post_data[$this->name.'_name']);
            $check = $this->model->check_duplicate($id,$data[$this->name.'_name']);
            if(!empty($check)) return ['message' => ucfirst($this->name).' already exist.']; 

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
        
    }
?>