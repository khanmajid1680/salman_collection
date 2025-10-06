$(document).ready(function(){
    $("#acc_id").select2(select2_default({
        url:`purchase/get_select2_acc_id`,
        placeholder:'SUPPLIER',
    })).on('change', () => trigger_search());
});