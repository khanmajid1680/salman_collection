$(document).ready(function(){
    $("#pm_acc_id").select2(select2_default({
        url:`master/account/get_select2_supplier`,
        placeholder:'NAME - CODE- MOBILE',
    })).on("change", (event) => get_supplier_data(event.target.value));

    $("#entry_no").select2(select2_default({
        url:`purchase/get_select2_entry_no`,
        placeholder:'ENTRY NO',
    })).on('change', () => trigger_search());
    $("#bill_no").select2(select2_default({
        url:`purchase/get_select2_bill_no`,
        placeholder:'BILL NO',
    })).on('change', () => trigger_search());
    $("#acc_id").select2(select2_default({
        url:`purchase/get_select2_acc_id`,
        placeholder:'SUPPLIER',
    })).on('change', () => trigger_search());
    
    $("#design_id").select2(select2_default({
        url:`master/design/get_select2`,
        placeholder:'DESIGN',
        param:true,
    })).on("change", (event) => get_design_data(event.target.value));
    $("#style_id").select2(select2_default({
        url:`master/style/get_select2`,
        placeholder:'STYLE',
        param:true,
    }));

    $(".style_selec2").select2(select2_default({
        url:`master/style/get_select2`,
        placeholder:'STYLE',
        param:true,
    }));

    $("#brand_id").select2(select2_default({
        url:`master/brand/get_select2`,
        placeholder:'BRAND',
        param:true,
    }));

    $("#hsn_id").select2(select2_default({
        url:`master/hsn/get_select2`,
        placeholder:'HSN',
        param:true,
    }));

    $("#from_entry_date, #to_entry_date, #from_bill_date, #to_bill_date").on('change', () => trigger_search());
    set_default_style_brand();
});

const set_default_style_brand =()=>{
    $("#style_id").html(`<option value="1">DRESS MATERIAL</option>`);
    $("#brand_id").html(`<option value="1">NA</option>`);
}
var purchase_cnt    = 1;

const get_supplier_data = (id) => {
  $("#pm_gst_type").val(0);
  if (id) {
    const path = `purchase/get_supplier_data/${id}`;
    ajaxCall(
      "POST",
      path,
      '',
      "JSON",
      (resp) => {
          const { data, msg } = resp;
          $("#pm_gst_type").val(data);
          calculate_master_total();
      },
      (errmsg) => {}
    );
  }
};

const get_design_data = (id) => { 
  $("#sgst_per").val(0);
  $("#cgst_per").val(0);
  $("#igst_per").val(0);
  if (id) {
    const path = `master/design/get_data/${id}`;
    ajaxCall("POST",path,'',"JSON",(resp) => {
        if (resp['status']) {
          const { data, msg } = resp;
          if (data && data.length != 0) {
            $("#sgst_per").val(data[0]["design_sgst_per"]);
            $("#cgst_per").val(data[0]["design_cgst_per"]);
            $("#igst_per").val(data[0]["design_igst_per"]);
            calculate_single_amt(); 
          }
        }
      },
      (errmsg) => {}
    );
  }
};

