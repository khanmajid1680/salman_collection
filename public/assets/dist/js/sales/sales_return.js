$(document).ready(()=>{
    $("#bm_id").select2(select2_default({
        url:`master/barcode/get_barcode_select2`,
        placeholder:'SCAN BARCODE',
        maximumInputLength:12,
        minimumInputLength:4,
        param:'SRETURN',
        param1:$('#srm_id').val(),
        param2:$('#srm_acc_id').val(),
        barcode:'bm_id',
    })).on('change', ()=> get_barcode_data());
    $("#entry_no").select2(select2_default({
        url:`sales_return/get_select2_entry_no`,
        placeholder:'ENTRY NO',
    })).on('change', () => trigger_search());
    $("#acc_id").select2(select2_default({
        url:`sales_return/get_select2_acc_id`,
        placeholder:'CUSTOMER',
    })).on('change', () => trigger_search());    
    $("#from_entry_date, #to_entry_date").on('change', () => trigger_search());
});
var sales_cnt = 1;
const is_customer_change = data => {
    if(data.length == 0) return false
    let acc_id = $('#srm_acc_id').val()
    if(acc_id == '') return true;
    if(acc_id == data[0]['customer_id']) return true;
    if(!confirm('This barcode is from diffrent supplier. Do you want to continue?')){
        $('#bm_id').val(null).trigger('change');
        $('#bm_id').select2('open');
        return false;
    }
    $('#srm_acc_id').val('')
    $('#account_name').val('')
    $('#sales_material_wrapper').empty()
    return true
}
const get_barcode_data = () =>{
    notifier('bm_id')
    let bm_id   = $('#bm_id').val();
    let check   = false;
    let total_tr= $('#sales_material_wrapper tr').length;
    if(bm_id != null){
        if(total_tr > 0){
            for (let i = 1; i <= total_tr; i++){
                let id = $('#sales_material_wrapper tr:nth-child('+i+')').attr('id');
                let lastunderscore = id.lastIndexOf("_");
                let cnt = id.substring(lastunderscore+1);
                let old_bm_id   = $('#srt_bm_id_'+cnt).val();
                if(bm_id == old_bm_id){
                    check = true;
                }
            }
        }
        if(check){
            callToastify('error', 'Duplicate item found!', 'right')
            notifier('bm_id', 'Barcode already added')
            $("body, html").animate({'scrollTop':0},1000);

            setTimeout(() =>{
                $('#bm_id').val(null).trigger('change');
                $('#bm_id').select2('open');
            },RELOAD_TIME);
        }else{
            let path = `master/barcode/get_barcode_data/${bm_id}`;
            ajaxCall('GET',path,'','JSON',resp=>{
                let {status, flag, data, msg} = resp
                if(status){
                    if(flag == 1){
                        if(is_customer_change(data)){
                            $('#account_name').val(`${data[0]['customer_name']} - ${data[0]['customer_mobile']}`)
                            $('#srm_acc_id').val(data[0]['customer_id'])

                            let tr = `
                                <tr id="rowid_${sales_cnt}" class="floating-form">
                                    <td class="floating-label">
                                        <input type="hidden" name="srt_id[]" id="srt_id_${sales_cnt}" value="0" />
                                        <input type="hidden" name="srt_user_id[]" id="srt_user_id_${sales_cnt}" value="${data[0]['user_id']}" />
                                        <input type="text" class="form-control floating-input" id="user_name_${sales_cnt}" value="${data[0]['user_name']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="hidden" name="srt_bm_id[]" id="srt_bm_id_${sales_cnt}" value="${data[0]['bm_id']}" />
                                        <input type="number" class="form-control floating-input" id="srt_bm_item_code_${sales_cnt}" value="${data[0]['bm_item_code']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="srt_bill_no[]" id="srt_bill_no_${sales_cnt}" value="${data[0]['sm_bill_no']}" readonly />
                                        <input type="hidden" name="srt_sm_id[]" id="srt_sm_id_${sales_cnt}" value="${data[0]['sm_id']}" />
                                        <input type="hidden" name="srt_st_id[]" id="srt_st_id_${sales_cnt}" value="${data[0]['st_id']}" />
                                        <input type="hidden" id="payment_mode_${sales_cnt}" value="${data[0]['sm_payment_mode']}" />
                                    </td>
                                    <td class="floating-label">
                                        <input type="text" class="form-control floating-input" name="srt_bill_date[]" id="srt_bill_date_${sales_cnt}" value="${data[0]['sm_bill_date']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="text" class="form-control floating-input" id="style_name_${sales_cnt}" value="${data[0]['style_name']}" readonly />
                                        <input type="hidden" name="srt_style_id[]" id="srt_style_id_${sales_cnt}" value="${data[0]['style_id']}" />
                                    </td>
                                    <td class="floating-label">
                                        <input type="text" class="form-control floating-input" id="brand_name_${sales_cnt}" value="${data[0]['brand_name']}" readonly />
                                        <input type="hidden" name="srt_brand_id[]" id="srt_brand_id_${sales_cnt}" value="${data[0]['brand_id']}" />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="srt_qty[]" id="srt_qty_${sales_cnt}" value="${data[0]['bm_pt_qty']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="srt_rate[]" id="srt_rate_${sales_cnt}" value="${data[0]['st_rate']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="srt_sub_total[]" id="srt_sub_total_${sales_cnt}" value="${data[0]['st_sub_total']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="srt_disc_amt[]" id="srt_disc_amt_${sales_cnt}" value="${data[0]['st_disc_amt']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="srt_taxable_amt[]" id="srt_taxable_amt_${sales_cnt}" value="${data[0]['srt_taxable_amt']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="srt_sgst_per[]" id="srt_sgst_per_${sales_cnt}" value="${data[0]['srt_sgst_per']}" readonly />
                                        <input type="number" class="form-control floating-input" name="srt_sgst_amt[]" id="srt_sgst_amt_${sales_cnt}" value="${data[0]['srt_sgst_amt']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="srt_cgst_per[]" id="srt_cgst_per_${sales_cnt}" value="${data[0]['srt_cgst_per']}" readonly />
                                        <input type="number" class="form-control floating-input" name="srt_cgst_amt[]" id="srt_cgst_amt_${sales_cnt}" value="${data[0]['srt_cgst_amt']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="srt_igst_per[]" id="srt_igst_per_${sales_cnt}" value="${data[0]['srt_igst_per']}" readonly />
                                        <input type="number" class="form-control floating-input" name="srt_igst_amt[]" id="srt_igst_amt_${sales_cnt}" value="${data[0]['srt_igst_amt']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="srt_total_amt[]" id="srt_total_amt_${sales_cnt}" value="${data[0]['st_sub_total_amt']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <button type="button" class="btn btn-primary" href="javascript:void(0)" onclick="remove_row(${sales_cnt})"> 
                                            <i class="text-danger fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;

                            $('#sales_material_wrapper').prepend(tr);
                            callToastify('success', `${data[0]['style_name']} - ${data[0]['brand_name']} ADDED`, 'right')
                            sales_cnt++;

                            $('#bm_id').val(null).trigger('change');
                            $('#bm_id').select2('open');
                            calculate_master_total()                            

                        }
                    }else{
                        response_error(flag, msg)
                    }
                }else{
                    session_expired();
                }
            }, errmsg=>{})
        }
    }
}
const calculate_master_total = () =>{
    let total_tr            = $('#sales_material_wrapper tr').length;
    let total_qty           = 0;
    let total_sub_amt       = 0;
    let total_disc_amt      = 0;
    let total_taxable_amt   = 0;
    let total_sgst_amt      = 0;
    let total_cgst_amt      = 0;
    let total_igst_amt      = 0;

    let total_final_amt     = 0;
    let total_paid_amt      = 0;
    for (let i = 1; i <= total_tr; i++) {
        let id = $('#sales_material_wrapper tr:nth-child('+i+')').attr('id');
        let lastunderscore = id.lastIndexOf("_");
        let cnt = id.substring(lastunderscore+1);
        
        let qty = $("#srt_qty_"+cnt).val() 
        if(isNaN(qty) || qty == '') qty = 0;

        let rate = $("#srt_rate_"+cnt).val() 
        if(isNaN(rate) || rate == '') rate = 0;

        let sub_total = parseFloat(qty) * parseFloat(rate);
        if(isNaN(sub_total) || sub_total == '') sub_total = 0; 
        $("#srt_sub_total_"+cnt).val(sub_total.toFixed(2));

        let disc_amt = $("#srt_disc_amt_"+cnt).val();
        if(isNaN(disc_amt) || disc_amt == '') disc_amt = 0; 

        let taxable_amt = $("#srt_taxable_amt_"+cnt).val();
        if(isNaN(taxable_amt) || taxable_amt == '') taxable_amt = 0; 

        // let taxable_amt = parseFloat(sub_total) - parseFloat(disc_amt);
        // if(isNaN(taxable_amt) || taxable_amt == '') taxable_amt = 0; 
        // $("#srt_taxable_amt_"+cnt).val(taxable_amt.toFixed(2));

        let sgst_per = $("#srt_sgst_per_"+cnt).val() 
        if(isNaN(sgst_per) || sgst_per == '') sgst_per = 0;

        let cgst_per = $("#srt_cgst_per_"+cnt).val() 
        if(isNaN(cgst_per) || cgst_per == '') cgst_per = 0;

        let igst_per = $("#srt_igst_per_"+cnt).val() 
        if(isNaN(igst_per) || igst_per == '') igst_per = 0;

        let sgst_amt = (parseFloat(taxable_amt) * parseFloat(sgst_per)) / 100;
        if (isNaN(sgst_amt) || sgst_amt == "") sgst_amt = 0; 
        $("#srt_sgst_amt_"+cnt).val(sgst_amt.toFixed(2));

        let cgst_amt = (parseFloat(taxable_amt) * parseFloat(cgst_per)) / 100;
        if (isNaN(cgst_amt) || cgst_amt == "") cgst_amt = 0; 
        $("#srt_cgst_amt_"+cnt).val(cgst_amt.toFixed(2));

        let igst_amt = (parseFloat(taxable_amt) * parseFloat(igst_per)) / 100;
        if (isNaN(igst_amt) || igst_amt == "") igst_amt = 0; 
        $("#srt_igst_amt_"+cnt).val(igst_amt.toFixed(2));

        let total_amt = parseFloat(taxable_amt) + parseFloat(sgst_amt) + parseFloat(cgst_amt) + parseFloat(igst_amt);
        if (isNaN(total_amt) || total_amt == "") total_amt = 0; 
        $("#srt_total_amt_"+cnt).val(total_amt.toFixed(2));

        total_qty      = parseFloat(total_qty) + parseFloat(qty);
        total_sub_amt  = parseFloat(total_sub_amt) + parseFloat(sub_total);
        total_disc_amt = parseFloat(total_disc_amt) + parseFloat(disc_amt);
        
        total_taxable_amt = parseFloat(total_taxable_amt) + parseFloat(taxable_amt);
        total_sgst_amt = parseFloat(total_sgst_amt) + parseFloat(sgst_amt);
        total_cgst_amt = parseFloat(total_cgst_amt) + parseFloat(cgst_amt);
        total_igst_amt = parseFloat(total_igst_amt) + parseFloat(igst_amt);

        total_final_amt= parseFloat(total_final_amt) + parseFloat(total_amt);
        if($("#payment_mode_"+cnt).val() != 'CREDIT'){
            total_paid_amt= parseFloat(total_paid_amt) + parseFloat(total_amt);
        }
    } 

    $("#srm_total_qty").val(total_qty.toFixed(2));        
    $("#srm_sub_total").val(total_sub_amt.toFixed(2));
    $("#srm_total_disc").val(total_disc_amt.toFixed(2));
    
    $("#srm_taxable_amt").val(total_taxable_amt.toFixed(2));
    $("#srm_sgst_amt").val(total_sgst_amt.toFixed(2));
    $("#srm_cgst_amt").val(total_cgst_amt.toFixed(2));
    $("#srm_cgst_amt").val(total_cgst_amt.toFixed(2));

    let after_decimal = parseFloat('0.'+total_final_amt.toString().split(".")[1]);
    $("#srm_round_off").val(after_decimal.toFixed(2))

    let bill_disc = $("#srm_bill_disc").val()
    if(isNaN(bill_disc) || bill_disc == '') bill_disc = 0;

    total_final_amt = parseFloat(total_final_amt) - parseFloat(bill_disc); 
    total_paid_amt = parseFloat(total_paid_amt) - parseFloat(bill_disc); 
    $("#srm_final_amt").val(Math.round(total_final_amt));        
    $("#srm_amt_paid").val(Math.round(total_paid_amt));        
    
    if(total_final_amt > 0)
    {
        $('.master_block_btn').prop('disabled', false)
        // $('.master_block_btn').removeClass('btn-default').addClass('btn-success')
    }
    else
    {
        $('.master_block_btn').prop('disabled', true)   
        // $('.master_block_btn').removeClass('btn-success').addClass('btn-default')            
    }
}
const remove_row = cnt =>{
    let style_name = $(`#style_name_${cnt}`).val()
    let brand_name = $(`#brand_name_${cnt}`).val()
    callToastify('success', `${style_name} - ${brand_name} REMOVED`, 'right')
    $("#rowid_"+cnt).detach();  
    calculate_master_total() 
}
const remove_sr_master_notifier = () =>{
    notifier('srm_acc_id')
    notifier('srm_total_qty')
    notifier('srm_sub_total')
    notifier('srm_final_amt')
    notifier('srm_amt_paid')
}
const add_update_sales_return = (id) =>{
    remove_sr_master_notifier()
    let check   = true;
    let total_tr= $('#sales_material_wrapper tr').length;
    if($("#srm_acc_id").val() == ''){
        notifier('srm_acc_id', 'Required')
        check = false;
    }
    if($("#srm_total_qty").val() <= 0){
        notifier('srm_total_qty', 'Required')
        check = false;
    }

    if($("#srm_sub_total").val() <= 0){
        notifier('srm_sub_total', 'Required')
        check = false;
    }

    if($("#srm_final_amt").val() <= 0){
        notifier('srm_final_amt', 'Required')
        check = false;
    }
    if($("#srm_amt_paid").val() > 0){
        if(parseFloat($("#srm_final_amt").val()) != parseFloat($("#srm_amt_paid").val())){
            notifier('srm_amt_paid', 'Amt to pay should be equal to bill amt.')
            check = false;
        }
    }
    if(!check){
        callToastify('error', 'Oh snap ! You forgot to enter some information', 'right')
        $("body, html").animate({'scrollTop':0},1000);
    }else{
        let path        = "sales_return/add_update/"+id;   
        var form_data   = $("#sales_return_form").serialize();

        ajaxCall('POST',path,form_data,'JSON',resp =>{
            let {status, flag, msg} = resp;
            if(status){
                if(flag == 1){
                    if(id == 0) {
                    } else {
                    }
                    remove_sr_master_notifier()
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
            console.log(errmsg);
        });
    }
}