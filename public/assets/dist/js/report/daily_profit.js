$(document).ready(function(){
    $("#from_date").on('change', () => trigger_search());
    $("#to_date").on('change', () => trigger_search());

    $("#_style_name").select2(select2_default({
        url:`master/style/get_select2/_name`,
        placeholder:'STYLE',
    })).on('change', () => trigger_search());
}); 