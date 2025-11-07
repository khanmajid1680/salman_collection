<?php
    defined('BASEPATH') or exit('No direct script access allowed');
    use Restserver\Libraries\REST_Controller;
    require_once APPPATH . 'core/MY_Controller.php';
    class sales_person extends my_controller{ 
        protected $name;
        public function __construct(){
            $this->name = 'user';
            parent::__construct([
                'model' => 'master/sales_person_model',
                'table' => 'user_master',
                'label' => 'sales_person',
            ]);
        }

        public function store_master($id){ 
            $result = $this->validate_master($id);
            if(!isset($result['status']) || (isset($result['status']) && $result['status'] === FALSE)) return $result;
            // print_r($result);die;
            $form_data = $result['data'];
            if($id==0){
                $form_data['user_role'] ='SALES';
                $form_data['user_branch_id'] =$this->user['branch_id'];
                $id = $this->db_operations->data_insert($this->table, $form_data);
                if($id < 1){ 
                    return ['message' => ucfirst($this->label).' not added.'];
                }
                $message = ucfirst($this->name).' added successfully';  
            }else{
                $prev_data = $this->db_operations->get_record($this->table, [$this->name.'_id' => $id]);
                if(empty($prev_data)){
                    return['status' => FALSE, 'message' => ucfirst($this->label).' not found.'];
                }

                if($this->db_operations->data_update($this->table, $form_data, $this->name.'_id', $id) < 1){
                    $this->db->trans_rollback();
                    return ['message' => ucfirst($this->name).' not updated.'];
                }
                $message = ucfirst($this->name).' updated successfully';   
            }


            $data[$this->label.'_id']= $id;
            $data[$this->label.'_name']= $this->post_data[$this->label.'_name'];
            return ['status' => TRUE,'data'=>$data,'message'=>$message];
        }
        protected function validate_master($id){
            $data = [];
            if(!isset($this->post_data[$this->label.'_name']) || (empty($this->post_data[$this->label.'_name']))) return ['message' => ucfirst($this->label).' Required']; 

            $data[$this->name.'_status']    = 1;
            $data[$this->name.'_fullname']  = trim($this->post_data[$this->label.'_name']);
            $check = $this->model->check_duplicate($id,$data[$this->name.'_fullname']);
            if(!empty($check)) return ['message' => ucfirst($this->label).' already exist.']; 

            return ['status' => TRUE, 'data' => $data];
        }

        public function remove_master($id){ 
            $prev_data = $this->db_operations->get_record($this->name.'_master', [$this->name.'_id' => $id]);
            if(empty($prev_data)){
                return['status' => FALSE, 'message' => ucfirst($this->label).' not found.'];
            }
            if($this->model->isExist($id)) return ['message' => 'Not allowed to delete.'];
            if($this->db_operations->delete_record($this->name.'_master', [$this->name.'_id' => $id]) < 1)return ['msg' => ucfirst($this->name).' not deleted.'];

            return ['status' => TRUE,'data'=>[],'message'=>ucfirst($this->label).' deleted successfully'];
        }
        
    }
?>