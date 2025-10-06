$(document).ready(function(){
    /************** PURCHASE REURN SUMMARY *********/
        $("#prm_entry_no").select2(select2_default({
            url:`purchase_return/get_select2_entry_no`,
            placeholder:'ENTRY NO',
        })).on('change', () => trigger_search());
        $("#prm_acc_id").select2(select2_default({
            url:`purchase_return/get_select2_acc_id`,
            placeholder:'SUPPLIER',
        })).on('change', () => trigger_search());
        $("#from_entry_date").on('change', () => trigger_search());
        $("#to_entry_date").on('change', () => trigger_search());

    /************** PURCHASE REURN SUMMARY *********/
});