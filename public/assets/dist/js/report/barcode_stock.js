$(document).ready(function(){
    $("#bm_id").select2(select2_default({
        url:`master/barcode/get_select2`,
        placeholder:'BARCODE',
        barcode:'bm_id',
    })).on('change', () => trigger_search());
    $("#bm_acc_id").select2(select2_default({
        url:`master/barcode/get_select2_acc_id`,
        placeholder:'SUPPLIER',
    })).on('change', () => trigger_search());
    $("#bm_style_id").select2(select2_default({
        url:`master/barcode/get_select2_style_id`,
        placeholder:'STYLE',
    })).on('change', () => trigger_search());
    $("#bm_design_id").select2(select2_default({
        url:`master/barcode/get_select2_design_id`,
        placeholder:'DESIGN',
    })).on('change', () => trigger_search());
    $("#bm_brand_id").select2(select2_default({
        url:`master/barcode/get_select2_brand_id`,
        placeholder:'BRAND',
    })).on('change', () => trigger_search());
    $("#bm_age_id").select2(select2_default({
        url:`master/barcode/get_select2_age_id`,
        placeholder:'AGE GROUP',
    })).on('change', () => trigger_search());
});
const barcode_popup = value => {
    const {bm_id, st_rate, bm_item_code, account_code, style_name, design_name, brand_name, age_name, pt_rate} = value;
    let data = `
                <form class="form-horizontal" id="barcode_form" onsubmit="update_barcode(${bm_id})">              
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-sm table-hover mb-5">
                                <tr>
                                    <td>Barcode</td>
                                    <td>: ${bm_item_code}</td>
                                </tr>
                                <tr>
                                    <td>Supplier</td>
                                    <td>: ${account_code}</td>
                                </tr>
                                <tr>
                                    <td>Style</td>
                                    <td>: ${style_name}</td>
                                </tr>
                                <tr>
                                    <td>design</td>
                                    <td>: ${design_name}</td>
                                </tr>
                                <tr>
                                    <td>Brand</td>
                                    <td>: ${brand_name}</td>
                                </tr>
                                <tr>
                                    <td>Age Group</td>
                                    <td>: ${age_name ? age_name : ''}</td>
                                </tr>
                                <tr>
                                    <td>Purchase Rate</td>
                                    <td>: ${pt_rate}</td>
                                </tr>
                                <tr>
                                    <td>Sale Rate</td>
                                    <td>: ${st_rate}</td>
                                </tr>
                            </table>
                            <div class="d-flex form-group floating-form">
                                <div class="col-8 floating-label">
                                    <input type="number" class="form-control floating-input" name="bm_sp_amt" id="bm_sp_amt" value="${st_rate}" placeholder=" " autocomplete="off" />   
                                    <label for="inputEmail3">NEW SALE RATE <span style="color:red">*</span></label>
                                    <small class="form-text text-muted helper-text" id="bm_sp_amt_msg"></small>
                                </div>
                            </div>              
                        </div>              
                    </div>              
                </form>
               `;

    let btn =`
                <button type="button" class="btn btn-sm btn-primary" id="sbt_btn" onclick="update_barcode(${bm_id})" >UPDATE</button>
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">CANCEL</button>        
             `;
    
    $(".modal-title-sm").html('UPDATE BARCODE');
    $(".modal-body-sm").html(data);
    $('.modal-footer-sm').html(btn)
    $("#popup_modal_sm").modal('show');  
    setTimeout(()=>{
        $('#bm_sp_amt').focus();
    }, RELOAD_TIME)
}
const update_barcode = id =>{
    event.preventDefault();
    let check   = true;
    notifier('bm_sp_amt')
    if($(`#bm_sp_amt`).val() == '' || $(`#bm_sp_amt`).val() <= 0){
        notifier(`bm_sp_amt`, 'Required')
        check = false;
    }
    if(check){
        let path = `report/barcode_stock/update_barcode/${id}`;
        let form_data = $(`#barcode_form`).serialize();
        ajaxCall('POST',path,form_data,'JSON',resp=>{
            notifier('bm_sp_amt')
            const {status, flag, data, msg} = resp;
            if(status){
                if(flag == 1){
                    $("#popup_modal_sm").modal('hide');  
                    callToastify('success', msg, 'right')
                    setTimeout(()=>{
                        window.location.reload();
                        window.open(`${base_url}/master/barcode?action=print&id=${id}`,'_blank','width=1024, height=768');
                    }, RELOAD_TIME)
                }else{
                    response_error(flag, msg)
                }
            }else{
                session_expired()
            }
        },errmsg =>{});
    }
}

// var per_page    = 20;
// var offset      = 0;
// var action      = false;

// const get_barcode_stock = (per_page, offset) =>{
//     const path  = `report/get_barcode_stock/${per_page}/${offset}`;
//     ajax('GET',path,'','JSON',resp=>{
//         const {status, flag, data, msg} = resp;
//         if(status){
//             if(flag == 1){
//                 if(data['data'] && data['data'].length != 0){
//                     let tr = data['data'].map(temp => {
//                         let {bm_item_code, account_code, style_name, design_name, bm_pt_qty, bm_pt_rate, pur_amt, bm_prt_qty, bm_st_qty, bm_sp_amt, sale_amt, bm_srt_qty, sold_amt, bal_qty, bal_amt} = temp;
//                         return  `
//                                     <tr>
//                                         <td width="7%">${bm_item_code}</td>
//                                         <td width="5%">${account_code}</td>
//                                         <td width="5%">${style_name}</td>
//                                         <td width="7%">${design_name}</td>
//                                         <td width="5%">${parseInt(bm_pt_qty)}</td>
//                                         <td width="7%">${bm_pt_rate}</td>
//                                         <td width="7%">${pur_amt}</td>
//                                         <td width="7%">${parseInt(bm_prt_qty)}</td>
//                                         <td width="5%">${parseInt(bm_st_qty)}</td>
//                                         <td width="7%">${bm_sp_amt}</td>
//                                         <td width="7%">${sale_amt}</td>
//                                         <td width="7%">${parseInt(bm_srt_qty)}</td>
//                                         <td width="7%">${sold_amt}</td>
//                                         <td width="7%">${parseInt(bal_qty)}</td>
//                                         <td width="7%">${bal_amt}</td>
//                                     </tr>
//                                 `;
//                     });
//                     $('#table_tbody').append(tr);
//                     action = false;
//                 }else{
//                     $('#load_msg').html('<button class="btn btn-block btn-primary">No More Data.</button>');
//                     action = true;
//                 }   
//             }else{
//                 response_error(flag, msg)
//             }
//         }else{
//             session_expired()
//         }
//     },errmsg =>{}, true);
// }
// $(window).scroll(() => {
//     if($(window).scrollTop() + $(window).height() > $('#table_tbody').height() && !action){
//         action = true;
//         offset = per_page + offset;
//         setTimeout(()=>{
//             get_barcode_stock(per_page, offset)
//         }, RELOAD_TIME); 
//     }
// });