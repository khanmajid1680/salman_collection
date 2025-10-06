$(document).ready(function(){
    /************** SALES SUMMARY ******************/
        $("#sm_bill_no").select2(select2_default({
            url:`sales/get_select2_bill_no`,
            placeholder:'BILL NO',
        })).on('change', () => trigger_search());
        $("#sm_acc_id").select2(select2_default({
            url:`sales/get_select2_acc_id`,
            placeholder:'CUSTOMER',
        })).on('change', () => trigger_search());
        $("#sm_user_id").select2(select2_default({
            url:`sales/get_select2_user_id`,
            placeholder:'SALES PERSON',
        })).on('change', () => trigger_search());
        $("#sm_payment_mode").select2(select2_default({
            url:`master/common/get_select2_mode`,
            placeholder:'PAYMENT MODE',
        })).on('change', () => trigger_search());
        $("#from_bill_date").on('change', () => trigger_search());
        $("#to_bill_date").on('change', () => trigger_search());
    /************** SALES SUMMARY ******************/
});

const set_order_status = (id,isvalue)=>{ 
  if(id){
    let path= `report/trial_report/set_order_status/${id}/${isvalue}`;   
    ajaxCall('GET',path,'','JSON',resp =>{
        callToastify('success', 'Updated successfully', 'right');
         $("body, html").animate({ scrollTop: 0 }, 1000);
      setTimeout(()=>{$('[data-toggle="toggle"]').bootstrapToggle();},1000);
      
    },errmsg => {
    });
  }
  
}