<?php
    defined('BASEPATH') or exit('No direct script access allowed');
    use Restserver\Libraries\REST_Controller;
    require_once APPPATH . 'core/MY_Controller.php';
    class order extends my_controller{
        public function __construct(){
            parent::__construct([
                'model' => 'Order_model',
                'table' => 'sales_master',
                'label' => 'sales',
            ]);
        }

        public function store_master($id){      
            $result = $this->validate_master($id);
            if(!isset($result['status']) || (isset($result['status']) && $result['status'] === FALSE)) return $result;
            if($id==0){
                $result['data']['sm_bill_no'] = $this->db_operations->get_order_fin_year_branch_max_id($this->table, 'sm_bill_no', 'sm_fin_year', $this->user['financial_year'], 'sm_branch_id', $this->user['branch_id'], 'sm_with_gst', $result['data']['sm_with_gst'], 'sm_sales_type', 0); 

                $result['data']['sm_fin_year']    = $this->user['financial_year'];
                $result['data']['sm_branch_id']    = $this->user['branch_id'];
                $result['data']['sm_created_by']   = $this->user['id'];
                $result['data']['sm_created_at']   = date('Y-m-d H:i:s'); 

                $id = $this->db_operations->data_insert($this->table, $result['data']);
                if($id < 1) return ['message' => 'Order not created. Try again.', 'code' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR];

                $this->post_data['sm_bill_no'] = $result['data']['sm_bill_no'];

            }else{
                $prev_data = $this->db_operations->get_record($this->label.'_master', ['sm_id' => $id]);
                if(empty($prev_data)) return ['message' => '1. Order not found.'];
                $this->post_data['sm_bill_no'] = $prev_data[0]['sm_bill_no'];
                
                $result=$this->db_operations->data_update($this->table, $result['data'],'sm_id',$id);
                if($result < 1) return ['message' => 'Order not updated. Try again.', 'code' => REST_Controller::HTTP_INTERNAL_SERVER_ERROR];
            }

            $result = $this->add_update_transaction($id);
            if(!isset($result['status']) || (isset($result['status']) && $result['status'] === FALSE)) return $result;

           $data['sm_id'] = $id;
           $message = ($id==0) ? 'Sales data Added Successfully' : 'Sales data Updated Successfully'; 
           return ['status' => TRUE,'data'=>$data,'message'=>$message];
        }
        protected function validate_master($id){  
            
            if(!isset($this->post_data['sm_bill_no'])) return ['message' => 'Bill No not defined.'];
            if(empty($this->post_data['sm_bill_no'])) return ['message' => 'Bill No is empty.'];

            if(!isset($this->post_data['sm_bill_date'])) return ['message' => 'Bill Date not defined.'];
            if(empty($this->post_data['sm_bill_date'])) return ['message' => 'Bill Date is empty.'];

            if(!isset($this->post_data['sm_acc_id'])) return ['message' => 'Customer Id not defined.'];
            if(empty($this->post_data['sm_acc_id'])) return ['message' => 'Customer Id is empty.'];

            if(!isset($this->post_data['sm_user_id'])) return ['message' => 'Sales Person Id not defined.'];
            if(empty($this->post_data['sm_user_id'])) return ['message' => 'Sales person Id is empty.'];
           
            if(!isset($this->post_data['sm_total_qty']))return ['message' => 'Total Qty not defined.'];
            if(empty($this->post_data['sm_total_qty']))return ['message' => 'Total Qty is empty.'];

            if(!isset($this->post_data['sm_final_amt'])) return ['message' => 'Total Amt not defined.'];
            if(empty($this->post_data['sm_final_amt'])) return ['message' => 'Total Amt is empty.'];

            // $uuid = trim($this->post_data['om_uuid']);
            // $unique = $this->db_operations->get_record($this->table, ['om_id !='=>$id,'om_uuid' => $uuid]);
            // if(!empty($unique)) return ['message' => 'Form already submitted.'];

            $sm_acc_id = trim($this->post_data['sm_acc_id']); 
            $sm_user_id = trim($this->post_data['sm_user_id']);

            $sm_transport_id  = isset($this->post_data['sm_transport_id'])?$this->post_data['sm_transport_id']: 0;

           
            $data = [
                'sm_bill_date'         => date('Y-m-d',strtotime($this->post_data['sm_bill_date'])),
                'sm_acc_id'             => $sm_acc_id,
                'sm_user_id'            => $sm_user_id,
                'sm_transport_id'        => $sm_transport_id,
                'sm_bill_type'          => $this->post_data['sm_bill_type'],
                'sm_gst_type'           => $this->post_data['sm_gst_type'],
                'sm_with_gst'           => $this->post_data['sm_with_gst'],
                'sm_total_qty'          => $this->post_data['sm_total_qty'],
                'sm_sub_total'          => $this->post_data['sm_sub_total'],
                'sm_hidden_disc_amt'           => $this->post_data['sm_hidden_disc_amt'],
                'sm_disc_per'           => $this->post_data['sm_disc_per'],
                'sm_total_disc'         => $this->post_data['sm_total_disc'],
                'sm_taxable_amt'        => $this->post_data['sm_taxable_amt'],
                'sm_sgst_amt'           => $this->post_data['sm_sgst_amt'],
                'sm_cgst_amt'           => $this->post_data['sm_cgst_amt'],
                'sm_igst_amt'           => $this->post_data['sm_igst_amt'],
                'sm_round_off'          => $this->post_data['sm_round_off'],
                'sm_final_amt'          => $this->post_data['sm_final_amt'],
                'sm_notes'              => $this->post_data['sm_notes'],
                'sm_sales_type'         =>0,
                'sm_updated_by'         => $this->user['id'],
                'sm_updated_at'         => date('Y-m-d H:i:s'),
            ];
            
            return ['status' => TRUE, 'data' => $data];
        }  

        protected function add_update_transaction($id){ 
            $trans_db = $this->db_operations->get_record('sales_trans', ['st_sm_id' => $id]);
            $ids = $this->get_ids($this->post_data['trans_data'], 'st_id');
            if(!empty($trans_db)){
                foreach ($trans_db as $key => $value){  
                    if(!in_array($value['st_id'], $ids)){
                        if($this->db_operations->delete_record($this->label.'_trans',['st_id'=>$value['st_id']]) < 1) return ['message' => '1. Transaction not deleted.']; 
                    } 
                }
            }
            foreach ($this->post_data['trans_data'] as $key => $value){ 
                $result = $this->validate_item_transaction($value);
                if(!isset($result['status']) || (isset($result['status']) && $result['status'] === FALSE)) return $result;

                $trans_data                         = [];
                $trans_data['st_sm_id']             = $id;
                $trans_data['st_bm_id']             = $value['st_bm_id'];
                $trans_data['st_style_id']           = $value['st_style_id'];
                $trans_data['st_brand_id']          = $value['st_brand_id'];

                $trans_data['st_qty']               = $value['st_qty'];
                $trans_data['st_rate']              = $value['st_rate'];
                $trans_data['st_sub_total']         = $value['st_sub_total'];
           
                $trans_data['st_disc_per']          = $value['st_disc_per'];
                $trans_data['st_disc_amt']          = $value['st_disc_amt'];
                $trans_data['st_taxable_amt']       = $value['st_taxable_amt'];
                $trans_data['st_sgst_per']          = $value['st_sgst_per'];
                $trans_data['st_sgst_amt']          = $value['st_sgst_amt'];
                $trans_data['st_cgst_per']          = $value['st_cgst_per'];
                $trans_data['st_cgst_amt']          = $value['st_cgst_amt'];
                $trans_data['st_igst_per']          = $value['st_igst_per'];
                $trans_data['st_igst_amt']          = $value['st_igst_amt'];
                $trans_data['st_sub_total_amt']     = $value['st_sub_total_amt'];

                $trans_data['st_trial']         = (isset($value['st_trial']) && !empty($value['st_trial'])) ? 1 : 0;
                $trans_data['st_dispatch_date'] = (empty($value['st_dispatch_date'])) ? '' : date('Y-m-d',strtotime($value['st_dispatch_date']));

                $trans_data['st_pt_rate']= $this->model->get_purchase_rate($trans_data['st_bm_id']);

                if(empty($value['st_id'])){ 
                    if($this->db_operations->data_insert($this->label.'_trans', $trans_data) < 1) return ['message' => '1. Transaction not Inserted.'];
                }else{
                    $prev_data = $this->db_operations->get_record($this->label.'_trans', ['st_id' => $value['st_id']]);
                    if(empty($prev_data)) return ['message' => '1. Transaction not found.'];
                    if($this->db_operations->data_update($this->label.'_trans', $trans_data, 'st_id', $value['st_id']) < 1) return ['message' => '1. Transaction not updated.'];
                }
            }

            return ['status' => TRUE];
        }
       
        public function validate_item_transaction($trans){  

            if(!isset($trans['st_qty']) || (isset($trans['st_qty']) && empty($trans['st_qty']))){
                return ['message' => '1. Qty is required.']; 
            }else{
                if($trans['st_qty'] <= 0) {
                    return ['message' => '1. Invalid Qty.'];
                }else{
                    $qty = empty($trans['st_sm_id']) ? 0 : $trans['st_qty']; 
                    $data = $this->model->get_stock_barcode_data($trans['st_bm_id'], $qty);
                    if(empty($data)) return ['message' => 'Barcode Not Found'];
                    if(!empty($data) && ($trans['st_qty'] > $data[0]['bal_qty'])) return ['message' => '1. Qty should be less than available Qty.'];
                }
            }

            if(!isset($trans['st_rate']) || (isset($trans['st_rate']) && empty($trans['st_rate']))){
                return ['message' => '1. Rate is required.'];
            }else{
                if($trans['st_rate'] <= 0) return ['message' => '1. Invalid Rate.'];   
            }

            if(!isset($trans['st_sub_total']) || (isset($trans['st_sub_total']) && empty($trans['st_sub_total']))){
                return ['message' => '1. Amt is required.'];
            }else{
                if($trans['st_sub_total'] <= 0) return ['message' => '1. Invalid Amt.'];   
            }

            if(!isset($trans['st_sub_total_amt']) || (isset($trans['st_sub_total_amt']) && empty($trans['st_sub_total_amt']))){
                return ['message' => '1. Total Amt is required.'];
            }else{
                if($trans['st_sub_total_amt'] <= 0) return ['message' => '1. Invalid Total Amt.'];   
            }

            if(!isset($trans['st_bm_id'])) return ['message' => 'Barcode Id not define'];
            if(empty($trans['st_bm_id'])) return ['message' => 'Barcode Id is empty'];
       
            return ['status' => TRUE];
        }
  
        public function scan_barcode(){   
            $this->allow_method(['POST']);
            // print_r($this->post_data);die;
            if(!isset($this->post_data['barcode'])) return $this->response(['message' => 'barcode not defined.']);
            if(empty($this->post_data['barcode'])) return $this->response(['message' => 'barcode is empty.']);

            $data = $this->model->get_barcode_data($this->post_data['barcode']);
            
            if(empty($data)) return $this->response(['message' => 'Barcode Not found']);
            if($data[0]['bal_qty']<=0) return $this->response(['message' => '5. Barcode not available.']);
            return $this->response(['status' => TRUE,'data' => $data[0], 'message' => 'Barcode Scaned successfully..', 'code' => REST_Controller::HTTP_OK]);
        }

        public function max_bill_no(){  
            $this->allow_method(['POST']);
            $sm_id = (isset($this->post_data['sm_id']) && !empty($this->post_data['sm_id'])) ?$this->post_data['sm_id']:0;
            $with_gst = (isset($this->post_data['sm_with_gst']) && !empty($this->post_data['sm_with_gst'])) ? 1 :0;

            $bill_no  = $this->model->get_entry_no($sm_id,$with_gst);
            return $this->response(['status' => TRUE,'data' => $bill_no, 'message' => 'Bill No fetched successfully..', 'code' => REST_Controller::HTTP_OK]);
        }

        public function share_bill($id=0){       
            $this->allow_method(['GET']);

            if(!isset($id)) return $this->response(['message' => 'Sales Id not defined.']);
            if(empty($id)) return $this->response(['message' => 'Sales Id is empty.']);
          
            $om_data = $this->db_operations->get_record('sales_master',['sm_id'=>$id]);  
            if(empty($om_data)) return $this->response(['message' => 'Sales data Not found!!']); 
            
            $base_url = str_replace('api_order/','', base_url());  
            // $pdf = $base_url.'sales/print_api_pdf/'.$id;  
            redirect($base_url.'sales/print_api_pdf/'.$id);
           // return $this->response(['status' => true,'data' => $pdf, 'message' => 'Pdf Generated successfully', 'code' => REST_Controller::HTTP_OK]);
        }

       
        

}?>