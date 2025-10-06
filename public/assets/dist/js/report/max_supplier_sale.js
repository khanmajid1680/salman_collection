$(document).ready(function(){
    $("#bm_acc_id").select2(select2_default({
        url:`master/barcode/get_select2_acc_id`,
        placeholder:'SUPPLIER',
    })).on('change', () => trigger_search());
    $("#from_date").on('change', () => trigger_search());
    $("#to_date").on('change', () => trigger_search());
});