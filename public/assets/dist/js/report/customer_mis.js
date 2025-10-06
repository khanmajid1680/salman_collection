$(document).ready(function(){
    $("#acc_id").select2(select2_default({
        url:`sales/get_select2_acc_id`,
        placeholder:'CUSTOMER',
    })).on('change', () => trigger_search());
});