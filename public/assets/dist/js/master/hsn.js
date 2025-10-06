$(document).ready(function(){
    $("#id").select2(select2_default({
        url:`master/hsn/get_select2`,
        placeholder:'HSN',
    })).on('change', () => trigger_search());
});
const set_hsn_field = id => {
    const term  = "hsn";
    const path  = `master/${term}/get_data/${id}`
    ajaxCall('GET',path,'','JSON',resp=>{
        const {status, flag, data, msg} = resp;
        if(status){
            if(flag == 1){
                if(data){
                    $(`#${term}_name`).val(data[0][`${term}_name`])
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
const hsn_popup = (id, field = undefined) =>{
    const term  = "hsn";
    let title   =  id != 0 ? `EDIT` : `ADD`;
    let data = `
                <form class="form-horizontal" id="${term}_form" onsubmit="add_update_${term}(${id}, ${field})">              
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex form-group floating-form">
                                <div class="col-8 floating-label">
                                    <input type="text" name="${term}_name" class="form-control floating-input" id="${term}_name" onkeyup="validate_textfield(this)" placeholder=" " autocomplete="off" />   
                                    <label for="inputEmail3">${term.toUpperCase()} <span style="color:red">*</span></label>
                                    <small class="form-text text-muted helper-text" id="${term}_name_msg"></small>
                                </div>
                                ${field == undefined ? `<div class="col-4 floating-label">
                                    <input type="checkbox" id="${term}_status" name="${term}_status" data-toggle="toggle" data-on="ACTIVE" data-off="INACTIVE" data-onstyle="primary" data-offstyle="primary" data-width="100" data-size="normal" checked>
                                </div>` : `<input type="hidden" name="${term}_status" value="1">` }                            
                            </div>              
                        </div>              
                    </div>              
                </form>
               `;

    let btn =`
                <button type="button" class="btn btn-sm btn-primary" id="sbt_btn" ${id == 0 && 'disabled'} onclick="add_update_${term}(${id}, ${field})" >${title}</button>
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">CANCEL</button>        
             `;
    
    $(".modal-title-sm").html(`${title} ${term.toUpperCase()}`);
    $(".modal-body-sm").html(data);
    $('.modal-footer-sm').html(btn)
    $("#popup_modal_sm").modal('show');  
    
    setTimeout(()=>{
        $(`#${term}_name`).focus();
    },RELOAD_TIME);

    if(id != 0){
        set_hsn_field(id);
    }
    $(`#${term}_status`).bootstrapToggle()
}
const remove_hsn_notifier = term => {
    notifier(`${term}_name`)
}
const add_update_hsn = (id, field) =>{
    event.preventDefault();
    const term  = "hsn";
    let check   = true;
    remove_hsn_notifier(term)
    if($(`#${term}_name`).val() == ''){
        notifier(`${term}_name`, 'Required')
        check = false;
    }
    if(check){
        let path = `master/${term}/add_update/${id}`;
        let form_data = $(`#${term}_form`).serialize();
        ajaxCall('POST',path,form_data,'JSON',resp=>{
            remove_hsn_notifier();
            handle_master_response_select2(id, field, term, resp);
        },errmsg =>{});
    }
}
