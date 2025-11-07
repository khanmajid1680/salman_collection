$(document).ready(()=>{
    $("#account_mobile").on('change', ()=>get_customer_data());
    $("#sm_acc_id").select2(select2_default({
        url:`master/account/get_select2_customer`,
        placeholder:'NAME - MOBILE',
    })).on('change', ()=>get_customer_data_with_loyalty());

    $("#sm_shipping_acc_id").select2(select2_default({
        url:`master/account/get_select2_customer`,
        placeholder:'NAME - MOBILE',
    }));

    $("#sm_transport_id").select2(select2_default({
       url:`master/transport/get_select2/_name`,
        placeholder:'TRANSPORT',
    }));

    $("#sm_user_id").select2(select2_default({
        url:`master/person/get_select2_user`,
        placeholder:'NAME - MOBILE',
    }));
    $("#bm_id").select2(select2_default({
        url:`master/barcode/get_barcode_select2`,
        placeholder:'SCAN BARCODE',
        maximumInputLength:12,
        minimumInputLength:4,
        param:'SALES',
        barcode:'bm_id',
    })).on('change', ()=> get_barcode_data());
    $("#design_id").select2(select2_default({
        url:`master/design/get_select2`,
        placeholder:'SELECT',
    }));
    $("#style_id").select2(select2_default({
        url:`master/style/get_select2`,
        placeholder:'SELECT',
    }));
    $("#brand_id").select2(select2_default({
        url:`master/brand/get_select2`,
        placeholder:'SELECT',
    }));
    $("#age_id").select2(select2_default({
        url:`master/age/get_select2`,
        placeholder:'SELECT',
    }));
    $("#bill_no").select2(select2_default({
        url:`sales/get_select2_bill_no`,
        placeholder:'BILL NO',
    })).on('change', () => trigger_search());
    $("#acc_id").select2(select2_default({
        url:`sales/get_select2_acc_id`,
        placeholder:'CUSTOMER',
    })).on('change', () => trigger_search());
    $("#user_id").select2(select2_default({
        url:`sales/get_select2_user_id`,
        placeholder:'SALES PERSON',
    })).on('change', () => trigger_search());
    $("#from_bill_date, #to_bill_date").on('change', () => trigger_search());

    set_default_style_brand();
});

var sales_cnt = 1;

const set_default_style_brand =()=>{
    $("#style_id").html(`<option value="1">DRESS MATERIAL</option>`);
    $("#brand_id").html(`<option value="1">NA</option>`);
}

