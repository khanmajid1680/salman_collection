$(document).ready(function(){
    /************** sales SUMMARY ***************/
        $("#sm_entry_no").select2(select2_default({
            url:`sales/get_select2_entry_no`,
            placeholder:'ENTRY NO',
        })).on('change', () => trigger_search());
        $("#sm_bill_no").select2(select2_default({
            url:`sales/get_select2_bill_no`,
            placeholder:'BILL NO',
        })).on('change', () => trigger_search());
        $("#sm_acc_id").select2(select2_default({
            url:`sales/get_select2_acc_id`,
            placeholder:'CUSTOMER',
        })).on('change', () => trigger_search());

        $("#_design_name").select2(select2_default({
            url:`master/design/get_select2/_name`,
            placeholder:'DESIGN',
        })).on('change', () => trigger_search());

        $("#_style_name").select2(select2_default({
            url:`master/style/get_select2/_name`,
            placeholder:'STYLE',
        })).on('change', () => trigger_search());


        $("#from_entry_date").on('change', () => trigger_search());
        $("#to_entry_date").on('change', () => trigger_search());
    /************** sales SUMMARY ***************/

});