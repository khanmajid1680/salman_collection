
$(document).ready(function(){

        /************** PURCHASE SUMMARY ***************/
        $("#pm_entry_no").select2(select2_default({
            url:`purchase/get_select2_entry_no`,
            placeholder:'ENTRY NO',
        })).on('change', () => trigger_search());
        $("#pm_bill_no").select2(select2_default({
            url:`purchase/get_select2_bill_no`,
            placeholder:'BILL NO',
        })).on('change', () => trigger_search());
        $("#pm_acc_id").select2(select2_default({
            url:`purchase/get_select2_acc_id`,
            placeholder:'SUPPLIER',
        })).on('change', () => trigger_search());
    /************** PURCHASE SUMMARY ***************/
    /************** PURCHASE REURN SUMMARY *********/
        $("#prm_entry_no").select2(select2_default({
            url:`purchase_return/get_select2_entry_no`,
            placeholder:'ENTRY NO',
        })).on('change', () => trigger_search());
        $("#prm_acc_id").select2(select2_default({
            url:`purchase_return/get_select2_acc_id`,
            placeholder:'SUPPLIER',
        })).on('change', () => trigger_search());
    /************** PURCHASE REURN SUMMARY *********/
    /************** SALES SUMMARY ******************/
        $("#sm_bill_no").select2(select2_default({
            url:`sales/get_select2_bill_no`,
            placeholder:'BILL NO',
        })).on('change', () => trigger_search());
        $("#sm_acc_id").select2(select2_default({
            url:`sales/get_select2_acc_id`,
            placeholder:'SUPPLIER',
        })).on('change', () => trigger_search());
        $("#sm_user_id").select2(select2_default({
            url:`sales/get_select2_user_id`,
            placeholder:'SALES PERSON',
        })).on('change', () => trigger_search());
    /************** SALES SUMMARY ******************/
    /************** SALES RETURN SUMMARY ***********/
        $("#srm_entry_no").select2(select2_default({
            url:`sales_return/get_select2_entry_no`,
            placeholder:'ENTRY NO',
        })).on('change', () => trigger_search());
        $("#srm_acc_id").select2(select2_default({
            url:`sales_return/get_select2_acc_id`,
            placeholder:'SUPPLIER',
        })).on('change', () => trigger_search());    
    /************** SALES RETURN SUMMARY ***********/
    $(window).scrollTop(0);
});


let win = document.querySelector("#scroll_wrapper"); 
let page = 1;
let total_rows = parseInt($("#total_rows").html(), 10);
let total_pages = Math.ceil(total_rows / PER_PAGE);
let lastScrollTop = 0; // track last scroll position

win && win.addEventListener('scroll', function() {
    let st = win.scrollTop;
    // Only trigger when scrolling down
    if (st > lastScrollTop && st + win.clientHeight >= win.scrollHeight - 50) {
        if (page <= total_pages) {
            let offset = page * PER_PAGE;
            let limit = PER_PAGE;
            let path = `${base_url}/${link}/${sub_link}/get_scroll_data?offset=${offset}&limit=${limit}&${queryString}`;
            $("#loading").show();
            $.ajax({
                type: 'POST',
                url: path,
                dataType: 'JSON',
                success: function(resp) {      
                    const {status, data, msg} = resp; 
                    if (status && data.length > 0) {
                        console.log(data)
                        $.each(data, (inx, val) => {
                            render(val, offset + inx);
                        });
                        page++; // move to next page

                    }
                    $("#loading").hide();
                },
                error: function(resp) {
                    console.log(resp);
                    $("#loading").hide();
                }
            });
        }
    }

    lastScrollTop = st <= 0 ? 0 : st; // update last scroll
});



const paginate = (items, page)=>{
  let start = PER_PAGE * page;
  return items.slice(start, start + PER_PAGE);
}

const sorting_data = field => {
    $('#report_wrapper').scrollTop(0);
    let new_raw = raw.sort(dynamicSort(field));
    $('#report_wrapper').html('');
    page = 0
    let data = paginate(new_raw, page);
    if(data && data.length != 0){
        render(data, page);
    }
    $(`.fa-fw`).removeClass('text-success').addClass('text-danger');
    $(`#${field}`).removeClass('text-danger').addClass('text-success');

}
const dynamicSort = property => {
    let sortOrder = 1;
    if(property[0] === "-") {
        sortOrder = -1;
        property = property.substr(1);
    }
    return function (a,b) {
        let result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
        return result * sortOrder;
    }
}