$(document).ready(function(){
    $("#user_id").select2(select2_default({
        url:`sales/get_select2_user_id`,
        placeholder:'SALES PERSON',
    })).on('change', () => trigger_search());
    $("#from_date").on('change', () => trigger_search());
    $("#to_date").on('change', () => trigger_search());
});