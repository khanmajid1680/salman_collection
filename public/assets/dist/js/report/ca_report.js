$(document).ready(function(){
    /************** SALES SUMMARY ******************/
        $("#sm_bill_no").select2(select2_default({
            url:`sales/get_select2_bill_no`,
            placeholder:'BILL NO',
        })).on('change', () => trigger_search());
        $("#from_bill_date").on('change', () => trigger_search());
        $("#to_bill_date").on('change', () => trigger_search());
    /************** SALES SUMMARY ******************/
});