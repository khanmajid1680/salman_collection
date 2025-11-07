$(document).ready(function(){
    $("#id").select2(select2_default({
        url:`master/transport/get_select2/_name`,
        placeholder:'TRANSPORT',
    })).on('change', () => trigger_search());
    $("#_gst_no").select2(select2_default({
         url:`master/transport/get_select2/_gstno`,
        placeholder:'GST NO',
    })).on('change', () => trigger_search());
});
const set_transport_field = id => {
    const term  = "transport";
    const path  = `master/transport/get_data/${id}`
    ajaxCall('GET',path,'','JSON',resp=>{
        const {status, flag, data, msg} = resp;
        if(status){
            if(flag == 1){
                if(data){
                    $(`#${term}_name`).val(data[0][`${term}_name`])
                    $(`#${term}_gst_no`).val(data[0][`${term}_gst_no`])
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
const transport_popup = (id, field = undefined) => {
    let path = `master/transport/get_data_for_user`
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
    let {roles} = resp;
    const term  = "transport";
    let title= id == 0 ? 'ADD' : 'EDIT';
    let data = `
                <form class="form-horizontal" id="${term}_form" onsubmit="add_update_transport(${id}, ${field})">              
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex flex-wrap form-group floating-form">
                                <div class="col-12 floating-label">
                                    <input type="text" name="${term}_name" class="form-control floating-input" id="${term}_name" onkeyup="validate_textfield(this)" placeholder=" " autocomplete="off" />   
                                    <label for="inputEmail3">NAME <span style="color:red">*</span></label>
                                    <small class="form-text text-muted helper-text" id="${term}_name_msg"></small>
                                </div>  
                                <div class="col-6 floating-label">
                                    <input type="text" id="${term}_gst_no" name="${term}_gst_no" value="" class="form-control floating-input" placeholder=" " autocomplete="off" />   
                                    <label for="inputEmail3">GST NO. </label>
                                    <small class="form-text text-muted helper-text" id="${term}_gst_no_msg"></small>
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
                <button type="button" class="btn btn-sm btn-primary" id="sbt_btn" ${id == 0 && 'disabled'} onclick="add_update_transport(${id}, ${field})" >${title}</button>
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">CANCEL</button>        
             `;
    
    $(".modal-title-sm").html(`${title} TRANSPORT`);
    $(".modal-body-sm").html(data);
    $('.modal-footer-sm').html(btn)
    $("#popup_modal_sm").modal('show');  
    
    setTimeout(()=>{
        $(`#${term}_name`).focus();
    },RELOAD_TIME);

    if(id != 0){
        set_transport_field(id);
    }
    $(`#${term}_status`).bootstrapToggle()
}
const remove_transport_notifier = term => {
    notifier(`${term}_name`)
    notifier(`${term}_gst_no`)
}
const add_update_transport = (id, field) =>{
    event.preventDefault();
    const term  = "transport";
    let check   = true;
    remove_transport_notifier(term);
    if($(`#${term}_name`).val() == ''){
        notifier(`${term}_name`, 'Required')
        check = false;
    }
   
    if($("#isMailValid").val() == 0){
        notifier(`${term}_email`, 'Invalid Email')
        check = false;
    }
    if($(`#${term}_gst_no`).val() == ''){
        notifier(`${term}_gst_no`, 'Required')
        check = false;
    }
    if(check){
        let path = `master/transport/add_update/${id}`;
        let form_data = $(`#${term}_form`).serialize();
        ajaxCall('POST',path,form_data,'JSON',resp=>{
            remove_transport_notifier();
            handle_master_response(id, field, term, resp, 'name');
        },errmsg =>{});
    }
}
