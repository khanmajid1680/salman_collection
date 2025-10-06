$(document).ready(()=>{
    $("#bm_id").select2(select2_default({
        url:`master/barcode/get_barcode_select2`,
        placeholder:'SCAN BARCODE',
        maximumInputLength:12,
        minimumInputLength:4,
        param:'OUTWARD',
        param1:$('#om_id').val(),
        param2:$('#om_branch').val(),
        barcode:'bm_id',
    })).on('change', ()=> get_barcode_data());
    $("#entry_no").select2(select2_default({
        url:`outward/get_select2_entry_no`,
        placeholder:'ENTRY NO',
    })).on('change', () => trigger_search());
    $("#branch_id").select2(select2_default({
        url:`outward/get_select2_branch_id`,
        placeholder:'BRANCH',
    })).on('change', () => trigger_search());    
    $("#from_entry_date, #to_entry_date").on('change', () => trigger_search());
});
var outward_cnt = 1;
const get_barcode_data = () =>{
    notifier('bm_id')
    let bm_id   = $('#bm_id').val();
    let check   = false;
    let total_tr= $('#outward_material_wrapper tr').length;
    if(bm_id != null){
        if(total_tr > 0){
            for (let i = 1; i <= total_tr; i++){
                let id = $('#outward_material_wrapper tr:nth-child('+i+')').attr('id');
                let lastunderscore = id.lastIndexOf("_");
                let cnt = id.substring(lastunderscore+1);
                let old_bm_id   = $('#ot_bm_id_'+cnt).val();
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
                        if(data.length != 0){
                            let tr = `
                                <tr id="rowid_${outward_cnt}" class="floating-form">
                                    <td class="floating-label">
                                        <input type="hidden" name="ot_id[]" id="ot_id_${outward_cnt}" value="0" />
                                        <input type="hidden" name="ot_bm_id[]" id="ot_bm_id_${outward_cnt}" value="${data[0]['bm_id']}" />
                                        <input type="number" class="form-control floating-input" id="bm_item_code_${outward_cnt}" value="${data[0]['bm_item_code']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="ot_bill_no[]" id="ot_bill_no_${outward_cnt}" value="${data[0]['pm_bill_no']}" readonly />
                                        <input type="hidden" name="ot_pm_id[]" id="ot_pm_id_${outward_cnt}" value="${data[0]['pm_id']}" />
                                    </td>
                                    <td class="floating-label">
                                        <input type="text" class="form-control floating-input" name="ot_bill_date[]" id="ot_bill_date_${outward_cnt}" value="${data[0]['pm_bill_date']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="text" class="form-control floating-input" id="design_name_${outward_cnt}" value="${data[0]['design_name']}" readonly />
                                        <input type="hidden" name="ot_design_id[]" id="ot_design_id_${outward_cnt}" value="${data[0]['design_id']}" />
                                    </td>
                                    <td class="floating-label">
                                        <input type="text" class="form-control floating-input" id="style_name_${outward_cnt}" value="${data[0]['style_name']}" readonly />
                                        <input type="hidden" name="ot_style_id[]" id="ot_style_id_${outward_cnt}" value="${data[0]['style_id']}" />
                                    </td>
                                    <td class="floating-label">
                                        <input type="text" class="form-control floating-input" id="brand_name_${outward_cnt}" value="${data[0]['brand_name']}" readonly />
                                        <input type="hidden" name="ot_brand_id[]" id="ot_brand_id_${outward_cnt}" value="${data[0]['brand_id']}" />
                                    </td>
                                  
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="ot_qty[]" id="ot_qty_${outward_cnt}" value="${data[0]['bm_pt_qty']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="ot_rate[]" id="ot_rate_${outward_cnt}" value="${data[0]['bm_pt_rate']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="ot_sub_total[]" id="ot_sub_total_${outward_cnt}" value="${data[0]['bm_pt_rate']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <button type="button" class="btn btn-primary" href="javascript:void(0)" onclick="remove_row(${outward_cnt})"> 
                                            <i class="text-danger fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;

                            $('#outward_material_wrapper').prepend(tr);
                            callToastify('success', `${data[0]['style_name']} - ${data[0]['brand_name']} ADDED`, 'right')
                            outward_cnt++;

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
    let total_tr            = $('#outward_material_wrapper tr').length;
    let total_qty           = 0;
    let total_sub_amt       = 0;
    let total_final_amt     = 0;
    for (let i = 1; i <= total_tr; i++) {
        let id = $('#outward_material_wrapper tr:nth-child('+i+')').attr('id');
        let lastunderscore = id.lastIndexOf("_");
        let cnt = id.substring(lastunderscore+1);
        
        let qty = $("#ot_qty_"+cnt).val() 
        if(isNaN(qty) || qty == '') qty = 0;

        let rate = $("#ot_rate_"+cnt).val() 
        if(isNaN(rate) || rate == '') rate = 0;

        let sub_total = parseFloat(qty) * parseFloat(rate);
        if(isNaN(sub_total) || sub_total == '') sub_total = 0; 
        $("#ot_sub_total_"+cnt).val(sub_total.toFixed(2));

        total_qty    = parseInt(total_qty) + parseInt(qty);
        total_sub_amt= parseFloat(total_sub_amt) + parseFloat(sub_total);
    }
    $("#om_total_qty").val(total_qty);        
    $("#om_sub_total").val(total_sub_amt.toFixed(2));

    let after_decimal = parseFloat('0.'+total_sub_amt.toString().split(".")[1]);
    $("#om_round_off").val(after_decimal.toFixed(2))
    
    total_final_amt = parseFloat(total_sub_amt); 
    $("#om_final_amt").val(Math.round(total_final_amt));        
    
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
const remove_out_master_notifier = () =>{
    notifier('om_branch')
    notifier('om_total_qty')
    notifier('om_sub_total')
    notifier('om_final_amt')
}
const add_update_outward = (id) =>{
    remove_out_master_notifier()
    let check   = true;
    let total_tr= $('#outward_material_wrapper tr').length;
    if($("#om_branch").val() == 0){
        notifier('om_branch', 'Required')
        check = false;
    }
    if($("#om_total_qty").val() <= 0){
        notifier('om_total_qty', 'Required')
        check = false;
    }

    if($("#om_sub_total").val() <= 0){
        notifier('om_sub_total', 'Required')
        check = false;
    }

    if($("#om_final_amt").val() <= 0){
        notifier('om_final_amt', 'Required')
        check = false;
    }
    if(!check){
        callToastify('error', 'Oh snap ! You forgot to enter some information', 'right')
        $("body, html").animate({'scrollTop':0},1000);
    }else{
        let path        = "outward/add_update/"+id;   
        let form_data   = $("#outward_form").serialize();
        ajaxCall('POST',path,form_data,'JSON',resp =>{
            let {status, flag, msg} = resp;
            if(status){
                if(flag == 1){
                    if(id == 0) {
                    } else {
                    }
                    remove_out_master_notifier()
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