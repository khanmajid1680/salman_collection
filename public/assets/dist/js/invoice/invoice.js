$(document).ready(()=>{
    $("#im_entry_no").select2(select2_default({
        url:`invoice/get_select2_entry_no`,
        placeholder:'ENTRY NO',
    })).on('change', () => trigger_search());
    $("#from_date, #to_date").on('change', () => trigger_search());
    $("#im_from_bill_date, #im_to_bill_date").on('change', () => get_sales_data());
});
const clear_sales_wrapper = () =>{
    $('#sales_material_wrapper').html('')
}
const get_sales_data = () => {
    let from_date   = $("#im_from_bill_date").val();
    let to_date     = $("#im_to_bill_date").val();
    clear_sales_wrapper();
    if(from_date != '' && to_date != ''){
        let path = `invoice/get_sales_data/${from_date}/${to_date}`;  
        ajaxCall('GET',path,'','JSON',resp=>{
            const{status, data, flag, msg} = resp
            if(status){
                if(flag == 1){
                    add_sales_wrapper_for_invoice(data);
                }else{
                    response_error(flag, msg)
                }
            }else{
                session_expired()
            }
            
        },errmsg=>{
        });
    }
}
const add_sales_wrapper_for_invoice = data => {
    if(data && data.length != 0){
        let tr = '';
        data.forEach(sm => {
            let {sm_id, sm_bill_no, sm_bill_date, sm_acc_id, account_name, sm_user_id, user_fullname, sm_payment_mode, sm_total_qty,
                sm_sub_total, sm_total_disc, sm_promo_disc, sm_point_used, sm_round_off, sm_final_amt} = sm;
            tr +=`
                <tr id="rowid_${sm_id}">
                    <td width="3%">
                        <input type="checkbox" class="checkboxes" id="child_checkbox_${sm_id}" onchange="calculate_master_total()" ${sm_payment_mode == 'CASH' ? '' : 'checked'} ${sm_payment_mode == 'CASH' ? '' : 'disabled'} />
                        <input type="hidden" id="it_id_${sm_id}" name="it_id[]" value="0"/>
                        <input type="hidden" id="it_generate_${sm_id}" name="it_generate[]" value="${sm_payment_mode == 'CASH' ? 0 : 1}"/>
                    </td>
                    <td width="7%">
                        ${sm_bill_no}
                        <input type="hidden" id="it_sm_id_${sm_id}" name="it_sm_id[]" value="${sm_id}" />
                        <input type="hidden" id="it_bill_no_${sm_id}" name="it_bill_no[]" value="${sm_bill_no}" />
                    </td>
                    <td width="7%">
                        ${sm_bill_date}
                        <input type="hidden" id="it_bill_date_${sm_id}" name="it_bill_date[]" value="${sm_bill_date}" />
                    </td>
                    <td width="10%">
                        ${account_name}
                        <input type="hidden" id="it_acc_id_${sm_id}" name="it_acc_id[]" value="${sm_acc_id}" />
                    </td>
                    <td width="10%">
                        ${user_fullname}
                        <input type="hidden" id="it_user_id_${sm_id}" name="it_user_id[]" value="${sm_user_id}" />
                    </td>
                    <td width="10%">
                        ${sm_payment_mode}
                        <input type="hidden" id="it_payment_mode_${sm_id}" name="it_payment_mode[]" value="${sm_payment_mode}" />
                    </td>
                    <td width="7%">
                        ${sm_total_qty}
                        <input type="hidden" id="it_total_qty_${sm_id}" name="it_total_qty[]" value="${sm_total_qty}" />
                    </td>
                    <td width="7%">
                        ${sm_sub_total}
                        <input type="hidden" id="it_sub_amt_${sm_id}" name="it_sub_amt[]" value="${sm_sub_total}" />
                    </td>
                    <td width="7%">
                        ${sm_total_disc}
                        <input type="hidden" id="it_disc_amt_${sm_id}" name="it_disc_amt[]" value="${sm_total_disc}" />
                    </td>
                    <td width="8%">
                        ${sm_promo_disc}
                        <input type="hidden" id="it_promo_amt_${sm_id}" name="it_promo_amt[]" value="${sm_promo_disc}" />
                    </td>
                    <td width="10%">
                        ${sm_point_used}
                        <input type="hidden" id="it_point_amt_${sm_id}" name="it_point_amt[]" value="${sm_point_used}" />
                    </td>
                    <td width="8%">
                        ${sm_round_off}
                        <input type="hidden" id="it_round_off_${sm_id}" name="it_round_off[]" value="${sm_round_off}" />
                    </td>
                    <td width="8%">
                        ${sm_final_amt}
                        <input type="hidden" id="it_final_amt_${sm_id}" name="it_final_amt[]" value="${sm_final_amt}" />
                    </td>
                </tr>
            `;
        })
        $('#sales_material_wrapper').html(tr);
        calculate_master_total();
    }else{
        $('#sales_material_wrapper').html(`
            <tr>
                <td colspan="13" class="font-weight-bold text-danger" align="center"> No Record Found!!!</td>
            </tr>
        `);
    }
}
const select_deselect = () => {
    let parent_checked = $(`#parent_checkbox`).is(':checked');
    let total_tr = $('#sales_material_wrapper tr').length;
    for (let i = 1; i <= total_tr; i++){
        let id = $('#sales_material_wrapper tr:nth-child('+i+')').attr('id');
        let lastunderscore = id.lastIndexOf("_");
        let cnt = id.substring(lastunderscore+1);
        let mode = $(`#it_payment_mode_${cnt}`).val();
        if(mode == 'CASH'){
            $(`#child_checkbox_${cnt}`).prop('checked', parent_checked)
        }
    }
    calculate_master_total()   
}
const calculate_master_total = () => {
    let total_tr        = $('#sales_material_wrapper tr').length;
    let sales_amt       = 0;
    let generated_amt   = 0;
    for (let i = 1; i <= total_tr; i++){
        let id = $('#sales_material_wrapper tr:nth-child('+i+')').attr('id');
        let lastunderscore = id.lastIndexOf("_");
        let cnt = id.substring(lastunderscore+1);
        if($(`#child_checkbox_${cnt}`).is(':checked')){
            let bill = $(`#it_final_amt_${cnt}`).val();
            generated_amt = parseFloat(generated_amt) + parseFloat(bill);
            if(isNaN(generated_amt) || generated_amt == '') generated_amt = 0;
            $(`#it_generate_${cnt}`).val(1)
        }else{
            $(`#it_generate_${cnt}`).val(0);
        }
        let sm_bill = $(`#it_final_amt_${cnt}`).val();
        sales_amt = parseFloat(sales_amt) + parseFloat(sm_bill);
        if(isNaN(sales_amt) || sales_amt == '') sales_amt = 0;

    }
    let total_checked   = $('.checkboxes:checked').length;
    $('#im_sm_bill_count').val(total_tr)
    $('#im_sm_bill_amt').val(sales_amt.toFixed(2))
    $('#im_total_qty').val(total_checked)
    $('#im_final_amt').val(generated_amt.toFixed(2))
    $(`#parent_checkbox`).prop('checked', total_tr == total_checked);
    if(total_checked != 0){
        $('.master_block_btn').prop('disabled', false)
    }else{
        $('.master_block_btn').prop('disabled', true)
    }
}
const add_update_invoice = id => {
    event.preventDefault();
    let check = true;
    if(!check){
        callToastify('error', 'Oh snap ! You forgot to enter some information', 'right')
        $("body, html").animate({'scrollTop':0},1000);
    }else{
        let path        = `invoice/add_update/${id}`;   
        let form_data   = $("#invoice_form").serialize();
        ajaxCall('POST',path,form_data,'JSON',resp =>{
            let {status, flag, data, msg} = resp;
            if(status){
                if(flag == 1){
                    if(id == 0){

                    }else{

                    }
                    if(data){
                        window.open(`${base_url}/invoice?action=print&id=${data.id}`,'_blank','width=1024, height=768');
                    }
                    callToastify('success', msg, 'right')
                    $("body, html").animate({'scrollTop':0},1000);
                    setTimeout(function(){window.location.reload(); },RELOAD_TIME); 

                }else{
                    response_error(flag, msg)
                }
            }else{
                session_expired()               
            }                
        },errmsg => {
        });
    }
}