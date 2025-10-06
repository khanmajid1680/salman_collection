$(document).ready(function(){
    $("#id").select2(select2_default({
        url:`master/person/get_select2`,
        placeholder:'SALES PERSON - MOBILE',
    })).on('change', () => trigger_search());
    $("#branchh").select2(select2_default({
        url:`master/branch/get_select2`,
        placeholder:'BRANCH',
    })).on('change', () => trigger_search());
});
const set_person_field = id => {
    const term  = "user";
    const path  = `master/person/get_data/${id}`
    ajaxCall('GET',path,'','JSON',resp=>{
        const {status, flag, data, msg} = resp;
        if(status){
            if(flag == 1){
                if(data){
                    $(`#${term}_fullname`).val(data[0][`${term}_fullname`])
                    $(`#${term}_branch_id`).val(data[0][`${term}_branch_id`])
                    $(`#${term}_mobile`).val(data[0][`${term}_mobile`])
                    $(`#${term}_email`).val(data[0][`${term}_email`])
                    $(`#${term}_address`).val(data[0][`${term}_address`])
                    $(`#${term}_status`).bootstrapToggle(data[0][`${term}_status`] == 1 ? 'on' : 'off');
                }else{
                 response_error(0, msg)
                }   
            }else{
                response_error(flag, msg)
            }
        }else{
            session_expired()
        }
    },errmsg =>{});
}
const person_popup = (id, field = undefined) => {
    let path = `master/person/get_data_for_user`
    ajaxCall('GET',path,'','JSON',resp=>{
        const {status, flag, data, msg} = resp;
        if(status){
            if(flag == 1){
                if(data){
                    let {roles, branches} = data;
                    let r = drop_down(roles)
                    let b = drop_down(branches)
                    user_popup1(id, field, {roles:r, branches:b})
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
const user_popup1 = (id, field, resp) =>{
    let {roles, branches} = resp;
    const term  = "user";
    let title= id == 0 ? 'ADD' : 'EDIT';
    let data = `
                <form class="form-horizontal" id="${term}_form" onsubmit="add_update_person(${id}, ${field})">              
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex flex-wrap form-group floating-form">
                                <div class="col-6 floating-label">
                                    <input type="hidden" id="user_role" name="user_role" value="OTHER">
                                    <input type="text" name="${term}_fullname" class="form-control floating-input" id="${term}_fullname" onkeyup="validate_textfield(this)" placeholder=" " autocomplete="off" />   
                                    <label for="inputEmail3">FULLNAME <span style="color:red">*</span></label>
                                    <small class="form-text text-muted helper-text" id="${term}_fullname_msg"></small>
                                </div>
                                <div class="col-6 floating-label">
                                    <p for="inputEmail3">BRANCH <span style="color:red">*</span></p>
                                    <select class="form-control floating-select" id="${term}_branch_id" name="${term}_branch_id" onchange="validate_dropdown(this)">
                                        ${branches}
                                    </select>
                                    <small class="form-text text-muted helper-text" id="${term}_branch_id_msg"></small>
                                </div>
                               <div class="col-6 floating-label">
                                    <input type="number" id="${term}_mobile" name="${term}_mobile" value="" class="form-control floating-input" placeholder=" " onkeyup="set_mobile_no(this)" onfocusout="validate_mobile_no(this, 6)" autocomplete="off" />   
                                    <label for="inputEmail3">MOBILE NO. <span id="${term}_mobile_length">(10)</span></label>
                                    <small class="form-text text-muted helper-text" id="${term}_mobile_msg"></small>
                                </div>
                                <div class="col-6 floating-label">
                                    <input type="email" id="${term}_email" name="${term}_email" value="" class="form-control floating-input" onfocusout="validate_email(this.value)" style="text-transform: lowercase;" placeholder=" " autocomplete="off" />   
                                    <input type="hidden" id="isMailValid" value="1">
                                    <label for="inputEmail3">EMAIL</label>
                                    <small class="form-text text-muted helper-text" id="${term}_email_msg"></small>
                                </div>
                                <div class="col-12 col-sm-12 col-md-8 col-lg-6 floating-label">
                                    <textarea class="form-control floating-textarea" id="${term}_address" name="${term}_address" placeholder=" " autocomplete="off"></textarea>
                                    <label for="inputEmail3">ADDRESS</label>
                                </div>
                                ${field == undefined && id != 1 ? `<div class="col-4 floating-label">
                                    <input type="checkbox" id="${term}_status" name="${term}_status" data-toggle="toggle" data-on="ACTIVE" data-off="INACTIVE" data-onstyle="primary" data-offstyle="primary" data-width="100" data-size="normal" checked>
                                </div>` : `<input type="hidden" name="${term}_status" value="1">` }                            
                            </div>              
                        </div>              
                    </div>              
                </form>
               `;

    let btn =`
                <button type="button" class="btn btn-sm btn-primary" id="sbt_btn" ${id == 0 && 'disabled'} onclick="add_update_person(${id}, ${field})" >${title}</button>
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">CANCEL</button>        
             `;
    
    $(".modal-title-sm").html(`${title} SALES PERSON`);
    $(".modal-body-sm").html(data);
    $('.modal-footer-sm').html(btn)
    $("#popup_modal_sm").modal('show');  
    
    setTimeout(()=>{
        $(`#${term}_fullname`).focus();
    },RELOAD_TIME);

    if(id != 0){
        set_person_field(id);
    }
    $(`#${term}_status`).bootstrapToggle()
}
const remove_person_notifier = term => {
    notifier(`${term}_fullname`)
    notifier(`${term}_branch_id`)
    notifier(`${term}_mobile`)
    notifier(`${term}_email`)
}
const add_update_person = (id, field) =>{
    event.preventDefault();
    const term  = "user";
    let check   = true;
    remove_person_notifier(term);
    if($(`#${term}_fullname`).val() == ''){
        notifier(`${term}_fullname`, 'Required')
        check = false;
    }
    if($(`#${term}_branch_id`).val() == 0){
        notifier(`${term}_branch_id`, 'Required')
        check = false;
    }
    if($("#isMailValid").val() == 0){
        notifier(`${term}_email`, 'Invalid Email')
        check = false;
    }
    if($(`#${term}_mobile`).val().length > 0){
        if($(`#${term}_mobile`).val().length !== 10){
            notifier(`${term}_mobile`, 'Invalid Mobile No')
            check = false;    
        }
    }
    if(check){
        let path = `master/person/add_update/${id}`;
        let form_data = $(`#${term}_form`).serialize();
        ajaxCall('POST',path,form_data,'JSON',resp=>{
            remove_person_notifier();
            handle_master_response(id, field, term, resp, 'fullname');
        },errmsg =>{});
    }
}
