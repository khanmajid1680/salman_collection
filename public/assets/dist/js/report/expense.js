$(document).ready(function(){
	$("#_party_id").select2(select2_default({
        url:`report/expense/get_select2_party_id`,
        placeholder:'DESCRIPTION',
    })).on('change', () => trigger_search());
    $("#from_date").on('change', () => trigger_search());
    $("#to_date").on('change', () => trigger_search());
});