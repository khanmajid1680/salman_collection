$(document).ready(function(){
    $("#id").select2(select2_default({
        url:`master/account/get_select2`,
        placeholder:'NAME - MOBILE',
        param:()=>$('#type').val(),
    })).on('change', () => trigger_search());
   
});
const set_account_field = id => {
    let path = `master/account/get_data/${id}`
    ajaxCall('GET',path,'','JSON',resp=>{
        const {status, flag, data} = resp;
        if(status){
            if(flag == 1){
                if(data){
                    $('#account_name').val(data[0]['account_name'])
                    $('#account_code').val(data[0]['account_code'])
                    $('#account_group_id').val(data[0]['account_group_id'])
                    $('#account_mobile').val(data[0]['account_mobile'])
                    $('#account_tel1').val(data[0]['account_tel1'])
                    $('#account_tel2').val(data[0]['account_tel2'])
                    $('#account_email').val(data[0]['account_email'])
                    $('#account_address').val(data[0]['account_address'])
                    $('#account_city_id').val(data[0]['account_city_id'])
                    $('#account_country_id').val(data[0]['account_country_id'])
                    $('#account_state_id').val(data[0]['account_state_id'])
                    $('#account_gst_no').val(data[0]['account_gst_no'])
                    $('#account_pan_no').val(data[0]['account_pan_no'])
                    $('#account_disc_per').val(data[0]['account_disc_per'])
                    $('#account_credit_days').val(data[0]['account_credit_days'])
                    $('#account_drcr').val(data[0]['account_drcr'])
                    $('#account_open_bal').val(data[0]['account_open_bal'])
                    $('#account_reference').val(data[0]['account_reference'])
                    $('#account_status').bootstrapToggle(data[0][`account_status`] == 1 ? 'on' : 'off');
                    set_mobile_no1('account_mobile')
                    if(data[0]['account_constant'] != ''){
                        $('#account_name').prop('readonly', 'readonly')
                        $('#account_group_id').prop('disabled', true)
                    }
                    if(data[0]['isExist']){
                        // $('#account_name').prop('readonly', 'readonly')
                        $('#account_group_id').prop('disabled', true)
                        $('#account_drcr').prop('disabled', true)
                        // $('#account_open_bal').prop('readonly', true)
                    }
                }else{
                    response_error(0, '')
                }   
            }else{
                response_error(flag, msg)
            }
        }else{
            session_expired();
        }
    },errmsg =>{});
}
const account_popup = (id, type, field = undefined) => {
    let path = `master/account/get_data_for_account/${type}`
    ajaxCall('GET',path,'','JSON',resp=>{
        const {status, flag, data, msg} = resp;
        if(status){
            if(flag == 1){
                if(data){
                    let {cities, states, countries, groups} = data;
                    let c = drop_down(cities)
                    let s = drop_down(states)
                    let cn= drop_down(countries)
                    let g = drop_down(groups)
                    account_popup1(id, type, field, {cities:c, states:s, countries:cn, groups:g})
                }else{
                    response_error(0, '')
                }   
            }else{
                response_error(flag, msg)   
            }
        }else{
            session_expired();
        }
    },errmsg =>{});   
}
const account_popup1 = (id, type, field, resp) =>{
    let {cities, states, countries, groups} = resp;
    let title= id == 0 ? 'ADD' : 'EDIT';
    let data = `
                <form class="form-horizontal" id="account_form" onsubmit="add_update_account(${id}, ${field})">              
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex flex-wrap form-group floating-form">
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-3 floating-label">
                                        <input type="text" id="account_type" name="account_type" value="${type}" class="form-control floating-input" placeholder=" " readonly="readonly" />   
                                        <label for="inputEmail3">TYPE <span style="color:red">*</span></label>
                                        <small class="form-text text-muted helper-text" id="account_type_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-8 col-lg-6 floating-label">
                                        <input type="text" id="account_name" name="account_name" value="" class="form-control floating-input" onkeyup="validate_textfield(this, ${type == SUPPLIER || type == GENERAL})" placeholder=" " autocomplete="off" />   
                                        <label for="inputEmail3">NAME ${type == SUPPLIER || type == GENERAL ? '<span style="color:red">*</span>':''} </label>
                                        <small class="form-text text-muted helper-text" id="account_name_msg"></small>
                                    </div>
                                    ${type == SUPPLIER ?
                                        `<div class="col-12 col-sm-12 col-md-4 col-lg-3 floating-label">
                                            <input type="text" id="account_code" name="account_code" value="" class="form-control floating-input" onkeyup="validate_textfield(this)" placeholder=" " autocomplete="off" />   
                                            <label for="inputEmail3">CODE <span style="color:red">*</span></label>
                                            <small class="form-text text-muted helper-text" id="account_code_msg"></small>
                                        </div>` : '<input type="hidden" id="account_code" name="account_code" value="" />'
                                    }
                                    ${type == GENERAL ?
                                        `<div class="col-12 col-sm-12 col-md-4 col-lg-3 floating-label">
                                            <p for="inputEmail3">GROUP</p>
                                            <select class="form-control floating-select" id="account_group_id" name="account_group_id">
                                            ${groups}
                                            </select>
                                            <small class="form-text text-muted helper-text" id="account_group_id_msg"></small>    
                                        </div>` : `<input type="hidden" id="account_group_id" name="account_group_id" value="${type == CUSTOMER ? 5 : 6}" />`
                                    }
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-3 floating-label">
                                        <input type="number" id="account_mobile" name="account_mobile" value="" class="form-control floating-input" onkeyup="set_mobile_no(this, ${type == CUSTOMER})" onfocusout="validate_mobile_no(this, ${type == CUSTOMER})" placeholder=" " autocomplete="off" />   
                                        <label for="inputEmail3">Mobile ${type == CUSTOMER ? '<span style="color:red">*</span>':''} <span id="account_mobile_length">(10)</span></label>
                                        <small class="form-text text-muted helper-text" id="account_mobile_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-3 floating-label">
                                        <input type="text" id="account_tel1" name="account_tel1" value="" class="form-control floating-input" placeholder=" " autocomplete="off" />   
                                        <label for="inputEmail3">TELEPHONE 1</label>
                                        <small class="form-text text-muted helper-text" id="account_tel1_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-3 floating-label">
                                        <input type="text" id="account_tel2" name="account_tel2" value="" class="form-control floating-input" placeholder=" " autocomplete="off" />   
                                        <label for="inputEmail3">TELEPHONE 2</label>
                                        <small class="form-text text-muted helper-text" id="account_tel2_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-3 floating-label">
                                        <input type="email" id="account_email" name="account_email" value="" class="form-control floating-input" onfocusout="validate_email(this.value)" style="text-transform: lowercase;" placeholder=" " autocomplete="off" />   
                                        <input type="hidden" id="isMailValid" value="1">
                                        <label for="inputEmail3">EMAIL</label>
                                        <small class="form-text text-muted helper-text" id="account_email_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-8 col-lg-6 floating-label">
                                        <textarea class="form-control floating-textarea" id="account_address" name="account_address" placeholder=" " autocomplete="off" rows="3"></textarea>
                                        <label for="inputEmail3">ADDRESS</label>
                                        <small class="form-text text-muted helper-text" id="account_address_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-3 floating-label">
                                        <p for="inputEmail3">CITY</p>
                                        <select class="form-control floating-select" id="account_city_id" name="account_city_id">
                                        ${cities}
                                        </select>
                                        <small class="form-text text-muted helper-text" id="account_city_id_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-3 floating-label">
                                        <p for="inputEmail3">STATE</p>
                                        <select class="form-control floating-select" id="account_state_id" name="account_state_id">
                                        ${states}
                                        </select>
                                        <small class="form-text text-muted helper-text" id="account_state_id_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-3 floating-label">
                                        <p for="inputEmail3">COUNTRY</p>
                                        <select class="form-control floating-select" id="account_country_id" name="account_country_id">
                                        ${countries}
                                        </select>
                                        <small class="form-text text-muted helper-text" id="account_country_id_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-3 floating-label">
                                        <input type="text" id="account_gst_no" name="account_gst_no" value="" class="form-control floating-input" placeholder=" " autocomplete="off" />   
                                        <label for="inputEmail3">GST NO</label>
                                        <small class="form-text text-muted helper-text" id="account_gst_no_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-3 floating-label">
                                        <input type="text" id="account_pan_no" name="account_pan_no" value="" class="form-control floating-input" placeholder=" " autocomplete="off" />   
                                        <label for="inputEmail3">PAN NO</label>
                                        <small class="form-text text-muted helper-text" id="account_pan_no_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-3 floating-label">
                                        <input type="number" id="account_disc_per" name="account_disc_per" value="" class="form-control floating-input" placeholder=" " autocomplete="off" />   
                                        <label for="inputEmail3">DISC PER</label>
                                        <small class="form-text text-muted helper-text" id="account_disc_per_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-3 floating-label">
                                        <input type="number" id="account_credit_days" name="account_credit_days" value="" class="form-control floating-input" placeholder=" " autocomplete="off" />   
                                        <label for="inputEmail3">CREDIT DAYS</label>
                                        <small class="form-text text-muted helper-text" id="account_credit_days_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-3 floating-label">
                                        <p for="inputEmail3">CR/DR</p>
                                        <select class="form-control floating-select" id="account_drcr" name="account_drcr">
                                            <option value="CR">CR</option>
                                            <option value="DR">DR</option>
                                        </select>
                                        <small class="form-text text-muted helper-text" id="account_drcr_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-3 floating-label">
                                        <input type="number" id="account_open_bal" name="account_open_bal" value="" class="form-control floating-input" placeholder=" " autocomplete="off" />   
                                        <label for="inputEmail3">OPENING BALANCE</label>
                                        <small class="form-text text-muted helper-text" id="account_open_bal_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-3 floating-label">
                                        <input type="text" id="account_reference" name="account_reference" value="" class="form-control floating-input" placeholder=" " autocomplete="off" />   
                                        <label for="inputEmail3">REFER BY</label>
                                        <small class="form-text text-muted helper-text" id="account_reference_msg"></small>
                                    </div>
                                    ${field == undefined ? `<div class="col-4 floating-label">
                                        <input type="checkbox" id="account_status" name="account_status" data-toggle="toggle" data-on="ACTIVE" data-off="INACTIVE" data-onstyle="primary" data-offstyle="primary" data-width="100" data-size="normal" checked>
                                    </div>` : `<input type="hidden" name="account_status" value="1">` }
                                </div>              
                            </div>              
                        </div>              
                    </div>
                </form>
               `;
    $(".modal-body-lg").html(data);
    $(".modal-title-lg").html(`${title} ${type}`);

    let btn =`
                <button type="button" class="btn btn-primary" id="sbt_btn" ${id == 0 && 'disabled'} onclick="add_update_account(${id}, ${field})" >${title}</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">CANCEL</button>        
             `;
    $('.modal-footer-lg').html(btn)
    $("#popup_modal_lg").modal('show');  
    setTimeout(()=>{
        $('#account_name').focus();
    },RELOAD_TIME);
    if(id != 0){
        set_account_field(id);
    }else{
        $('#account_city_id').val(1)
        $('#account_state_id').val(1)
        $('#account_country_id').val(1)
        $('#account_drcr').val(type == CUSTOMER ? 'DR' : 'CR')
    }
    $('#account_status').bootstrapToggle()
}
const remove_account_notifier = () =>{
    notifier('account_type')
    notifier('account_name')
    notifier('account_code')
    notifier('account_email')
    notifier('account_mobile')

}
const add_update_account = (id, field) =>{
    event.preventDefault();
    let check = true;
    let type  = $("#account_type").val();
    if($("#account_type").val() == ''){
        notifier('account_type', 'Required')
        check = false;
    }
    if(type == SUPPLIER || type == GENERAL){
        if($("#account_name").val() == ''){
            notifier('account_name', 'Required')
            check = false;
        }
    }
    if(type == SUPPLIER){
        if($("#account_code").val() == ''){
            notifier('account_code', 'Required')
            check = false;
        }
    }
    if($("#isMailValid").val() == 0){
        notifier('account_email', 'Invalid Email')
        check = false;
    }
    if(type == CUSTOMER){
        if($("#account_mobile").val() == ''){
            notifier('account_mobile', 'Required')
            check = false;
        }else{
            if($("#account_mobile").val().length > 0){
                if($("#account_mobile").val().length !== 10){
                    notifier('account_mobile', 'Invalid Mobile No')
                    check = false;    
                }
            }
        }
    }

    if(!check){
        callToastify('error', 'Oh snap ! You forgot to enter some information', 'right')
        $("body, html").animate({'scrollTop':0},1000);
    }else{
        let path = `master/account/add_update/${id}`;
        let form_data = $("#account_form").serialize();
        ajaxCall('POST',path,form_data,'JSON',resp=>{
            const {status, data, flag, msg} = resp
            if(status){
                if(flag == 1){
                    if(id == 0){
                        if(field != undefined){
                            $("#popup_modal_lg").modal('hide');  
                            refresh_dropdown_select2(data, field)
                        }else{
                            $("#account_form")[0].reset();
                            $("#account_name").focus();
                        }
                    }else{
                        $("#popup_modal_lg").modal('hide');  
                    }
                    remove_account_notifier()
                    callToastify('success', msg, 'right')
                    $("body, html").animate({'scrollTop':0},1000);
                }else{
                    response_error(flag, msg)
                }
            }else{
                session_expired()
            }

        },errmsg =>{});
    }
}
const get_user_role = () => {
    let path = `master/user/get_role`;
    let role = ADMIN;
    ajaxCall('GET',path,'','JSON',resp=>{
        const {status, flag, data} = resp;
        if(status){
            if(flag == 1){
                role = data;
            }else{
                response_error(flag, msg)
            }
        }else{
            session_expired();
        }
    },errmsg =>{}, false);
    return role;
}
const customer_popup = (id, type, field) =>{
    let title= 'ADD';
    let role = get_user_role();
    let data = `
                <form class="form-horizontal" id="account_form" onsubmit="add_customer(${id}, ${field})">              
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex flex-wrap form-group floating-form">
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                                        <input type="hidden" id="account_type" name="account_type" value="${type}" />
                                        <input type="hidden" id="account_group_id" name="account_group_id" value="${type == CUSTOMER ? 5 : 6}" />
                                        <input type="hidden" id="account_drcr" name="account_drcr" value="DR" />
                                        <input type="hidden" name="account_status" value="1">
                                        <input type="text" id="account_name" name="account_name" value="" class="form-control floating-input" placeholder=" " onkeyup="validate_textfield(this, ${false})" autocomplete="off" />   
                                        <label for="inputEmail3">NAME</label>
                                        <small class="form-text text-muted helper-text" id="account_name_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                                        <input type="number" id="account_mobile" name="account_mobile" value="" class="form-control floating-input" placeholder=" " onkeyup="set_mobile_no(this, ${type == CUSTOMER})" onfocusout="validate_mobile_no(this, ${type == CUSTOMER})" autocomplete="off" />   
                                        <label for="inputEmail3">Mobile <span style="color:red">*</span><span id="account_mobile_length">(10)</span></label>
                                        <small class="form-text text-muted helper-text" id="account_mobile_msg"></small>
                                    </div>
                                    ${role == ADMIN ? 
                                        `<div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                                            <input type="number" id="account_disc_per" name="account_disc_per" value="" class="form-control floating-input" placeholder=" " autocomplete="off" />   
                                            <label for="inputEmail3">DISC PER</label>
                                            <small class="form-text text-muted helper-text" id="account_disc_per_msg"></small>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                                            <input type="number" id="account_open_bal" name="account_open_bal" value="" class="form-control floating-input" placeholder=" " autocomplete="off" />   
                                            <label for="inputEmail3">OPENING BALANCE</label>
                                            <small class="form-text text-muted helper-text" id="account_open_bal_msg"></small>
                                        </div>` : ''
                                    }
                                </div>              
                            </div>              
                        </div>              
                    </div>
                </form>
               `;
    let btn =`
        <button type="button" class="btn btn-primary" id="sbt_btn" ${id == 0 && 'disabled'} onclick="add_customer(${id}, ${field})" >${title}</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">CANCEL</button>        
    `;
    $(".modal-body-sm").html(data);
    $(".modal-title-sm").html(`${title} ${type}`);
    $('.modal-footer-sm').html(btn)
    $("#popup_modal_sm").modal('show');  
    setTimeout(()=>{
        $('#account_name').focus();
    },RELOAD_TIME);
}
const add_customer = (id, field) =>{
    event.preventDefault();
    remove_account_notifier()
    let check = true;
    // if($("#account_name").val() == ''){
    //     notifier('account_name', 'Required')
    //     check = false;
    // }
    if($("#account_mobile").val() == ''){
        notifier('account_mobile', 'Required')
        check = false;    
    }else{
        if($("#account_mobile").val().length > 0){
            if($("#account_mobile").val().length !== 10){
                notifier('account_mobile', 'Invalid Mobile No')
                check = false;    
            }
        }
    }

    if(!check){
        callToastify('error', 'Oh snap ! You forgot to enter some information', 'right')
        $("body, html").animate({'scrollTop':0},1000);
    }else{
        let path = `master/account/add_customer/${id}`;
        let form_data = $("#account_form").serialize();
        ajaxCall('POST',path,form_data,'JSON',resp=>{
            const {status, data, flag, msg} = resp
            if(status){
                if(flag == 1){
                    if(id == 0){
                        if(field != undefined){
                            $("#popup_modal_lg").modal('hide');  
                            refresh_dropdown_select2(data, field)
                        }
                    }
                    $("#popup_modal_sm").modal('hide');  
                    remove_account_notifier()
                    callToastify('success', msg, 'right')
                }else{
                    response_error(flag, msg)
                }
            }else{
                session_expired()
            }

        },errmsg =>{});
    }
}