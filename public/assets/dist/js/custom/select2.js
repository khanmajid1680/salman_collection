$(document).on('focus', '.select2.select2-container', function (e) {
  var isOriginalEvent = e.originalEvent // don't re-open on closing focus event
  var isSingleSelect = $(this).find(".select2-selection--single").length > 0 // multi-select will pass focus to input

  if (isOriginalEvent && isSingleSelect) {
    $(this).siblings('select:enabled').select2('open');
  }
});
const select2_default = options => {
    let url                     = options.url ? options.url : '';
    let placeholder             = options.placeholder ? options.placeholder : '';
    let width                   = options.width ? options.width : '100%';
    let maximumSelectionLength  = options.maximumSelectionLength ? options.maximumSelectionLength : 1;
    let maximumInputLength      = options.maximumInputLength ? options.maximumInputLength : 15;
    let minimumInputLength      = options.minimumInputLength ? options.minimumInputLength : 0;
    let minimumResultsForSearch = options.minimumResultsForSearch ? options.minimumResultsForSearch : 10;
    let multiple                = options.multiple ? options.multiple : false;
    let selectOnClose           = options.selectOnClose ? options.selectOnClose : false;
    let closeOnSelect           = options.closeOnSelect ? options.closeOnSelect : true;
    let allowClear              = options.allowClear ? options.allowClear : true;
    let param                   = options.param ? options.param : '';
    let param1                  = options.param1 ? options.param1 : 0;
    let param2                  = options.param2 ? options.param2 : 0;
    let barcode                 = options.barcode ? options.barcode : '';
    return {
        multiple,
        selectOnClose,
        closeOnSelect,
        allowClear,
        // maximumSelectionLength,
        maximumInputLength,
        minimumInputLength,
        // minimumResultsForSearch,
        placeholder,
        width,
        ajax:{
            url: `${base_url}/${url}`,
            dataType: 'json',
            delay: 400,
            data: params => {
                return {
                    name: params.term, // search term
                    param,
                    param1,
                    param2,
                };
            },
            processResults: (data, params)=>{
                if(barcode.length != 0){
                    const {term} = params;
                    if(data && data.length != 0){
                        if(term.length == 12){
                            const matched = data.find(d=>{
                                if(d.text == term){
                                    return d;
                                }
                            })
                            if(matched){
                                $(`#${barcode}`).append($("<option />")
                                    .attr("value", matched.id)
                                    .html(matched.text)
                                ).val(matched.id).trigger("change").select2("close");
                            }
                        }
                    }
                }
                return { results: data};
            },
            cache: true
        }
    }
}