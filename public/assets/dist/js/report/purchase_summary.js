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
        $("#from_entry_date").on('change', () => trigger_search());
        $("#to_entry_date").on('change', () => trigger_search());
    /************** PURCHASE SUMMARY ***************/

});