const calculate_single_amt = () =>{
    // console.log(cnt);
    let qty = $("#qty").val();
    if(isNaN(qty) || qty == '') qty = 0;

    let rate = $("#rate").val();
    if(isNaN(rate) || rate == '') rate = 0;

    let sp_amt = $("#sp_amt").val();
    if(isNaN(sp_amt) || sp_amt == '') sp_amt = 0;
    $("#mrp").val(sp_amt);

    let sp_per = ((parseFloat(sp_amt) - parseFloat(rate)) * 100) / parseFloat(rate);
    if(isNaN(sp_per) || sp_per == '') sp_per = 0; 
    $("#sp_per").val(sp_per.toFixed(2));

    let sub_total = parseFloat(qty) * parseFloat(rate);
    if(isNaN(sub_total) || sub_total == '') sub_total = 0; 
    $("#sub_total").val(sub_total.toFixed(2));

    let disc_amt = $("#disc_amt").val();
    if(isNaN(disc_amt) || disc_amt == '') disc_amt = 0;

    let disc_per = (parseFloat(disc_amt) * 100) / parseFloat(sub_total);
    if(isNaN(disc_per) || disc_per == '') disc_per = 0; 
    $("#disc_per").val(disc_per.toFixed(2));

    let taxable_amt = parseFloat(sub_total) - parseFloat(disc_amt);
    if (isNaN(taxable_amt) || taxable_amt == "") taxable_amt = 0;
        
    let sgst_per = $("#sgst_per").val();
    if(isNaN(sgst_per) || sgst_per == '') sgst_per = 0;

    let cgst_per = $("#cgst_per").val();
    if(isNaN(cgst_per) || cgst_per == '') cgst_per = 0;

    let igst_per = $("#igst_per").val();
    if(isNaN(igst_per) || igst_per == '') igst_per = 0;

    let sgst_amt = 0;
    let cgst_amt = 0;
    let igst_amt = 0;

      if ($("#pm_gst_type").val() == 0) { 
        // WITHIN
        sgst_amt = (parseFloat(taxable_amt) * parseFloat(sgst_per)) / 100;
        if (isNaN(sgst_amt) || sgst_amt == "") sgst_amt = 0;
        sgst_amt = sgst_amt.toFixed(2);

        cgst_amt = (parseFloat(taxable_amt) * parseFloat(cgst_per)) / 100;
        if (isNaN(cgst_amt) || cgst_amt == "") cgst_amt = 0;
        cgst_amt = cgst_amt.toFixed(2);
      } else {
        // OUTSIDE
        igst_amt = (parseFloat(taxable_amt) * parseFloat(igst_per)) / 100;
        if (isNaN(igst_amt) || igst_amt == "") igst_amt = 0;
        igst_amt = igst_amt.toFixed(2);
      }
      $(`#taxable_amt`).val(taxable_amt);
      $(`#sgst_amt`).val(sgst_amt);
      $(`#cgst_amt`).val(cgst_amt);
      $(`#igst_amt`).val(igst_amt);

    let total_amt = parseFloat(taxable_amt) + parseFloat(sgst_amt) + parseFloat(cgst_amt) + parseFloat(igst_amt);
    if (isNaN(total_amt) || total_amt == "") total_amt = 0;
    total_amt = total_amt.toFixed(2);
    $(`#sub_total_amt`).val(total_amt);

    if(total_amt > 0)
    {
        $('#trans_block_btn').prop('disabled', false)
    }
    else
    {
        $('#trans_block_btn').prop('disabled', true)   
    }        
}
const calculate_single_amt_by_sp_per = () =>{
    let qty = $("#qty").val();
    if(isNaN(qty) || qty == '') qty = 0;

    let rate = $("#rate").val();
    if(isNaN(rate) || rate == '') rate = 0;

    let sp_per = $("#sp_per").val();
    if(isNaN(sp_per) || sp_per == '') sp_per = 0;

    let sp_amt = (parseFloat(sp_per) * parseFloat(rate)) / 100;
    if(isNaN(sp_amt) || sp_amt == '') sp_amt = 0; 
    sp_amt = parseFloat(sp_amt) + parseFloat(rate);
    sp_amt = Math.round(sp_amt)
    $("#sp_amt").val(sp_amt.toFixed(2));
    $("#mrp").val(sp_amt.toFixed(2));
    
    let sub_total = parseFloat(qty) * parseFloat(rate);
    if(isNaN(sub_total) || sub_total == '') sub_total = 0; 
    $("#sub_total").val(sub_total.toFixed(2));

    let disc_amt = $("#disc_amt").val();
    if(isNaN(disc_amt) || disc_amt == '') disc_amt = 0;

    let disc_per = (parseFloat(disc_amt) * 100) / parseFloat(sub_total);
    if(isNaN(disc_per) || disc_per == '') disc_per = 0; 
    $("#disc_per").val(disc_per.toFixed(2));

    
    let taxable_amt = parseFloat(sub_total) - parseFloat(disc_amt);
    if (isNaN(taxable_amt) || taxable_amt == "") taxable_amt = 0;
        
    let sgst_per = $("#sgst_per").val();
    if(isNaN(sgst_per) || sgst_per == '') sgst_per = 0;

    let cgst_per = $("#cgst_per").val();
    if(isNaN(cgst_per) || cgst_per == '') cgst_per = 0;

    let igst_per = $("#igst_per").val();
    if(isNaN(igst_per) || igst_per == '') igst_per = 0;

    let sgst_amt = 0;
    let cgst_amt = 0;
    let igst_amt = 0;

      if ($("#pm_gst_type").val() == 0) { 
        // WITHIN
        sgst_amt = (parseFloat(taxable_amt) * parseFloat(sgst_per)) / 100;
        if (isNaN(sgst_amt) || sgst_amt == "") sgst_amt = 0;
        sgst_amt = sgst_amt.toFixed(2);

        cgst_amt = (parseFloat(taxable_amt) * parseFloat(cgst_per)) / 100;
        if (isNaN(cgst_amt) || cgst_amt == "") cgst_amt = 0;
        cgst_amt = cgst_amt.toFixed(2);
      } else {
        // OUTSIDE
        igst_amt = (parseFloat(taxable_amt) * parseFloat(igst_per)) / 100;
        if (isNaN(igst_amt) || igst_amt == "") igst_amt = 0;
        igst_amt = igst_amt.toFixed(2);
      }
      $(`#taxable_amt`).val(taxable_amt);
      $(`#sgst_amt`).val(sgst_amt);
      $(`#cgst_amt`).val(cgst_amt);
      $(`#igst_amt`).val(igst_amt);

     let total_amt = parseFloat(taxable_amt) + parseFloat(sgst_amt) + parseFloat(cgst_amt) + parseFloat(igst_amt);
    if (isNaN(total_amt) || total_amt == "") total_amt = 0;
    total_amt = total_amt.toFixed(2);
    $(`#sub_total_amt`).val(total_amt);  

    if(total_amt > 0)
    {
        $('#trans_block_btn').prop('disabled', false)
    }
    else
    {
        $('#trans_block_btn').prop('disabled', true)   
    }        
}
const calculate_single_amt_by_disc_per = () =>{
    let qty = $("#qty").val();
    if(isNaN(qty) || qty == '') qty = 0;

    let rate = $("#rate").val();
    if(isNaN(rate) || rate == '') rate = 0;

    let sp_amt = $("#sp_amt").val();
    if(isNaN(sp_amt) || sp_amt == '') sp_amt = 0;

    let sp_per = ((parseFloat(sp_amt) - parseFloat(rate)) * 100) / parseFloat(rate);
    if(isNaN(sp_per) || sp_per == '') sp_per = 0; 
    $("#sp_per").val(sp_per.toFixed(2));

    let sub_total = parseFloat(qty) * parseFloat(rate);
    if(isNaN(sub_total) || sub_total == '') sub_total = 0; 
    $("#sub_total").val(sub_total.toFixed(2));

    let disc_per = $("#disc_per").val();
    if(isNaN(disc_per) || disc_per == '') disc_per = 0;

    let disc_amt = (parseFloat(disc_per) * parseFloat(sub_total)) / 100;
    if(isNaN(disc_amt) || disc_amt == '') disc_amt = 0; 
    disc_amt = Math.round(disc_amt);
    $("#disc_amt").val(disc_amt.toFixed(2));

    let total = parseFloat(sub_total) - parseFloat(disc_amt);
    if(isNaN(total) || total == '') total = 0; 
    $("#sub_total_amt").val(total.toFixed(2));

    if(total > 0)
    {
        $('#trans_block_btn').prop('disabled', false)
    }
    else
    {
        $('#trans_block_btn').prop('disabled', true)   
    }        
}
const generate_cp_code = rate =>{
    let code = ''
    let len = rate.toString().length
    for(let i = 0; i < len; i++)
    {
        let char = rate.toString().charAt(i)
        if(char == 1)
        {
            code += 'K'
        }
        else if(char == '2')
        {
            code += 'A'
        }
        else if(char == '3')
        {
            code += 'E'
        }
        else if(char == '4')
        {
            code += 'G'
        }
        else if(char == '5')
        {
            code += 'M'
        }
        else if(char == '6')
        {
            code += 'Y'
        }
        else if(char == '7')
        {
            code += 'Z'
        }
        else if(char == '8')
        {
            code += 'R'
        }
        else if(char == '9')
        {
            code += 'D'
        }
        else if(char == '0')
        {
            code += 'H'
        }
        else
        {
            code += '.'   
        }   
    }
    return code;
}
const calculate_master_total = () =>{
    let total_tr            = $('#purchase_material_wrapper tr').length;
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
        let id = $('#purchase_material_wrapper tr:nth-child('+i+')').attr('id');
        let lastunderscore = id.lastIndexOf("_");
        let cnt = id.substring(lastunderscore+1);

        let qty = $("#pt_qty_"+cnt).val() 
        if(isNaN(qty) || qty == '') qty = 0;

        let rate = $("#pt_rate_"+cnt).val() 
        if(isNaN(rate) || rate == '') rate = 0;

        let sp_per = $("#pt_sp_per_"+cnt).val() 
        if(isNaN(sp_per) || sp_per == '') sp_per = 0;
        
        let sp_amt = (parseFloat(sp_per) * parseFloat(rate)) / 100;
        if(isNaN(sp_amt) || sp_amt == '') sp_amt = 0; 
        sp_amt = parseFloat(sp_amt) + parseFloat(rate);
        sp_amt = Math.round(sp_amt)
        $("#pt_sp_amt_"+cnt).val(sp_amt.toFixed(2));
        
        let sub_total = parseFloat(qty) * parseFloat(rate);
        if(isNaN(sub_total) || sub_total == '') sub_total = 0; 
        $("#pt_sub_total_"+cnt).val(sub_total.toFixed(2));

        let disc_per = $("#pt_disc_per_"+cnt).val();
        if(isNaN(disc_per) || disc_per == '') disc_per = 0;
        
        let disc_amt = (parseFloat(disc_per) * parseFloat(sub_total)) / 100;            
        if(isNaN(disc_amt) || disc_amt == '') disc_amt = 0; 
        disc_amt = Math.round(disc_amt);
        $("#pt_disc_amt_"+cnt).val(disc_amt.toFixed(2));

        let taxable_amt = parseFloat(sub_total) - parseFloat(disc_amt);
        if (isNaN(taxable_amt) || taxable_amt == "") taxable_amt = 0;

        let sgst_per = $("#pt_sgst_per_"+cnt).val();
        if(isNaN(sgst_per) || sgst_per == '') sgst_per = 0;

        let cgst_per = $("#pt_cgst_per_"+cnt).val();
        if(isNaN(cgst_per) || cgst_per == '') cgst_per = 0;

        let igst_per = $("#pt_igst_per_"+cnt).val();
        if(isNaN(igst_per) || igst_per == '') igst_per = 0;

          let sgst_amt = 0;
          let cgst_amt = 0;
          let igst_amt = 0;

          if ($("#pm_gst_type").val() == 0) { 
            // WITHIN
            sgst_amt = (parseFloat(taxable_amt) * parseFloat(sgst_per)) / 100;
            if (isNaN(sgst_amt) || sgst_amt == "") sgst_amt = 0;
            sgst_amt = sgst_amt.toFixed(2);

            cgst_amt = (parseFloat(taxable_amt) * parseFloat(cgst_per)) / 100;
            if (isNaN(cgst_amt) || cgst_amt == "") cgst_amt = 0;
            cgst_amt = cgst_amt.toFixed(2);
          } else {
            // OUTSIDE
            igst_amt = (parseFloat(taxable_amt) * parseFloat(igst_per)) / 100;
            if (isNaN(igst_amt) || igst_amt == "") igst_amt = 0;
            igst_amt = igst_amt.toFixed(2);
          }
          $(`#pt_taxable_amt_${cnt}`).val(taxable_amt);
          $(`#pt_sgst_amt_${cnt}`).val(sgst_amt);
          $(`#pt_cgst_amt_${cnt}`).val(cgst_amt);
          $(`#pt_igst_amt_${cnt}`).val(igst_amt);

        let total_amt = parseFloat(taxable_amt) + parseFloat(sgst_amt) + parseFloat(cgst_amt) + parseFloat(igst_amt);
        if (isNaN(total_amt) || total_amt == "") total_amt = 0;
        total_amt = total_amt.toFixed(2);
        $(`#pt_sub_total_amt_${cnt}`).val(total_amt);            
       
        total_qty    = parseFloat(total_qty) + parseFloat(qty);
        total_sub_amt= parseFloat(total_sub_amt) + parseFloat(sub_total);
        total_disc_amt= parseFloat(total_disc_amt) + parseFloat(disc_amt);
        
        total_taxable_amt= parseFloat(total_taxable_amt) + parseFloat(taxable_amt);
        total_sgst_amt= parseFloat(total_sgst_amt) + parseFloat(sgst_amt);
        total_cgst_amt= parseFloat(total_cgst_amt) + parseFloat(cgst_amt);
        total_igst_amt= parseFloat(total_igst_amt) + parseFloat(igst_amt);

        total_final_amt= parseFloat(total_final_amt) + parseFloat(total_amt);
    }

    $("#pm_total_qty").val(total_qty.toFixed(2));        
    $("#pm_sub_total").val(total_sub_amt.toFixed(2));
    $("#pm_total_disc").val(total_disc_amt.toFixed(2));

    $("#pm_taxable_amt").val(total_taxable_amt.toFixed(2));
    $("#pm_sgst_amt").val(total_sgst_amt.toFixed(2));
    $("#pm_cgst_amt").val(total_cgst_amt.toFixed(2));
    $("#pm_igst_amt").val(total_igst_amt.toFixed(2));

    let after_decimal = parseFloat('0.'+total_final_amt.toString().split(".")[1]);

    $("#pm_round_off").val(after_decimal.toFixed(2))

    let bill_disc = $("#pm_bill_disc").val();
    if(isNaN(bill_disc) || bill_disc == '') bill_disc = 0;

    let gst_amt = $("#pm_gst_amt").val();
    if(isNaN(gst_amt) || gst_amt == '') gst_amt = 0;

    total_final_amt = (parseFloat(total_final_amt) + parseFloat(gst_amt)) - parseFloat(bill_disc); 
    $("#pm_final_amt").val(Math.round(total_final_amt));        
    
    if(total_final_amt > 0)
    {
        $('.master_block_btn').prop('disabled', false)
    }
    else
    {
        $('.master_block_btn').prop('disabled', true)
    } 
    set_serial_no() 
}
const set_serial_no = () => {
    let total_tr= $('#purchase_material_wrapper tr').length;
    let sr_no   = total_tr;   
    for (let i = 1; i <= total_tr; i++){
        let id = $('#purchase_material_wrapper tr:nth-child('+i+')').attr('id');
        let lastunderscore = id.lastIndexOf("_");
        let cnt = id.substring(lastunderscore+1);
        $("#pt_serial_no_"+cnt).val(sr_no);
        sr_no = total_tr - i;
    }
}
const check_for_duplicate_serial_no = cntCheck =>{
    let total_tr    = $('#purchase_material_wrapper tr').length;
    for (let i = 1; i <= total_tr; i++) 
    {
        let id = $('#purchase_material_wrapper tr:nth-child('+i+')').attr('id');
        let lastunderscore = id.lastIndexOf("_");
        let cnt = id.substring(lastunderscore+1);

        if(cntCheck != cnt)
        {
            if($('#pt_serial_no_'+cntCheck).val() == $('#pt_serial_no_'+cnt).val())
            {
                alert('Serial No already exists!')
                $("#pt_serial_no_"+cnt).css({'border-color':'red'});
                $("#pt_serial_no_"+cnt).addClass('shake');
                $('#pt_serial_no_'+cnt).on('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e)
                {
                    $('#pt_serial_no_'+cnt).delay(200).removeClass('shake');
                });    
                $('.master_block_btn').prop('disabled', true)   
                return 0;              
            }
            else if($('#pt_serial_no_'+cntCheck).val() == 0)
            {
                alert('Serial No should be greater than zero!')
                $('.master_block_btn').prop('disabled', true)  
                return 0;               
            }
            else
            {
                $('.master_block_btn').prop('disabled', false)
                $('#pt_serial_no_'+cntCheck).css({'border-color':'#757575'});                     
            }
        }
    }
}
const remove_purchase_trans_notifier = () => {
    notifier('pm_bill_date')
    notifier('pm_bill_no')
    notifier('pm_acc_id')
    notifier('design_id')
    notifier('style_id')
    notifier('brand_id')
    notifier('qty')
    notifier('rate')
    notifier('sub_total')
    notifier('sub_total_amt')

}
const add_purchase_row = id =>{ 
    remove_purchase_trans_notifier()
    let check       = true;
    let dup_check   = true;
    let total_tr    = $('#purchase_material_wrapper tr').length;
    if($("#pm_bill_no").val() == ''){
        notifier('pm_bill_no', 'Required')
        check = false;
    }
    if($("#pm_bill_date").val() == ''){
        notifier('pm_bill_date', 'Required')
        check = false;
    }
    if($("#pm_acc_id").val() == null){
        notifier('pm_acc_id', 'Required')
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
    // if($("#brand_id").val() == null){
    //     notifier('brand_id', 'Required')
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
    if($("#sub_total").val() <= 0){
        notifier('sub_total', 'Required')
        check = false;
    }
    if($("#sub_total_amt").val() <= 0){
        notifier('sub_total_amt', 'Required')
        check = false;
    }
    if(total_tr > 0){
        let new_design_id   = $('#design_id').val();
        let new_style_id    = $('#style_id').val();
        let new_brand_id    = $('#brand_id').val();
       
        let new_rate        = parseFloat($('#rate').val());
        for (let i = 1; i <= total_tr; i++) 
        {
            let id = $('#purchase_material_wrapper tr:nth-child('+i+')').attr('id');
            let lastunderscore = id.lastIndexOf("_");
            let cnt = id.substring(lastunderscore+1);

            let old_design_id   = $('#pt_design_id_'+cnt).val();
            let old_style_id    = $('#pt_style_id_'+cnt).val();
         
            let old_brand_id    = $('#pt_brand_id_'+cnt).val(); 
            let old_rate        = parseFloat($('#pt_rate_'+cnt).val()); 
            if(new_design_id == old_design_id && new_style_id == old_style_id && new_brand_id == old_brand_id && new_rate == old_rate)
            {
                notifier('design_id', 'Already added.')
                notifier('style_id', 'Already added.')
                notifier('brand_id', 'Already added.')
               
                // dup_check = false;
            }

        }
    }
    if(!check){
        callToastify('error', 'Oh snap ! You forgot to enter some information', 'right')
        $("body, html").animate({'scrollTop':0},1000);
    }else if(!dup_check){
        callToastify('error', 'Duplicate item found!', 'right')
        $("body, html").animate({'scrollTop':0},1000);
    }else{
        /*append row*/
        let serial_no   = 0; 
        let design_id   = $('#design_id').val() 
        let design_name = $('#design_id :selected').text() 
        let style_id    = $('#style_id').val() 
        let style_name  = $('#style_id :selected').text() 
        let brand_id    = $('#brand_id').val() 
        let brand_name  = $('#brand_id :selected').text() 
        
        let hsn_id    = $('#hsn_id').val() 
        let hsn_name  = $('#hsn_id :selected').text() 

        let desc        = $('#desc').val()
        let qty         = $('#qty').val() 
        let rate        = $('#rate').val() 
        let sp_amt      = $('#sp_amt').val() 
        let sp_per      = $('#sp_per').val() 
        let sub_total   = $('#sub_total').val() 
        let disc_amt    = $('#disc_amt').val() 
        let disc_per    = $('#disc_per').val() 

        let taxable_amt = $('#taxable_amt').val()
        let sgst_per = $('#sgst_per').val()
        let sgst_amt = $('#sgst_amt').val()
        let cgst_per = $('#cgst_per').val()
        let cgst_amt = $('#cgst_amt').val() 
        let igst_per = $('#igst_per').val()
        let igst_amt = $('#igst_amt').val()

        let sub_total_amt= $('#sub_total_amt').val() 
        let mrp         = $('#mrp').val()
        // let token_amt  = $('#token_amt').val() 

        let cp_code     = generate_cp_code(rate) 
        
        let data =`
            <tr id="rowid_${purchase_cnt}" class="floating-form">
                <td class="floating-label">
                    <input type="hidden" name="pt_id[]" id="pt_id_${purchase_cnt}" value="0"/>
                    <input type="number" class="form-control floating-input" id="pt_serial_no_${purchase_cnt}" name="pt_serial_no[]" value="${serial_no}" readonly />
                </td>
                <td class="floating-label">
                    <input type="text" class="form-control floating-input" value="${design_name}" readonly />
                    <input type="hidden" id="pt_design_id_${purchase_cnt}" name="pt_design_id[]" value="${design_id}"/>
                </td>
                <td class="floating-label">
                    <input type="text" class="form-control floating-input" id="style_name_${purchase_cnt}" value="${style_name}" readonly />
                    <input type="hidden" id="pt_style_id_${purchase_cnt}" name="pt_style_id[]" value="${style_id}"/>
                </td>
                <td class="floating-label">
                    <input type="text" class="form-control floating-input" id="brand_name_${purchase_cnt}" value="${brand_name}" readonly />
                    <input type="hidden" id="pt_brand_id_${purchase_cnt}" name="pt_brand_id[]" value="${brand_id}"/>
                </td>
                <td class="floating-label">
                    <input type="text" class="form-control floating-input" id="hsn_name_${purchase_cnt}" value="${hsn_name}" readonly />
                    <input type="hidden" id="pt_hsn_id_${purchase_cnt}" name="pt_hsn_id[]" value="${hsn_id}"/>
                </td>
              
                <td class="floating-label">
                    <input type="text" class="form-control floating-input" id="pt_desc_${purchase_cnt}" name="pt_desc[]" value="${desc}" readonly />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" id="pt_qty_${purchase_cnt}" name="pt_qty[]" value="${qty}" readonly />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" id="pt_rate_${purchase_cnt}" name="pt_rate[]" value="${rate}" onkeyup="calculate_master_total()" onfocusout="set_cp_code(${purchase_cnt})" />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input mb-2" id="pt_sp_amt_${purchase_cnt}" name="pt_sp_amt[]" value="${sp_amt}" onkeyup="calculate_master_total1()" />
                    <input type="number" class="form-control floating-input" id="pt_sp_per_${purchase_cnt}" name="pt_sp_per[]" value="${sp_per}" onkeyup="calculate_master_total()" />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" id="pt_sub_total_${purchase_cnt}" name="pt_sub_total[]" value="${sub_total}" readonly />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input mb-2" id="pt_disc_amt_${purchase_cnt}" name="pt_disc_amt[]" value="${disc_amt}" onkeyup="calculate_master_total1()" />
                    <input type="number" class="form-control floating-input" id="pt_disc_per_${purchase_cnt}" name="pt_disc_per[]" value="${disc_per}" onkeyup="calculate_master_total()" />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" id="pt_taxable_amt_${purchase_cnt}" name="pt_taxable_amt[]" value="${taxable_amt}" readonly />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" id="pt_sgst_per_${purchase_cnt}" name="pt_sgst_per[]" value="${sgst_per}" readonly />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" id="pt_sgst_amt_${purchase_cnt}" name="pt_sgst_amt[]" value="${sgst_amt}" readonly />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" id="pt_cgst_per_${purchase_cnt}" name="pt_cgst_per[]" value="${cgst_per}" readonly />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" id="pt_cgst_amt_${purchase_cnt}" name="pt_cgst_amt[]" value="${cgst_amt}" readonly />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" id="pt_igst_per_${purchase_cnt}" name="pt_igst_per[]" value="${igst_per}" readonly />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" id="pt_igst_amt_${purchase_cnt}" name="pt_igst_amt[]" value="${igst_amt}" readonly />
                </td>
                <td class="floating-label">
                    <input type="number" class="form-control floating-input" id="pt_sub_total_amt_${purchase_cnt}" name="pt_sub_total_amt[]" value="${sub_total_amt}" readonly />
                </td>
                <td class="floating-label">
                    <input type="text" class="form-control floating-input" id="pt_cp_code_${purchase_cnt}" name="pt_cp_code[]" value="${cp_code}" readonly />
                </td>
                <td class="floating-label">
                    <input type="text" class="form-control floating-input" id="pt_mrp_${purchase_cnt}" name="pt_mrp[]" value="${mrp}" />
                </td>
               
                <td class="text-center floating-label">
                    <button type="button" class="btn btn-sm btn-primary" onclick="remove_purchase_row(${purchase_cnt})"><i class="text-danger fa fa-trash"></i></button>
                </td>
            </tr>
        `;
        $("#purchase_material_wrapper").prepend(data);
        callToastify('success', `${style_name} - ${brand_name} ADDED`, 'right')

        purchase_cnt++;
        remove_purchase_trans_notifier()
        // $("#design_id").focus();
        $("#design_id").val(null).trigger('change');
        // $("#brand_id").val(0)
        $("#desc").val('')
        $("#qty").val(0)
        $("#rate").val(0)
        $("#sp_amt").val(0)
        $("#sp_per").val(0)
        $("#sub_total").val(0)
        $("#disc_amt").val(0)
        $("#disc_per").val(0)
        $("#sgst_per").val(0)
        $("#cgst_per").val(0)
        $("#igst_per").val(0)
        $("#sub_total_amt").val(0)
        $("#mrp").val(0)
        $('#trans_block_btn').prop('disabled', true);
        remove_purchase_trans_notifier();  
        calculate_single_amt();
        calculate_master_total();
    }
}
const remove_purchase_row = cnt =>{
    let style_name = $(`#style_name_${cnt}`).val()
    let design_name = $(`#design_name_${cnt}`).val()
    callToastify('success', `${style_name} - ${design_name} REMOVED`, 'right')
    $("#rowid_"+cnt).detach();  
    calculate_master_total() 
}
const remove_purchase_master_notifier = () => {
    notifier('pm_bill_no')
    notifier('pm_bill_date')
    notifier('pm_acc_id')
    notifier('pm_total_qty')
    notifier('pm_sub_total')
    notifier('pm_final_amt')
}
const add_update_purchase = id =>{
    event.preventDefault();
    remove_purchase_master_notifier()
    var check   = true;
    let cntCheck= true;
    let total_tr= $('#purchase_material_wrapper tr').length;
    if($("#pm_bill_no").val() == ''){
        notifier('pm_bill_no', 'Required')
        check = false;
    }
    if($("#pm_bill_date").val() == ''){
        notifier('pm_bill_date', 'Required')
        check = false;
    }
    if(id == 0){
        if($("#pm_acc_id").val() == null){
            notifier('pm_acc_id', 'Required')
            check = false;
        }    
    }    
    if($("#pm_total_qty").val() <= 0){
        notifier('pm_total_qty', 'Required')
        check = false;
    }

    if($("#pm_sub_total").val() <= 0){
        notifier('pm_sub_total', 'Required')
        check = false;
    }
    if($("#pm_final_amt").val() <= 0){
        notifier('pm_final_amt', 'Required')
        check = false;
    }
    if(total_tr > 0)
    {
        for (let i = 1; i <= total_tr; i++) 
        {
            let id = $('#purchase_material_wrapper tr:nth-child('+i+')').attr('id');
            let lastunderscore = id.lastIndexOf("_");
            let cnt = id.substring(lastunderscore+1);
            if(check_for_duplicate_serial_no(cnt) == 0)
            {
                cntCheck = false
            }
        }
    }else{
        check = false
    }
    if(!check){
        callToastify('error', 'Oh snap ! You forgot to enter some information', 'right')
        $("body, html").animate({'scrollTop':0},1000);
    }else if(!cntCheck){
        callToastify('error', 'Check entries!', 'right')
        $("body, html").animate({'scrollTop':0},1000);
    }else{
        let path        = `purchase/add_update/${id}`;   
        let form_data   = $("#purchase_form").serialize();
        ajaxCall('POST',path,form_data,'JSON',resp=>{
            const{status, flag, data, msg} = resp
            if(status){
                if(flag == 1){
                    if(id == 0){
                    }else{

                    }
                    if(data){
                        window.open(`${base_url}/purchase?action=print&id=${data.id}`,'_blank','width=1024, height=768');
                    }
                    remove_purchase_master_notifier()
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
            console.log(errmsg);
        });
    }
}
const set_cp_code = cnt => {
    let rate = $(`#pt_rate_${cnt}`).val()
    let code = generate_cp_code(rate);
    $(`#pt_cp_code_${cnt}`).val(code);
}
const calculate_master_total1 = () =>{
    let total_tr            = $('#purchase_material_wrapper tr').length;
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
        let id = $('#purchase_material_wrapper tr:nth-child('+i+')').attr('id');
        let lastunderscore = id.lastIndexOf("_");
        let cnt = id.substring(lastunderscore+1);

        let qty = $("#pt_qty_"+cnt).val() 
        if(isNaN(qty) || qty == '') qty = 0;

        let rate = $("#pt_rate_"+cnt).val() 
        if(isNaN(rate) || rate == '') rate = 0;

        let sp_amt = $("#pt_sp_amt_"+cnt).val() 
        if(isNaN(sp_amt) || sp_amt == '') sp_amt = 0;
        
        let sp_per = (parseFloat(sp_amt) * 100) / parseFloat(rate);
        if(isNaN(sp_per) || sp_per == '') sp_per = 0; 
        sp_per = parseFloat(sp_per) - 100;
        $("#pt_sp_per_"+cnt).val(sp_per.toFixed(2));
        
        let sub_total = parseFloat(qty) * parseFloat(rate);
        if(isNaN(sub_total) || sub_total == '') sub_total = 0; 
        $("#pt_sub_total_"+cnt).val(sub_total.toFixed(2));

        let disc_amt = $("#pt_disc_amt_"+cnt).val();
        if(isNaN(disc_amt) || disc_amt == '') disc_amt = 0;
        
        let disc_per = (parseFloat(disc_amt) * 100) / parseFloat(sub_total);            
        if(isNaN(disc_per) || disc_per == '') disc_per = 0; 
        $("#pt_disc_per_"+cnt).val(disc_per.toFixed(2)); 

        let taxable_amt = parseFloat(sub_total) - parseFloat(disc_amt);
        if (isNaN(taxable_amt) || taxable_amt == "") taxable_amt = 0;

        let sgst_per = $("#pt_sgst_per_"+cnt).val();
        if(isNaN(sgst_per) || sgst_per == '') sgst_per = 0;

        let cgst_per = $("#pt_cgst_per_"+cnt).val();
        if(isNaN(cgst_per) || cgst_per == '') cgst_per = 0;

        let igst_per = $("#pt_igst_per_"+cnt).val();
        if(isNaN(igst_per) || igst_per == '') igst_per = 0;

          let sgst_amt = 0;
          let cgst_amt = 0;
          let igst_amt = 0;

          if ($("#pm_gst_type").val() == 0) { 
            // WITHIN
            sgst_amt = (parseFloat(taxable_amt) * parseFloat(sgst_per)) / 100;
            if (isNaN(sgst_amt) || sgst_amt == "") sgst_amt = 0;
            sgst_amt = sgst_amt.toFixed(2);

            cgst_amt = (parseFloat(taxable_amt) * parseFloat(cgst_per)) / 100;
            if (isNaN(cgst_amt) || cgst_amt == "") cgst_amt = 0;
            cgst_amt = cgst_amt.toFixed(2);
          } else {
            // OUTSIDE
            igst_amt = (parseFloat(taxable_amt) * parseFloat(igst_per)) / 100;
            if (isNaN(igst_amt) || igst_amt == "") igst_amt = 0;
            igst_amt = igst_amt.toFixed(2);
          }
          
          $(`#pt_taxable_amt_${cnt}`).val(taxable_amt);
          $(`#pt_sgst_amt_${cnt}`).val(sgst_amt);
          $(`#pt_cgst_amt_${cnt}`).val(cgst_amt);
          $(`#pt_igst_amt_${cnt}`).val(igst_amt);

        let total_amt = parseFloat(taxable_amt) + parseFloat(sgst_amt) + parseFloat(cgst_amt) + parseFloat(igst_amt);
        if (isNaN(total_amt) || total_amt == "") total_amt = 0;
        total_amt = total_amt.toFixed(2);
        $(`#pt_sub_total_amt_${cnt}`).val(total_amt);
        
        total_qty    = parseFloat(total_qty) + parseFloat(qty);
        total_sub_amt= parseFloat(total_sub_amt) + parseFloat(sub_total);
        total_disc_amt= parseFloat(total_disc_amt) + parseFloat(disc_amt);
        
        total_taxable_amt= parseFloat(total_taxable_amt) + parseFloat(taxable_amt);
        total_sgst_amt= parseFloat(total_sgst_amt) + parseFloat(sgst_amt);
        total_cgst_amt= parseFloat(total_cgst_amt) + parseFloat(cgst_amt);
        total_igst_amt= parseFloat(total_igst_amt) + parseFloat(igst_amt);

        total_final_amt= parseFloat(total_final_amt) + parseFloat(total_amt);
    }

    $("#pm_total_qty").val(total_qty.toFixed(2));        
    $("#pm_sub_total").val(total_sub_amt.toFixed(2));
    $("#pm_total_disc").val(total_disc_amt.toFixed(2));
    
    $("#pm_taxable_amt").val(total_taxable_amt.toFixed(2));
    $("#pm_sgst_amt").val(total_sgst_amt.toFixed(2));
    $("#pm_cgst_amt").val(total_cgst_amt.toFixed(2));
    $("#pm_igst_amt").val(total_igst_amt.toFixed(2));

    let after_decimal = parseFloat('0.'+total_final_amt.toString().split(".")[1]);

    $("#pm_round_off").val(after_decimal.toFixed(2))

    let bill_disc = $("#pm_bill_disc").val();
    if(isNaN(bill_disc) || bill_disc == '') bill_disc = 0;

    let gst_amt = $("#pm_gst_amt").val();
    if(isNaN(gst_amt) || gst_amt == '') gst_amt = 0;

    total_final_amt = (parseFloat(total_final_amt) + parseFloat(gst_amt)) - parseFloat(bill_disc); 
    $("#pm_final_amt").val(Math.round(total_final_amt));        
    
    if(total_final_amt > 0)
    {
        $('.master_block_btn').prop('disabled', false)
    }
    else
    {
        $('.master_block_btn').prop('disabled', true)
    } 
    set_serial_no() 
}


const change_gst_per = cnt => {
    let igst_per = $('#pt_igst_per_'+cnt).val();
    if (isNaN(igst_per) || igst_per == "") igst_per = 0;
    $('#pt_sgst_per_'+cnt).val(parseFloat(igst_per/2));
    $('#pt_cgst_per_'+cnt).val(parseFloat(igst_per/2));
    calculate_master_total();
}