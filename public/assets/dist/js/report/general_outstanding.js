$(document).ready(function(){
    $("#acc_id").select2(select2_default({
        url:`master/account/get_account_select2`,
        placeholder:'GENERAL',
        param:'GENERAL',
    })).on('change', () => trigger_search());
});