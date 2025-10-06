$(document).ready(function(){
    /************** PURCHASE SUMMARY ***************/
        $("#pm_entry_no").select2(select2_default({
            url:`purchase/get_select2_entry_no`,
            placeholder:'ENTRY NO',
        })).on('change', () => trigger_search());
        $("#pm_bill_no").select2(select2_default({
            url:`purchase/get_select2_bill_no`,
            placeholder:'BILL NO',
        })).on('change', () => trigger_search());
        $("#pm_acc_id").select2(select2_default({
            url:`purchase/get_select2_acc_id`,
            placeholder:'SUPPLIER',
        })).on('change', () => trigger_search());
    /************** PURCHASE SUMMARY ***************/
    /************** PURCHASE REURN SUMMARY *********/
        $("#prm_entry_no").select2(select2_default({
            url:`purchase_return/get_select2_entry_no`,
            placeholder:'ENTRY NO',
        })).on('change', () => trigger_search());
        $("#prm_acc_id").select2(select2_default({
            url:`purchase_return/get_select2_acc_id`,
            placeholder:'SUPPLIER',
        })).on('change', () => trigger_search());
    /************** PURCHASE REURN SUMMARY *********/
    /************** SALES SUMMARY ******************/
        $("#sm_bill_no").select2(select2_default({
            url:`sales/get_select2_bill_no`,
            placeholder:'BILL NO',
        })).on('change', () => trigger_search());
        $("#sm_acc_id").select2(select2_default({
            url:`sales/get_select2_acc_id`,
            placeholder:'SUPPLIER',
        })).on('change', () => trigger_search());
        $("#sm_user_id").select2(select2_default({
            url:`sales/get_select2_user_id`,
            placeholder:'SALES PERSON',
        })).on('change', () => trigger_search());
    /************** SALES SUMMARY ******************/
    /************** SALES RETURN SUMMARY ***********/
        $("#srm_entry_no").select2(select2_default({
            url:`sales_return/get_select2_entry_no`,
            placeholder:'ENTRY NO',
        })).on('change', () => trigger_search());
        $("#srm_acc_id").select2(select2_default({
            url:`sales_return/get_select2_acc_id`,
            placeholder:'SUPPLIER',
        })).on('change', () => trigger_search());    
    /************** SALES RETURN SUMMARY ***********/


});