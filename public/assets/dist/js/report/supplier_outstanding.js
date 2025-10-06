$(document).ready(function(){
    $("#acc_id").select2(select2_default({
        url:`master/account/get_select2_supplier`,
        placeholder:'SUPPLIER',
    })).on('change', () => trigger_search());
});