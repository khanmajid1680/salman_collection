<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class order_model extends my_model{
    public function __construct(){ parent::__construct(); }
    public function isExist($id){
        return false;
    }

     public function isTransExist($id){ 
        return false;
    }

    public function get_entry_no($sm_id, $sm_with_gst){     
        if(!empty($sm_id)){
            $data = $this->db_operations->get_record('sales_master', ['sm_id' => $sm_id]);
            if(!empty($data)){ 
                // if($data[0]['sm_sales_type']==0){ 
                    if(!empty($data) && !empty($data[0]['sm_bill_no'])) return $data[0]['sm_bill_no'];
                // }
            }
        } 
        $query="SELECT sm_bill_no as max_no
                FROM sales_master
                WHERE sm_with_gst = $sm_with_gst
                AND sm_sales_type = 0
                AND sm_branch_id = '".$this->user['branch_id']."'
                AND sm_fin_year = '".$this->user['financial_year']."'
                ORDER BY sm_bill_no DESC
                LIMIT 1";

        // print_r($query); die;        
           $data = $this->db->query($query)->result_array();;
        return !empty($data) ? ($data[0]['max_no']+1) : 1;

    }

    public function get_purchase_rate($bm_id){ 
        $data = $this->db_operations->get_record('barcode_master', ['bm_id' => $bm_id]);
        if(empty($data)) return 0;
        return ($data[0]['bm_pt_rate'] - $data[0]['bm_pt_disc']);
    }
        
    public function read($search, $args){
        $where  = '';
        $having = '';
        
        if(isset($search['customer_name']) && !empty($search['customer_name']))
            $where .= " AND account.account_name LIKE '%".$search['customer_name']."%'";
        
        if(isset($search['customer_id']) && !empty($search['customer_id']))
            $where .= " AND account.account_id = '".$search['customer_id']."'";

        if(isset($search['bill_no']) && !empty($search['bill_no']))
            $where .= " AND sm.sm_bill_no LIKE '%".$search['bill_no']."%'";

        // if(isset($search['order_status']) && !empty($search['order_status']))
        //     $where .= " AND sm.sm_status = '".$search['order_status']."'";
        
        $query="SELECT 
                sm.sm_id,
                sm.sm_bill_no,
                sm.sm_bill_date, 
                sm.sm_acc_id,
                CONCAT(UPPER(acc.account_name), ' - ', acc.account_mobile) as account_name,
                CONCAT(UPPER(sh_acc.account_name), ' - ', sh_acc.account_mobile) as shipping_account_name,
                IFNULL(UPPER(user.user_fullname), '') as sales_person_name,
                sm.sm_total_qty,
                sm.sm_sub_total as sub_amt,
                sm.sm_total_disc as disc_amt,
                sm.sm_final_amt as total_amt,
                sm.sm_notes
                FROM sales_master sm
                LEFT JOIN account_master acc ON(acc.account_id = sm.sm_acc_id)
                LEFT JOIN account_master sh_acc ON(sh_acc.account_id = sm.sm_shipping_acc_id)
                LEFT JOIN user_master user ON(user.user_id = sm.sm_user_id)
                WHERE 1
                $where
                HAVING 1 $having
                ORDER BY sm.sm_id DESC";
        if (isset($args['wantCount']) && $args['wantCount'] == true) 
            return $this->db->query($query)->num_rows();

        if (isset($args['limit']) && !empty($args['limit']))
            $query .= " LIMIT ".(int) $args['limit'];

        if (isset($args['offset']) && !empty($args['offset']))
            $query .= " OFFSET ".(int) $args['offset'];
        return $this->db->query($query)->result_array();
    }


        public function get_transaction($sm_id){   
            $query="SELECT st.*,
                    IFNULL(UPPER(design.design_name), '') as design_name,
                    IFNULL(UPPER(style.style_name), '') as style_name,
                    IFNULL(UPPER(brand.brand_name), '') as brand_name,
                    IFNULL(UPPER(bm.bm_item_code),'') as barcode
                    FROM sales_trans st
                    INNER JOIN barcode_master bm ON(bm.bm_id = st.st_bm_id)
                    LEFT JOIN design_master design ON(design.design_id = bm.bm_design_id)
                    LEFT JOIN style_master style ON(style.style_id = st.st_style_id)
                    LEFT JOIN brand_master brand ON(brand.brand_id = st.st_brand_id)
                    WHERE st.st_sm_id = $sm_id
                    ORDER BY st.st_id DESC";
                $record = $this->db->query($query)->result_array();
                if(!empty($record)){
                    foreach ($record as $key => $value) {
                        $record[$key]['isExist'] = $this->isTransExist($value['st_id']);
                    }
                }
                
            return $record;
        }

        public function get_stock_barcode_data($bm_id,$qty){   
            $query ="
                        SELECT 
                        ((bm.bm_pt_qty - (bm.bm_st_qty + bm.bm_srt_qty + bm.bm_prt_qty)) + $qty) as bal_qty
                        FROM barcode_master bm
                        WHERE bm.bm_id = $bm_id
                        AND bm.bm_delete_status = 0 
                        AND bm.bm_branch_id = ".$this->user['branch_id']." ";
                return  $this->db->query($query)->result_array(); 

        }

         public function get_barcode_data($item_code){   
            $query ="
                        SELECT 
                        0 as st_id,
                        0 as st_sm_id,
                        bm.bm_id as st_bm_id,
                        bm.bm_item_code as barcode,
                        UPPER(design.design_name) as design_name,
                        bm.bm_style_id as st_style_id,
                        UPPER(style.style_name) as style_name,
                        0 as st_trial,
                        '' as st_dispatch_date,
                        bm.bm_brand_id as st_brand_id,
                        UPPER(brand.brand_name) as brand_name,
                        bm.bm_pt_qty as st_qty,
                        bm.bm_sp_amt as st_rate,
                        (bm.bm_pt_qty* bm.bm_sp_amt)as st_sub_total,
                        0 as st_disc_per,
                        0 as st_disc_amt,
                        0 as st_taxable_amt,
                        IF(design.design_sgst_per > 0, design.design_sgst_per, 2.5) AS st_sgst_per,
                        IF(design.design_cgst_per > 0, design.design_cgst_per, 2.5) AS st_cgst_per,
                        IF(design.design_igst_per > 0, design.design_igst_per, 5) AS st_igst_per,
                        0 as st_sgst_amt,
                        0 as st_cgst_amt,
                        0 as st_igst_amt,
                        0 as st_sub_total_amt,
                        FALSE as isExist,
                        ((bm.bm_pt_qty - (bm.bm_st_qty + bm.bm_srt_qty + bm.bm_prt_qty)) + 0) as bal_qty
                        FROM barcode_master bm
                        LEFT JOIN design_master design ON(design.design_id = bm.bm_design_id)
                        LEFT JOIN style_master style ON(style.style_id = bm.bm_style_id)
                        LEFT JOIN brand_master brand ON(brand.brand_id = bm.bm_brand_id)
                        WHERE bm.bm_item_code = '$item_code'
                        AND bm.bm_delete_status = 0 
                        AND bm.bm_branch_id = ".$this->user['branch_id']." ";
                return  $this->db->query($query)->result_array(); 

        }

    
   

}
?>
