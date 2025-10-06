$(document).ready(function(){
    $("#from_date").on('change', () => trigger_search());
    $("#to_date").on('change', () => trigger_search());
});