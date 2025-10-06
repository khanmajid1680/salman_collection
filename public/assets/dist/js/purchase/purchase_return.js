$(document).ready(()=>{
    $("#bm_id").select2(select2_default({
        url:`master/barcode/get_barcode_select2`,
        placeholder:'SCAN BARCODE',
        maximumInputLength:12,
        minimumInputLength:4,
        param:'PRETURN',
        param1:$('#prm_id').val(),
        param2:$('#prm_acc_id').val(),
        barcode:'bm_id',
    })).on('change', ()=> get_barcode_data());
    $("#entry_no").select2(select2_default({
        url:`purchase_return/get_select2_entry_no`,
        placeholder:'ENTRY NO',
    })).on('change', () => trigger_search());
    $("#acc_id").select2(select2_default({
        url:`purchase_return/get_select2_acc_id`,
        placeholder:'SUPPLIER',
    })).on('change', () => trigger_search());    
    $("#from_entry_date, #to_entry_date").on('change', () => trigger_search());
});
var purchase_cnt = 1;
const is_supplier_change = data => {
    if(data.length == 0) return false
    let acc_id = $('#prm_acc_id').val()
    if(acc_id == '') return true;
    if(acc_id == data[0]['account_id']) return true;
    if(!confirm('This barcode is from diffrent supplier. Do you want to continue?')){
        $('#bm_id').val(null).trigger('change');
        $('#bm_id').select2('open');
        return false;
    }
    $('#prm_acc_id').val('')
    $('#account_name').val('')
    $('#purchase_material_wrapper').empty()
    return true
}
const get_barcode_data = () =>{
    notifier('bm_id')
    let bm_id   = $('#bm_id').val();
    let check   = false;
    let total_tr= $('#purchase_material_wrapper tr').length;
    if(bm_id != null){
        if(total_tr > 0){
            for (let i = 1; i <= total_tr; i++){
                let id = $('#purchase_material_wrapper tr:nth-child('+i+')').attr('id');
                let lastunderscore = id.lastIndexOf("_");
                let cnt = id.substring(lastunderscore+1);
                let old_bm_id   = $('#prt_bm_id_'+cnt).val();
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
                        if(is_supplier_change(data)){
                            $('#account_name').val(`${data[0]['account_name']} - ${data[0]['account_code']}`)
                            $('#prm_acc_id').val(data[0]['account_id'])
                            let sub_total   = parseFloat(data[0]['bm_pt_rate']) - parseFloat(data[0]['bm_pt_disc']);
                            let tr = `
                                <tr id="rowid_${purchase_cnt}" class="floating-form">
                                    <td class="floating-label">
                                        <input type="hidden" name="prt_id[]" id="prt_id_${purchase_cnt}" value="0" />
                                        <input type="hidden" name="prt_bm_id[]" id="prt_bm_id_${purchase_cnt}" value="${data[0]['bm_id']}" />
                                        <input type="number" class="form-control floating-input" id="prt_bm_item_code_${purchase_cnt}" value="${data[0]['bm_item_code']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="prt_bill_no[]" id="prt_bill_no_${purchase_cnt}" value="${data[0]['pm_bill_no']}" readonly />
                                        <input type="hidden" name="prt_pm_id[]" id="prt_pm_id_${purchase_cnt}" value="${data[0]['pm_id']}" />
                                        <input type="hidden" name="prt_pt_id[]" id="prt_pt_id_${purchase_cnt}" value="${data[0]['bm_pt_id']}" />
                                    </td>
                                    <td class="floating-label">
                                        <input type="text" class="form-control floating-input" name="prt_bill_date[]" id="prt_bill_date_${purchase_cnt}" value="${data[0]['pm_bill_date']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="text" class="form-control floating-input" id="design_name_${purchase_cnt}" value="${data[0]['design_name']}" readonly />
                                        <input type="hidden" name="prt_design_id[]" id="prt_design_id_${purchase_cnt}" value="${data[0]['design_id']}" />
                                    </td>
                                    <td class="floating-label">
                                        <input type="text" class="form-control floating-input" id="style_name_${purchase_cnt}" value="${data[0]['style_name']}" readonly />
                                        <input type="hidden" name="prt_style_id[]" id="prt_style_id_${purchase_cnt}" value="${data[0]['style_id']}" />
                                    </td>
                                    <td class="floating-label">
                                        <input type="text" class="form-control floating-input" id="brand_name_${purchase_cnt}" value="${data[0]['brand_name']}" readonly />
                                        <input type="hidden" name="prt_brand_id[]" id="prt_brand_id_${purchase_cnt}" value="${data[0]['brand_id']}" />
                                    </td>
                                    
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="prt_qty[]" id="prt_qty_${purchase_cnt}" value="${data[0]['bm_pt_qty']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="prt_rate[]" id="prt_rate_${purchase_cnt}" value="${data[0]['bm_pt_rate']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="prt_disc[]" id="prt_disc_${purchase_cnt}" value="${data[0]['bm_pt_disc']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="prt_taxable_amt[]" id="prt_taxable_amt_${purchase_cnt}" value="${data[0]['prt_taxable_amt']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="prt_sgst_per[]" id="prt_sgst_per_${purchase_cnt}" value="${data[0]['prt_sgst_per']}" readonly />
                                        <input type="number" class="form-control floating-input" name="prt_sgst_amt[]" id="prt_sgst_amt_${purchase_cnt}" value="${data[0]['prt_sgst_amt']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="prt_cgst_per[]" id="prt_cgst_per_${purchase_cnt}" value="${data[0]['prt_cgst_per']}" readonly />
                                        <input type="number" class="form-control floating-input" name="prt_cgst_amt[]" id="prt_cgst_amt_${purchase_cnt}" value="${data[0]['prt_cgst_amt']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="prt_igst_per[]" id="prt_igst_per_${purchase_cnt}" value="${data[0]['prt_igst_per']}" readonly />
                                        <input type="number" class="form-control floating-input" name="prt_igst_amt[]" id="prt_igst_amt_${purchase_cnt}" value="${data[0]['prt_igst_amt']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="prt_sub_total[]" id="prt_sub_total_${purchase_cnt}" value="${sub_total}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <button type="button" class="btn btn-primary" href="javascript:void(0)" onclick="remove_row(${purchase_cnt})"> 
                                            <i class="text-danger fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;

                            $('#purchase_material_wrapper').prepend(tr);
                            callToastify('success', `${data[0]['style_name']} - ${data[0]['brand_name']} ADDED`, 'right')
                            purchase_cnt++;

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
    let total_tr            = $('#purchase_material_wrapper tr').length;
    let total_qty           = 0;
    let total_total_amt     = 0;
    let total_taxable_amt   = 0;
    let total_sgst_amt      = 0;
    let total_cgst_amt      = 0;
    let total_igst_amt      = 0;

    let total_final_amt     = 0;
    for (let i = 1; i <= total_tr; i++) {
        let id = $('#purchase_material_wrapper tr:nth-child('+i+')').attr('id');
        let lastunderscore = id.lastIndexOf("_");
        let cnt = id.substring(lastunderscore+1);
        
        let qty = $("#prt_qty_"+cnt).val() 
        if(isNaN(qty) || qty == '') qty = 0;

        let rate = $("#prt_rate_"+cnt).val() 
        if(isNaN(rate) || rate == '') rate = 0;

        let disc = $("#prt_disc_"+cnt).val() 
        if(isNaN(disc) || disc == '') disc = 0;

        let taxable_amt = (parseFloat(qty) * parseFloat(rate)) - parseFloat(disc);
        if(isNaN(taxable_amt) || taxable_amt == '') taxable_amt = 0; 
        $("#prt_taxable_amt_"+cnt).val(taxable_amt.toFixed(2));

        let sgst_per = $("#prt_sgst_per_"+cnt).val() 
        if(isNaN(sgst_per) || sgst_per == '') sgst_per = 0;

        let cgst_per = $("#prt_cgst_per_"+cnt).val() 
        if(isNaN(cgst_per) || cgst_per == '') cgst_per = 0;

        let igst_per = $("#prt_igst_per_"+cnt).val() 
        if(isNaN(igst_per) || igst_per == '') igst_per = 0;

        let sgst_amt = (parseFloat(taxable_amt) * parseFloat(sgst_per)) / 100;
        if (isNaN(sgst_amt) || sgst_amt == "") sgst_amt = 0; 
        $("#prt_sgst_amt_"+cnt).val(sgst_amt.toFixed(2));

        let cgst_amt = (parseFloat(taxable_amt) * parseFloat(cgst_per)) / 100;
        if (isNaN(cgst_amt) || cgst_amt == "") cgst_amt = 0; 
        $("#prt_cgst_amt_"+cnt).val(cgst_amt.toFixed(2));

        let igst_amt = (parseFloat(taxable_amt) * parseFloat(igst_per)) / 100;
        if (isNaN(igst_amt) || igst_amt == "") igst_amt = 0; 
        $("#prt_igst_amt_"+cnt).val(igst_amt.toFixed(2));

        let total_amt =
            parseFloat(taxable_amt) +
            parseFloat(sgst_amt) +
            parseFloat(cgst_amt) +
            parseFloat(igst_amt);
          if (isNaN(total_amt) || total_amt == "") total_amt = 0; 

        $("#prt_sub_total_"+cnt).val(total_amt.toFixed(2));


        total_qty    = parseFloat(total_qty) + parseFloat(qty);
        total_taxable_amt= parseFloat(total_taxable_amt) + parseFloat(taxable_amt);
        total_sgst_amt= parseFloat(total_sgst_amt) + parseFloat(sgst_amt);
        total_cgst_amt= parseFloat(total_cgst_amt) + parseFloat(cgst_amt);
        total_igst_amt= parseFloat(total_igst_amt) + parseFloat(igst_amt);

        total_total_amt= parseFloat(total_total_amt) + parseFloat(total_amt);

    }

    $("#prm_total_qty").val(total_qty.toFixed(2));        
    $("#prm_taxable_amt").val(total_taxable_amt.toFixed(2));
    $("#prm_sgst_amt").val(total_sgst_amt.toFixed(2));
    $("#prm_cgst_amt").val(total_cgst_amt.toFixed(2));
    $("#prm_igst_amt").val(total_igst_amt.toFixed(2));
    
    $("#prm_sub_total").val(total_total_amt.toFixed(2));


    let after_decimal = parseFloat('0.'+total_total_amt.toString().split(".")[1]);
    $("#prm_round_off").val(after_decimal.toFixed(2))

    let bill_disc = $("#prm_bill_disc").val()
    if(isNaN(bill_disc) || bill_disc == '') bill_disc = 0;
    
    let gst_amt = $("#prm_gst_amt").val();
    if(isNaN(gst_amt) || gst_amt == '') gst_amt = 0;

    total_final_amt = (parseFloat(total_total_amt) + parseFloat(gst_amt)) - parseFloat(bill_disc); 
    $("#prm_final_amt").val(Math.round(total_final_amt));        
    
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
const remove_pr_master_notifier = () =>{
    notifier('prm_acc_id')
    notifier('prm_total_qty')
    notifier('prm_sub_total')
    notifier('prm_final_amt')
}
const add_update_purchase_return = (id) =>{
    remove_pr_master_notifier()
    let check   = true;
    let total_tr= $('#purchase_material_wrapper tr').length;
    if($("#prm_acc_id").val() == ''){
        notifier('prm_acc_id', 'Required')
        check = false;
    }
    if($("#prm_total_qty").val() <= 0){
        notifier('prm_total_qty', 'Required')
        check = false;
    }

    if($("#prm_sub_total").val() <= 0){
        notifier('prm_sub_total', 'Required')
        check = false;
    }

    if($("#prm_final_amt").val() <= 0){
        notifier('prm_final_amt', 'Required')
        check = false;
    }
    if(!check){
        callToastify('error', 'Oh snap ! You forgot to enter some information', 'right')
        $("body, html").animate({'scrollTop':0},1000);
    }else{
        let path        = "purchase_return/add_update/"+id;   
        var form_data   = $("#purchase_return_form").serialize();

        ajaxCall('POST',path,form_data,'JSON',resp =>{
            let {status, flag, msg} = resp;
            if(status){
                if(flag == 1){
                    if(id == 0) {
                    } else {
                    }
                    remove_pr_master_notifier()
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