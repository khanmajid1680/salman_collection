$(document).ready(function(){
    $("#bm_id").select2(select2_default({
        url:`master/barcode/get_select2`,
        placeholder:'BARCODE',
        barcode:'bm_id',
        multiple:true
    }));
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
    const {bm_id, st_rate, bm_item_code, account_code, style_name, design_name, brand_name, age_name, pt_rate,bm_token_check,bm_token_amt} = value;
   
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
                                    <td>: ${st_rate} 
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a type="button" class="btn btn-sm btn-primary" target="_blank" href="${base_url}/purchase?action=single_print&clause=bm.bm_id&id=${bm_id}">
                                            <i class="text-info fa fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <div class="d-flex form-group floating-form">
                                <div class="col-8 floating-label">
                                    <input type="number" class="form-control floating-input" name="bm_sp_amt" id="bm_sp_amt" value="${st_rate}" placeholder=" " autocomplete="off" />   
                                    <label for="inputEmail3">NEW SALE RATE <span style="color:red">*</span></label>
                                    <small class="form-text text-muted helper-text" id="bm_sp_amt_msg"></small>
                                </div>
                            </div> 
                            <div class="d-flex form-group floating-form">
                                <div class="col-1 floating-label">
                                   <input type="checkbox" style="width: 30px; height: 30px" name="bm_token_check" value="1" ${(bm_token_check)>0 ? 'checked' : ''}>
                                </div>
                                <div class="col-8 floating-label">
                                    <input type="number" class="form-control floating-input" name="bm_token_amt" id="bm_token_amt" value="${bm_token_amt}" placeholder=" " autocomplete="off" />   
                                    <small class="form-text text-muted helper-text" id="bm_token_amt_msg"></small>
                                    <label for="inputEmail3">TOKEN</label>
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
                    callToastify('success', msg, 'right');
                    // setTimeout(()=>{
                        // window.location.reload();
                        window.open(`${base_url}/purchase?action=single_print&clause=bm.bm_id&id=${id}`,'_blank','width=1024, height=768');
                    // }, RELOAD_TIME)
                }else{
                    response_error(flag, msg)
                } 
            }else{
                session_expired()
            }
        },errmsg =>{});
    }
}

const multiple_barcode_popup = () => {
    // const checkboxes = document.querySelectorAll('input[name="barcode_id[]"]');
    const checkboxes = document.querySelectorAll('input[name^="barcode_id["]');
    let anyChecked = false;
    checkboxes.forEach((checkbox) => {
        if (checkbox.checked) {
            anyChecked = true;
        }
    });
    if (!anyChecked) {
        response_error(2, "Please select at least one barcode.");
        return false;
    }

    let data = `
                <form class="form-horizontal" id="barcode_form" onsubmit="update_barcode()">              
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex form-group floating-form">
                                <div class="col-8 floating-label">
                                    <input type="number" class="form-control floating-input" name="bm_sp_amt" id="bm_sp_amt" value="''" placeholder=" " autocomplete="off" />   
                                    <label for="inputEmail3">NEW SALE RATE <span style="color:red">*</span></label>
                                    <small class="form-text text-muted helper-text" id="bm_sp_amt_msg"></small>
                                </div>
                            </div> 
                            <div class="d-flex form-group floating-form">
                                <div class="col-1 floating-label">
                                   <input type="checkbox" style="width: 30px; height: 30px" name="bm_token_check" value="1" >
                                </div>
                                <div class="col-8 floating-label">
                                    <input type="number" class="form-control floating-input" name="bm_token_amt" id="bm_token_amt" value="" placeholder=" " autocomplete="off" />   
                                    <small class="form-text text-muted helper-text" id="bm_token_amt_msg"></small>
                                    <label for="inputEmail3">TOKEN</label>
                                </div>
                            </div>              
                        </div>              
                    </div>              
                </form>
               `;

    let btn =`
                <button type="button" class="btn btn-sm btn-primary" id="sbt_btn" onclick="update_multiple_barcode()" >UPDATE</button>
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

const update_multiple_barcode = () =>{
    event.preventDefault();
    let check   = true;
    if(check){
        let path = `report/barcode_stock/update_multiple_barcode`;
        var form_data = $('#barcode_form').serialize() + '&' + $('#stock_form').serialize();
        ajaxCall('POST',path,form_data,'JSON',resp=>{
            const {status, flag, data, msg} = resp;
            if(status){
                if(flag == 1){
                    $("#popup_modal_sm").modal('hide');  
                    callToastify('success', msg, 'right');
                    // setTimeout(()=>{
                    //     window.location.reload();
                    // }, RELOAD_TIME)

                     window.open(`${base_url}/purchase?action=single_print&clause=bm.bm_id&id=${data}`,'_blank','width=1024, height=768');
                     
                }else{
                    response_error(flag, msg)
                } 
            }else{
                session_expired()
            }
        },errmsg =>{});
    }
} 

const render = (data, page) => {  
    let sr_no = page + 1;
    // let sr_no = PER_PAGE * page + 1;

    // let content = data.map((data, index) => {
        const {
            bm_id,
            bm_item_code,
            account_code,
            style_name,
            design_name,
            brand_name,
            pt_qty,
            pt_rate,
            pt_amt,
            prt_qty,
            st_qty,
            st_rate,
            st_amt,
            srt_qty,
            bal_qty,
            bal_amt,
            profit_amt,
            token,

        } = data;

        var content =`<tr>
                    <td width="3%">${sr_no}</td>
                    <td width="10%">`;
                    if(bal_qty !=0){      
                        content +=`<input type="checkbox" name="barcode_id[${bm_id}]" id="barcode_id_${bm_id}" style="height: 20px; width: 20px">`;
                    }    

                    content +=`<a target="_blank" href="${base_url}/purchase?action=single_print&clause=bm.bm_id&id=${bm_id}" data-toggle="tooltip" data-placement="bottom" title="PRINT">
                            ${bm_item_code}
                        </a>        
                        <br/>                               
                        <a class="mr-5" target="_blank" href="${base_url}master/barcode?action=edit&id=${bm_id}" data-toggle="tooltip" data-placement="bottom" title="PURCHASE">
                            <i class="fa fa-eye"></i>
                        </a>`;

                   if(bal_qty !=0){     
                        content +=`<a  href="#" onclick='barcode_popup(${JSON.stringify(data)})' data-toggle="tooltip" data-placement="bottom" title="SINGLE">
                            <i class="fa fa-edit"></i>
                        </a>`;  
                    }   

                content +=`</td>
                   <td width="8%">${account_code}</td>
                    <td width="7%">${style_name}</td>
                    <td width="7%">${design_name}</td>
                    <td width="7%">${brand_name}</td>
                    <td width="5%">${pt_qty}</td>
                    <td width="5%">${pt_rate}</td>
                    <td width="5%">${pt_amt}</td>
                    <td width="5%">${prt_qty}</td>
                    <td width="5%">${st_qty}</td>
                    <td width="5%">${st_rate}</td>
                    <td width="5%">${st_amt}</td>
                    <td width="5%">${srt_qty}</td>
                    <td width="5%">${bal_qty}</td>
                    <td width="5%">${bal_amt}</td>
                    <td width="5%">${profit_amt}</td>
                    <td width="5%">${token}</td>
                    
                </tr>`;
    // });

    $("#report_wrapper").append(content);
    $("a").tooltip();
};