const calculate_single_vip_amt = () =>{
    let qty = $("#qty").val();
    if(isNaN(qty) || qty == '') qty = 0;

    let rate = $("#rate").val();
    if(isNaN(rate) || rate == '') rate = 0;
    let total = parseFloat(qty) * parseFloat(rate);
    if(isNaN(total) || total == '') total = 0; 
    $("#sub_total_amt").val(total.toFixed(2));
    if(total > 0){
        $('#trans_block_btn').prop('disabled', false)
    }else{
        $('#trans_block_btn').prop('disabled', true)   
    }        
}
const remove_sales_trans_notifier = () => {
    notifier('design_id')
    notifier('style_id')
    notifier('brand_id')
    notifier('age_id')
    notifier('qty')
    notifier('rate')
    notifier('sub_total_amt')
}
const add_sales_row = id =>{
    remove_sales_trans_notifier()
    remove_sales_master_notifier()
    let check       = true;
    if($("#sm_acc_id").val() == null){
        notifier('sm_acc_id', 'Required')
        check = false;
    }
    if($("#sm_user_id").val() == null){
        notifier('sm_user_id', 'Required')
        check = false;
    }
    if($("#design_id").val() == null){
        notifier('design_id', 'Required')
        check = false;
    }
    if($("#style_id").val() == null){
        notifier('style_id', 'Required')
        check = false;
    }
    if($("#brand_id").val() == null){
        notifier('brand_id', 'Required')
        check = false;
    }
    // if($("#age_id").val() == 0){
    //     notifier('age_id', 'Required')
    //     check = false;
    // }
    if($("#qty").val() <= 0){
        notifier('qty', 'Required')
        check = false;
    }
    if($("#rate").val() <= 0){
        notifier('rate', 'Required')
        check = false;
    }
    if($("#sub_total_amt").val() <= 0){
        notifier('sub_total_amt', 'Required')
        check = false;
    }
    if(!check){
        callToastify('error', 'Oh snap ! You forgot to enter some information', 'right')
        $("body, html").animate({'scrollTop':0},1000);
    }else{
        let path = `master/barcode/add_barcode/`;
        let form_data = {
            design_id : $('#design_id').val(),
            style_id : $('#style_id').val(),
            brand_id : $('#brand_id').val(),
            age_id : $('#age_id').val(),
            qty : $('#qty').val(),
            rate : $('#rate').val(),
        }
        ajaxCall('POST',path,form_data,'JSON',resp=>{
            const{status, data, flag, msg} = resp
                if(status){
                    if(flag == 1){
                        if(data){
                            const newOption = new Option(data.name, data.id, true, true)
                            $(`#bm_id`).append(newOption).trigger('change');
                            $('#design_id').val(null).trigger('change');
                            $('#style_id').val(null).trigger('change');
                            $('#brand_id').val(null).trigger('change');
                            $('#age_id').val(null).trigger('change');
                            $('#rate').val(0);
                            $('#sub_total_amt').val(0);
                            remove_sales_trans_notifier()
                        }
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
const get_walking_id = () => {
    let id = 0;
    let path = `master/account/get_walkin_data`;
    ajaxCall('GET',path,'','JSON',resp=>{
        const{status, data, flag, msg} = resp
        if(status){
            if(flag == 1){
               if(data && data.length != 0){
                    id = data[0]['account_id'];
               }
            }else{
                response_error(flag, msg)
            }
        }else{
            session_expired()
        }
        
        },errmsg=>{
    }, false);
    console.log({id})
    return id;
}
const get_customer_data = () =>{
    let mobile = $('#account_mobile').val();
    if(mobile.length === 10){
        let path = `master/account/get_customer_data/${mobile}`;
        ajaxCall('GET',path,'','JSON',resp=>{
            const{status, data, flag, msg} = resp
            if(status){
                if(flag == 1){
                   if(data && data.length != 0){
                        let {account_id, account_name, account_mobile} = data[0];
                        const newOption = new Option(`${account_name} - ${account_mobile}`, account_id, true, true)
                        $(`#sm_acc_id`).append(newOption).trigger('change')
                        $(`#account_name`).val(account_name)
                   }else{
                        const newOption = new Option('', get_walking_id(), true, true)
                        $(`#sm_acc_id`).append(newOption).trigger('change')
                   }
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
const get_customer_data_with_loyalty = () =>{
    let acc_id = $('#sm_acc_id').val();
    if(acc_id != null){
        let path = `master/account/get_customer_data_with_loyalty/${acc_id}`;  
        ajaxCall('GET',path,'','JSON',resp=>{
            const{status, data, flag, msg} = resp
            if(status){
                if(flag == 1){
                   if(data['acc_data'] && data['acc_data'].length > 0){
                        let today_date  = new Date($('#sm_bill_date').val().split('-').reverse().join('-'))
                        let exp_date    = new Date(data['acc_data'][0]['account_date'])
                        let Days        = Math.floor((today_date.getTime() - exp_date.getTime())/(1000*60*60*24));
                        if(data['acc_data'][0]['account_constant'] != 'WALKIN'){
                            // if(data['allow_disc']){
                            //     if(Days > 0 && Days < 16){
                            //         $('#sm_disc_per').val(0)                
                            //         $('#sm_disc_per').prop('readonly', true)    
                                   
                            //     }else{
                            //         $('#sm_disc_per').val(data['acc_data'][0]['account_disc_per'])  
                            //         $('#sm_disc_per').prop('readonly', false) 
                            //     } 
                            // }else{
                            //     $('#sm_gst_type').val(0) 
                            //     $('#sm_disc_per').val(0)  
                            //     $('#sm_disc_per').prop('readonly', false) 
                            //     $('#sm_promo_per').val(0)               
                            //     $('.loyalty_points_area').hide()
                            //     $('#sm_point_used').val('') 
                            //     $('#sm_point').val('')     
                            // }
                            $('#sm_disc_per').val(data['acc_data'][0]['account_disc_per']); 
                            $('#sm_gst_type').val(data['acc_data'][0]['gst_type']); 
                        }else{ 
                            $('#sm_gst_type').val(0) 
                            $('#sm_disc_per').val(0)  
                            $('#sm_disc_per').prop('readonly', false) 
                            $('#sm_promo_per').val(0)               
                            $('.loyalty_points_area').hide()
                            $('#sm_point_used').val('') 
                            $('#sm_point').val('') 
                        }

                        calculate_master_total(false);

                   }
                }else{
                    response_error(flag, msg)
                }
            }else{
                session_expired()
            }
            
        },errmsg=>{
        });
    }else{
        $('#sm_disc_per, #disc_per').val('')
        $('.loyalty_points_area').hide()
        $('#sm_point_used').val('')
        calculate_master_total();
    }
}
const remove_sales_master_notifier = () =>{
    notifier('sm_acc_id')
    notifier('sm_user_id')
    notifier('sm_bill_no')
    notifier('sm_bill_date')
    notifier('sm_total_qty')
    notifier('sm_sub_total')
    notifier('sm_final_amt')
    notifier('sm_collected_amt')
    notifier('sm_payment_mode')
    notifier('account_mobile')
}
const get_barcode_data = () =>{
    remove_sales_master_notifier()
    let check   = true;
    let check_dup= true;
    let total_tr= $('#sales_material_wrapper tr').length;
    let bm_id   = $('#bm_id').val();
    if($("#sm_acc_id").val() == null){
        notifier('sm_acc_id', 'Required')
        check = false;
    }
    if($("#sm_user_id").val() == null){
        notifier('sm_user_id', 'Required')
        check = false;
    }
    if(bm_id != null){
        if(!check){
            callToastify('error', 'Oh snap ! You forgot to enter some information', 'right')
            $("body, html").animate({'scrollTop':0},1000);
            setTimeout(() =>{
                $('#bm_id').val(null).trigger('change');
                $('#bm_id').select2('open');
            },RELOAD_TIME);
        }else{
            if(total_tr > 0){
                for (let i = 1; i <= total_tr; i++){
                    let id = $('#sales_material_wrapper tr:nth-child('+i+')').attr('id');
                    let lastunderscore = id.lastIndexOf("_");
                    let cnt = id.substring(lastunderscore+1);
                    let old_bm_id   = $('#st_bm_id_'+cnt).val();
                    if(bm_id == old_bm_id){
                        check_dup = false;
                    }
                }
            }
            if(!check_dup){
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
                                let sub_total = parseFloat(data[0]['bm_pt_qty']) * parseFloat(data[0]['bm_sp_amt']); 
                                let total     = parseFloat(sub_total);
                                let trial_area = (data[0]['style_name'] && 
                                    ['readymade', 'kurti'].includes(data[0]['style_name'].toLowerCase())
                                ) ? '' : 'd-none';

                                let tr = `
                                <tr id="rowid_${sales_cnt}" class="floating-form">
                                    <td class="floating-label">
                                        <input type="hidden" name="st_id[]" id="st_id_${sales_cnt}" value="0" />
                                        <input type="hidden" name="st_bm_id[]" id="st_bm_id_${sales_cnt}" value="${data[0]['bm_id']}" />
                                        <input type="number" class="form-control floating-input" id="st_bm_item_code_${sales_cnt}" value="${data[0]['bm_item_code']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="text" class="form-control floating-input" id="design_name_${sales_cnt}" value="${data[0]['design_name']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="text" class="form-control floating-input" id="style_name_${sales_cnt}" value="${data[0]['style_name']}" readonly />
                                        <input type="hidden" name="st_style_id[]" id="st_style_id_${sales_cnt}" value="${data[0]['style_id']}" />

                                        <div style="display: flex; align-items: center; gap: 5px; margin-top: 5px;" class="${trial_area}">
                                            <input type="checkbox" name="st_trial[]" id="st_trial_${sales_cnt}" value="1" />
                                            <label for="st_trial_${sales_cnt}" style="margin: 0;">Trial</label> 
                                        </div> 
                                    </td>
                                    <td class="floating-label">
                                        <input type="text" class="form-control floating-input" id="brand_name_${sales_cnt}" value="${data[0]['brand_name']}" readonly />
                                        <input type="hidden" name="st_brand_id[]" id="st_brand_id_${sales_cnt}" value="${data[0]['brand_id']}" />
                                        
                                        <div style="display: flex; align-items: center; gap: 5px; margin-top: 5px;" class="${trial_area}">
                                            <label for="st_dispatch_date_${sales_cnt}" style="margin: 0;">D.Date</label>
                                            <input type="date" name="st_dispatch_date[]" id="st_dispatch_date_${sales_cnt}" class="form-control  floating-input" />
                                        </div>
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="st_qty[]" id="st_qty_${sales_cnt}" value="${data[0]['bm_pt_qty']}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="st_rate[]" id="st_rate_${sales_cnt}" value="${data[0]['bm_sp_amt']}" readonly />
                                        <input type="number" class="form-control floating-input" name="st_sub_total[]" id="st_sub_total_${sales_cnt}" value="${sub_total}" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="st_disc_per[]" id="st_disc_per_${sales_cnt}" value="0" onkeyup="calculate_single_total(${sales_cnt},true)" />
                                        <input type="number" class="form-control floating-input" name="st_disc_amt[]" id="st_disc_amt_${sales_cnt}" value="0" onkeyup="calculate_single_total(${sales_cnt})"/>
                                        
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="st_taxable_amt[]" id="st_taxable_amt_${sales_cnt}" value="0" readonly />
                                    </td>
                                    <td class="floating-label"> 
                                        <input type="number" class="form-control floating-input" name="st_sgst_per[]" id="st_sgst_per_${sales_cnt}" value="${data[0]['design_sgst_per']}" readonly />
                                        <input type="number" class="form-control floating-input" name="st_cgst_per[]" id="st_cgst_per_${sales_cnt}" value="${data[0]['design_cgst_per']}" readonly />
                                        <input type="number" class="form-control floating-input" name="st_igst_per[]" id="st_igst_per_${sales_cnt}" value="${data[0]['design_igst_per']}" onkeyup="change_gst_per(${sales_cnt})" >
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="st_sgst_amt[]" id="st_sgst_amt_${sales_cnt}" value="0" readonly />
                                        <input type="number" class="form-control floating-input" name="st_cgst_amt[]" id="st_cgst_amt_${sales_cnt}" value="0" readonly />
                                        <input type="number" class="form-control floating-input" name="st_igst_amt[]" id="st_igst_amt_${sales_cnt}" value="0" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <input type="number" class="form-control floating-input" name="st_sub_total_amt[]" id="st_sub_total_amt_${sales_cnt}" value="0" readonly />
                                    </td>
                                    <td class="floating-label">
                                        <button type="button" class="btn btn-primary" href="javascript:void(0)" onclick="edit_row(${sales_cnt})"> 
                                            <i class="text-success fa fa-edit"></i>
                                        </button>
                                    </td>
                                    <td class="floating-label">
                                        <button type="button" class="btn btn-primary" href="javascript:void(0)" onclick="remove_sales_row(${sales_cnt})"> 
                                            <i class="text-danger fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>`;

                            $('#sales_material_wrapper').prepend(tr);
                            callToastify('success', `${data[0]['design_name']} - ${data[0]['brand_name']} ADDED`, 'right')
                            $('#bm_id').val(null).trigger('change');
                            $('#bm_id').select2('open');
                            // calculate_single_total(sales_cnt);
                            // calculate_master_total();
                            calculate_master_from_item_disc(true); 
                            sales_cnt++;                           
                            }else{
                                response_error(0, '')
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
}
const edit_row = cnt => {
    $('#st_disc_per_'+cnt).prop('readonly', false).focus();
    $('#st_disc_amt_'+cnt).prop('readonly', false);
    $('#st_disc_amt_'+cnt).prop('readonly', false);
    $('#st_igst_per_'+cnt).prop('readonly', false);
}

const change_gst_per = cnt => {
    let igst_per = $('#st_igst_per_'+cnt).val();
    if (isNaN(igst_per) || igst_per == "") igst_per = 0;
    $('#st_sgst_per_'+cnt).val(parseFloat(igst_per/2));
    $('#st_cgst_per_'+cnt).val(parseFloat(igst_per/2));
    calculate_single_total(cnt);
}
const disable_row = cnt => {
    $('#st_disc_amt_'+cnt).prop('readonly', true).removeAttr('onkeyup')
}

const set_bill_type = () => {
  calculate_master_total();
  get_entry_no();
};

const calculate_single_total =(cnt,DiscPer = false)=>{
    let qty = $("#st_qty_"+cnt).val() 
    if(isNaN(qty) || qty == '') qty = 0;

    let rate = $("#st_rate_"+cnt).val() 
    if(isNaN(rate) || rate == '') rate = 0;

    let sub_total = parseFloat(qty) * parseFloat(rate);
    if(isNaN(sub_total) || sub_total == '') sub_total = 0; 
    $("#st_sub_total_"+cnt).val(sub_total.toFixed(2));
    
    let disc_per = $("#st_disc_per_"+cnt).val();
    if (isNaN(disc_per) || disc_per == "") disc_per = 0;

    let disc_amt = $("#st_disc_amt_"+cnt).val();
    if(isNaN(disc_amt) || disc_amt == '') disc_amt = 0; 

    if (DiscPer) {
        disc_amt = (parseFloat(sub_total) * parseFloat(disc_per)) / 100;
        if (isNaN(disc_amt) || disc_amt == "") disc_amt = 0;
        if (disc_amt == 0) $("#st_disc_amt_"+cnt).val("");
        if (disc_amt > 0) $("#st_disc_amt_"+cnt).val(disc_amt.toFixed(2));
      } else {
        disc_per = (parseFloat(disc_amt) * 100) / parseFloat(sub_total);
        if (isNaN(disc_per) || disc_per == "") disc_per = 0;
        if (disc_per == 0) $("#st_disc_per_"+cnt).val("");
        if (disc_per > 0) $("#st_disc_per_"+cnt).val(disc_per.toFixed(2));

      }

    let taxable_amt = parseFloat(sub_total) - parseFloat(disc_amt);
    if(isNaN(taxable_amt) || taxable_amt == '') taxable_amt = 0; 
   
    let sgst_per = $("#st_sgst_per_"+cnt).val();
    if (isNaN(sgst_per) || sgst_per == "") sgst_per = 0;

    let cgst_per = $("#st_cgst_per_"+cnt).val();
    if (isNaN(cgst_per) || cgst_per == "") cgst_per = 0;

    let igst_per = $("#st_igst_per_"+cnt).val();
    if (isNaN(igst_per) || igst_per == "") igst_per = 0;
    
    let sgst_amt = 0;
    let cgst_amt = 0;
    let igst_amt = 0;

    if($(`#sm_with_gst`).is(":checked") || $(`#sm_with_gst`).val() > 0)
    {
        if ($(`#sm_bill_type`).is(":checked")) {
          let deduct_amt =
            (parseFloat(taxable_amt) * igst_per) /
            (100 + parseFloat(igst_per));
          if (isNaN(deduct_amt) || deduct_amt == "") deduct_amt = 0;
          taxable_amt = parseFloat(taxable_amt) - parseFloat(deduct_amt);
          if (isNaN(taxable_amt) || taxable_amt == "") taxable_amt = 0;
        } 

        if ($("#sm_gst_type").val() == 0) { 
                // WITHIN
                sgst_amt = (parseFloat(taxable_amt) * parseFloat(sgst_per)) / 100;
                if (isNaN(sgst_amt) || sgst_amt == "") sgst_amt = 0;

                cgst_amt = (parseFloat(taxable_amt) * parseFloat(cgst_per)) / 100;
                if (isNaN(cgst_amt) || cgst_amt == "") cgst_amt = 0;
            } else {
                // OUTSIDE
                igst_amt = (parseFloat(taxable_amt) * parseFloat(igst_per)) / 100;
                if (isNaN(igst_amt) || igst_amt == "") igst_amt = 0;
        }
    }

    $("#st_taxable_amt_"+cnt).val(taxable_amt.toFixed(2)); 
    $("#st_sgst_amt_"+cnt).val(sgst_amt.toFixed(2));
    $("#st_cgst_amt_"+cnt).val(cgst_amt.toFixed(2));
    $("#st_igst_amt_"+cnt).val(igst_amt.toFixed(2));
    
    let total_amt = parseFloat(taxable_amt) + parseFloat(sgst_amt) + parseFloat(cgst_amt) + parseFloat(igst_amt);
    if (isNaN(total_amt) || total_amt == "") total_amt = 0;
    $("#st_sub_total_amt_"+cnt).val(total_amt.toFixed(2));
    calculate_master_total();

}

const calculate_master_total =(item_disc=true)=>{
    let total_tr            = $('#sales_material_wrapper tr').length;
    let total_qty           = 0;
    let total_sub_amt       = 0;
    let total_disc_amt      = 0;

    let total_taxable_amt   = 0;
    let total_sgst_amt      = 0;
    let total_cgst_amt      = 0;
    let total_igst_amt      = 0;

    let total_final_amt     = 0;
    let max_serial_no       = 0;
    let sr_no               = 1;

    for (let i = 1; i <= total_tr; i++) 
    {
        let id = $('#sales_material_wrapper tr:nth-child('+i+')').attr('id');
        let lastunderscore = id.lastIndexOf("_");
        let cnt = id.substring(lastunderscore+1);

        qty = $("#st_qty_"+cnt).val() 
        if(isNaN(qty) || qty == '') qty = 0;

        rate = $("#st_rate_"+cnt).val() 
        if(isNaN(rate) || rate == '') rate = 0;

        sub_total = parseFloat(qty) * parseFloat(rate);
        if(isNaN(sub_total) || sub_total == '') sub_total = 0; 
        $("#st_sub_total_"+cnt).val(sub_total.toFixed(2));
        
        let disc_per = $("#st_disc_per_"+cnt).val(); 
        if(isNaN(disc_per) || disc_per == '') disc_per = 0;

        let disc_amt = $("#st_disc_amt_"+cnt).val(); 
        if(isNaN(disc_amt) || disc_amt == '') disc_amt = 0;
       
         let taxable_amt = parseFloat(sub_total) - parseFloat(disc_amt);
        if(isNaN(taxable_amt) || taxable_amt == '') taxable_amt = 0; 

        let sgst_per = $("#st_sgst_per_"+cnt).val();
        if (isNaN(sgst_per) || sgst_per == "") sgst_per = 0;

        let cgst_per = $("#st_cgst_per_"+cnt).val();
        if (isNaN(cgst_per) || cgst_per == "") cgst_per = 0;

        let igst_per = $("#st_igst_per_"+cnt).val();
        if (isNaN(igst_per) || igst_per == "") igst_per = 0;
        
        let sgst_amt = 0;
        let cgst_amt = 0;
        let igst_amt = 0;

        if($(`#sm_with_gst`).is(":checked") || $(`#sm_with_gst`).val() > 0)
        {
             if ($(`#sm_bill_type`).is(":checked")) {
              let deduct_amt =
                (parseFloat(taxable_amt) * igst_per) /
                (100 + parseFloat(igst_per));
              if (isNaN(deduct_amt) || deduct_amt == "") deduct_amt = 0;
              taxable_amt = parseFloat(taxable_amt) - parseFloat(deduct_amt);
              if (isNaN(taxable_amt) || taxable_amt == "") taxable_amt = 0;
            } 

           
            if ($("#sm_gst_type").val() == 0) { 
                    // WITHIN
                    sgst_amt = (parseFloat(taxable_amt) * parseFloat(sgst_per)) / 100;
                    if (isNaN(sgst_amt) || sgst_amt == "") sgst_amt = 0;

                    cgst_amt = (parseFloat(taxable_amt) * parseFloat(cgst_per)) / 100;
                    if (isNaN(cgst_amt) || cgst_amt == "") cgst_amt = 0;
                } else {
                    // OUTSIDE
                    igst_amt = (parseFloat(taxable_amt) * parseFloat(igst_per)) / 100;
                    if (isNaN(igst_amt) || igst_amt == "") igst_amt = 0;
            }
        }
        
        $("#st_taxable_amt_"+cnt).val(taxable_amt.toFixed(2)); 
        $("#st_sgst_amt_"+cnt).val(sgst_amt.toFixed(2));
        $("#st_cgst_amt_"+cnt).val(cgst_amt.toFixed(2));
        $("#st_igst_amt_"+cnt).val(igst_amt.toFixed(2));
        
        let total_amt = parseFloat(taxable_amt) + parseFloat(sgst_amt) + parseFloat(cgst_amt) + parseFloat(igst_amt);
        if (isNaN(total_amt) || total_amt == "") total_amt = 0;
        $("#st_sub_total_amt_"+cnt).val(total_amt.toFixed(2));           
        
        total_qty    = parseFloat(total_qty) + parseFloat(qty);
        total_sub_amt= parseFloat(total_sub_amt) + parseFloat(sub_total);
        total_disc_amt= parseFloat(total_disc_amt) + parseFloat(disc_amt);
        
        total_taxable_amt= parseFloat(total_taxable_amt) + parseFloat(taxable_amt);
        total_sgst_amt= parseFloat(total_sgst_amt) + parseFloat(sgst_amt);
        total_cgst_amt= parseFloat(total_cgst_amt) + parseFloat(cgst_amt);
        total_igst_amt= parseFloat(total_igst_amt) + parseFloat(igst_amt);
        total_final_amt= parseFloat(total_final_amt) + parseFloat(total_amt);
    }

    $("#sm_total_qty").val(total_qty.toFixed(2));        
    $("#sm_sub_total").val(total_sub_amt.toFixed(2));
    // $("#sm_total_disc").val(total_disc_amt.toFixed(2));
    $("#sm_hidden_disc_amt").val(total_disc_amt.toFixed(2));
    $("#sm_taxable_amt").val(total_taxable_amt.toFixed(2));
    $("#sm_sgst_amt").val(total_sgst_amt.toFixed(2));
    $("#sm_cgst_amt").val(total_cgst_amt.toFixed(2));
    $("#sm_igst_amt").val(total_igst_amt.toFixed(2));
    
    let after_decimal = parseFloat('0.'+total_final_amt.toString().split(".")[1]);
    $("#sm_round_off").val(after_decimal.toFixed(2))
    $("#sm_final_amt").val(Math.round(total_final_amt));   

    let advance_amt = parseFloat($(`#sm_collected_amt`).val());
    if (isNaN(advance_amt) || advance_amt == "") advance_amt = 0;

    let balance_amt = parseFloat(total_final_amt) - (parseFloat(advance_amt));
    if (isNaN(balance_amt) || balance_amt == "") balance_amt = 0;
    $("#sm_balance_amt").val(Math.round(balance_amt));

    if(total_final_amt > 1)
    {
        $('.master_block_btn').prop('disabled', false)
        $('.master_block_btn').removeClass('btn-default').addClass('btn-success')
    }
    else
    {
        $('.master_block_btn').prop('disabled', true)   
        $('.master_block_btn').removeClass('btn-success').addClass('btn-default')            
    } 
    if(item_disc){
        set_item_disc(); 
    }
}

const set_item_disc = () => {
  const amt = parseFloat($("#sm_sub_total").val());
  const disc_amt = parseFloat($("#sm_hidden_disc_amt").val());
  let disc_per = (parseFloat(disc_amt) * 100) / parseFloat(amt);
  if (isNaN(disc_per) || disc_per == "") disc_per = 0;
  $("#sm_disc_per").val(disc_per.toFixed(2));
  $("#sm_total_disc").val(disc_amt.toFixed(2));
};

const calculate_master_from_item_disc = (fromDiscPer = false) => {
  let total_disc_amt = 0;
  let item_disc_per = parseFloat($("#sm_disc_per").val());
  if (isNaN(item_disc_per) || item_disc_per == "") item_disc_per = 0;

  let item_disc_amt = parseFloat($("#sm_total_disc").val());
  if (isNaN(item_disc_amt) || item_disc_amt == "") item_disc_amt = 0;

  let total_amt = parseFloat($("#sm_sub_total").val());
  if (isNaN(total_amt) || total_amt == "") total_amt = 0;

   let total_tr  = $('#sales_material_wrapper tr').length;

    for (let i = 1; i <= total_tr; i++) 
    {
            let id = $('#sales_material_wrapper tr:nth-child('+i+')').attr('id');
            let lastunderscore = id.lastIndexOf("_");
            let cnt = id.substring(lastunderscore+1);
            
            let ot_amt = parseFloat($(`#st_sub_total_${cnt}`).val());
            if (isNaN(ot_amt) || ot_amt == "") ot_amt = 0;

          if (fromDiscPer) {
            let disc_amt = (parseFloat(ot_amt) * parseFloat(item_disc_per)) / 100;
            if (isNaN(disc_amt) || disc_amt == "") disc_amt = 0;

            $(`#st_disc_per_${cnt}`).val(item_disc_per.toFixed(2));
            $(`#st_disc_amt_${cnt}`).val(disc_amt.toFixed(2));

            total_disc_amt = parseFloat(total_disc_amt) + parseFloat(disc_amt);
            if (isNaN(total_disc_amt) || total_disc_amt == "") total_disc_amt = 0;
          } else {
            let stake_per = (parseFloat(ot_amt) * 100) / parseFloat(total_amt);
            if (isNaN(stake_per) || stake_per == "") stake_per = 0;

            let disc_amt =
              (parseFloat(item_disc_amt) * parseFloat(stake_per)) / 100;
            if (isNaN(disc_amt) || disc_amt == "") disc_amt = 0;

            let disc_per = (parseFloat(disc_amt) * 100) / parseFloat(ot_amt);
            if (isNaN(disc_per) || disc_per == "") disc_per = 0;

            $(`#st_disc_per_${cnt}`).val(disc_per.toFixed(2));
            $("#sm_disc_per").val(disc_per.toFixed(2));
            $(`#st_disc_amt_${cnt}`).val(disc_amt.toFixed(2));

            total_disc_amt = parseFloat(total_disc_amt) + parseFloat(disc_amt);
            if (isNaN(total_disc_amt) || total_disc_amt == "") total_disc_amt = 0;
          }
      }
  if (fromDiscPer) {
    $("#sm_total_disc").val(total_disc_amt.toFixed(2));
  }
  calculate_master_total(false);
};


const remove_sales_row = (cnt) =>{
    let style_name = $(`#style_name_${cnt}`).val()
    let brand_name = $(`#brand_name_${cnt}`).val()
    callToastify('success', `${style_name} - ${brand_name} REMOVED`, 'right')
    $("#rowid_"+cnt).detach();  
    calculate_single_total(cnt); 
}
const add_update_sales = id =>{
    event.preventDefault();
    remove_sales_master_notifier()
    var check   = true;
    let total_tr= $('#sales_material_wrapper tr').length;
    if($("#sm_bill_no").val() == ''){
        notifier('sm_bill_no', 'Required')
        check = false;
    }
    if($("#sm_bill_date").val() == ''){
        notifier('sm_bill_date', 'Required')
        check = false;
    }
    if($("#sm_acc_id").val() == null){
        notifier('sm_acc_id', 'Required')
        check = false;
    }
    if($("#sm_user_id").val() == null){
        notifier('sm_user_id', 'Required')
        check = false;
    }
    // if($("#account_mobile").val() != ''){
    //     if($("#account_mobile").val().length !== 10){
    //         notifier('account_mobile', 'Invalid Mobile No')
    //         check = false;
    //     }
    // }
    if($("#sm_total_qty").val() <= 0){
        notifier('sm_total_qty', 'Required')
        check = false;
    }
    if($("#sm_sub_total").val() <= 0){
        notifier('sm_sub_total', 'Required')
        check = false;
    }
    if($("#sm_final_amt").val() <= 0){
        notifier('sm_final_amt', 'Required')
        check = false;
    }
   
    if(!check){
        callToastify('error', 'Oh snap ! You forgot to enter some information', 'right')
        $("body, html").animate({'scrollTop':0},1000);
    }else{
        let path        = `sales/add_update/${id}`;   
        let form_data   = $("#sales_form").serialize();
        ajaxCall('POST',path,form_data,'JSON',resp=>{
            const{status, flag, data, msg} = resp
            if(status){
                if(flag == 1){
                    if(id == 0){
                    }else{
                    }
                    if(data){
                        window.open(`${base_url}/sales?action=print&id=${data.id}`,'_blank','width=1024, height=768');
                    }
                    remove_sales_master_notifier()
                    callToastify('success', msg, 'right')
                    $("body, html").animate({'scrollTop':0},1000);
                    setTimeout(function(){window.location.reload(); },RELOAD_TIME); 
                }else{
                    response_error(flag, msg)
                }
            }else{
                session_expired()
            }         
        },
        errmsg=>{
            
        });
    }
}

const toggle_payment_mode_popup = () => {
    if ($(`#payment_mode_wrapper .right-panel`).hasClass("active")) {
        $(`#payment_mode_wrapper .right-panel `).removeClass("active");
    } else {
        $(`#payment_mode_wrapper .right-panel `).addClass("active");
        let total_amt = $(`#sm_final_amt`).val();
        if (isNaN(total_amt) || total_amt == "") total_amt = 0;
        $(`._total_amt`).html(total_amt);

        let advance_amt = $(`#sm_collected_amt`).val();
        if (isNaN(advance_amt) || advance_amt == "") advance_amt = 0;
        $(`._advance_amt`).html(advance_amt);

        let balance_amt = parseFloat(total_amt) - parseFloat(advance_amt);
        if (isNaN(balance_amt) || balance_amt == "") balance_amt = 0;
        $(`._balance_amt`).html(balance_amt);
    }
};

const get_payment_mode_data = () => {
  let title = `<p>payment mode</p>`;
  let subtitle = `<div class="d-flex justify-content-around">
                      <p class="d-flex flex-column justify-content-around">
                        <span class="pb-1 border-bottom">total amt</span>
                        <span class="_total_amt">0</span>
                      </p>
                      <p class="d-flex flex-column justify-content-around">
                        <span class="pb-1 border-bottom">advance amt</span>
                        <span class="_advance_amt">0</span>
                      </p>
                      <p class="d-flex flex-column justify-content-around">
                        <span class="pb-1 border-bottom">balance amt</span>
                        <span class="_balance_amt">0</span>
                      </p>
                    </div>`;
  let body = ``;
  let footer = `<button 
                    type="button" 
                    id="sbt_btn" 
                    class="btn btn-md btn-secondary btn-block text-uppercase mx-3" 
                    onclick="toggle_payment_mode_popup()"
                >close</button>`;
  $(`#payment_mode_wrapper #right-panel-title`).html(title);
  $(`#payment_mode_wrapper #right-panel-subtitle`).html(subtitle);
  $(`#payment_mode_wrapper #right-panel-body`).html(body);
  $(`#payment_mode_wrapper #right-panel-footer`).html(footer);
    const id = $("#sm_id").val();
     let path        = `sales/get_payment_mode_data/${id}`;   
      ajaxCall( "POST",path, '',"JSON", (resp) => 
      {
          if (handle_response(resp)) {
            const { data, msg } = resp;
            if (data && data.length != 0) {
              const { pay_modes } = get_pay_modes(data);
             let body = `<div class="row pt-2">
                                        <div class="col-12">
                                            <div style="max-height: 50vh; overflow-x: auto;">
                                                <table class="table table-sm w-100">
                                                    <tbody id="payment_mode_tbody">
                                                        ${pay_modes}
                                                    </tbody>
                                                </table>
                                            </div>              
                                        </div>              
                                    </div> `;
              $(`#payment_mode_wrapper #right-panel-body `).html(body);
            }
          }
        },
        (errmsg) => {
          console.log(errmsg);
        }
      );
};

const get_pay_modes = (data) => {
  let pay_modes = ``;
  let advance_amt = 0;
  data.forEach((row) => {
    const { spmt_id, spmt_amt, spmt_payment_mode_id, payment_mode_name } =
      row;
    advance_amt = parseFloat(advance_amt) + parseFloat(spmt_amt);
    if (isNaN(advance_amt) || advance_amt == "") advance_amt = 0;

    pay_modes += `<tr id="rowpm_${spmt_payment_mode_id}">
                      <td width="10%" class="border-0 font-weight-bold"></td>
                      <td width="30%" class="border-0 font-weight-bold">${payment_mode_name} : </td>
                      <td width="50%" class="border-0 floating-label">
                        <input 
                          type="hidden"
                          id="spmt_id_${spmt_payment_mode_id}" 
                          name="spmt_id[${spmt_payment_mode_id}]" 
                          value="${spmt_id}"/>
                        <input 
                          type="hidden"
                          id="spmt_payment_mode_id_${spmt_payment_mode_id}" 
                          name="spmt_payment_mode_id[${spmt_payment_mode_id}]" 
                          value="${spmt_payment_mode_id}" 
                        />
                        <input 
                          type="number" 
                          class="form-control floating-input" 
                          id="spmt_amt_${spmt_payment_mode_id}" 
                          name="spmt_amt[${spmt_payment_mode_id}]" 
                          value="${spmt_amt}"
                          onkeyup="calculate_advance_amt()"
                          placeholder=" " 
                          autocomplete="off" />
                      </td>
                      <td width="10%" class="border-0 font-weight-bold"></td>
                    </tr>`;
  });

  return { advance_amt, pay_modes };
};

const calculate_advance_amt = () => { 
  let advance_amt = 0;
  for (let i = 1; i <= $("#payment_mode_tbody > tr").length; i++) {
    let cnt = $(`#payment_mode_tbody > tr:nth-child(${i})`).attr("id");
    let explode = cnt.split("_");
    let id = explode[1];

    let amt = $(`#spmt_amt_${id}`).val();
    if (isNaN(amt) || amt == "") amt = 0;

    advance_amt = parseFloat(advance_amt) + parseFloat(amt);
    if (isNaN(advance_amt) || advance_amt == "") advance_amt = 0;
  }
  $("._advance_amt").html(advance_amt);
  $(`#sm_collected_amt`).val(advance_amt);

  let total_amt = $(`#sm_final_amt`).val();
  if (isNaN(total_amt) || total_amt == "") total_amt = 0;

  let balance_amt = parseFloat(total_amt) - (parseFloat(advance_amt));
  if (isNaN(balance_amt) || balance_amt == "") balance_amt = 0;
  $("._balance_amt").html(balance_amt);
  $(`#sm_balance_amt`).val(balance_amt);

  if (balance_amt >= 0) {
    // $("#sbt_btn").prop("disabled", false);
    $("._balance_amt").removeClass("text-danger");
  } else {
    // $("#sbt_btn").prop("disabled", true);
    $("._balance_amt").addClass("text-danger");
  }
  calculate_master_total();
};


const get_entry_no = () => {  
  const path = `sales/get_entry_no`; 
  var sm_sales_type = $("#sm_sales_type").val(); 
  var sm_with_gst = ($(`#sm_with_gst`).is(":checked") || $(`#sm_with_gst`).val() > 0) ? 1 : 0
  const form_data = { func: "get_entry_no", id: $("#sm_id").val(), sm_with_gst,sm_sales_type };
  ajaxCall(
    "POST",
    path,
    form_data,
    "JSON",
    (resp) => {
      // if (handle_response(resp)) {
        const { data, msg } = resp;
        if (data && data.length != 0) {
            $("#sm_bill_no").val(data["sm_bill_no"]);
        }
      // }
    },
    (errmsg) => { }
  );
};

const convert_to_order = (id) => { 
    var isConfirmed = confirm('Do you want to convert estimate into Sales?');
      if (isConfirmed) {
        const path = `${sub_link}/add_edit_order/${id}`;
        ajaxCall(
          "POST",path,'',"JSON",
          (resp) => {
              const { msg,flag } = resp;
                if(flag == 1){
                    callToastify('success', msg, 'right')
                    $("body, html").animate({'scrollTop':0},1000);
                    setTimeout(function(){window.location.reload(); },RELOAD_TIME); 
                }else{
                    response_error(flag, msg)
                }
          },
          
          (errmsg) => {}
        );
      }
};
