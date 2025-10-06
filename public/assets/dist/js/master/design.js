$(document).ready(function(){
    $("#id").select2(select2_default({
        url:`master/design/get_select2`,
        placeholder:'DESIGN',
    })).on('change', () => trigger_search());
});
const set_design_field = id => {
    const term  = "design";
    const path  = `master/${term}/get_data/${id}`
    ajaxCall('GET',path,'','JSON',resp=>{
        const {status, flag, data, msg} = resp;
        if(status){
            if(flag == 1){
                if(data){
                    $(`#${term}_name`).val(data[0][`${term}_name`])
                    $(`#${term}_sgst_per`).val(data[0][`${term}_sgst_per`]);
					$(`#${term}_cgst_per`).val(data[0][`${term}_cgst_per`]);
					$(`#${term}_igst_per`).val(data[0][`${term}_igst_per`]);
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
const design_popup = (id, field = undefined) =>{
    const term  = "design";
    let title   =  id != 0 ? `EDIT` : `ADD`;
    let data = `
                <form class="form-horizontal" id="${term}_form" onsubmit="add_update_${term}(${id}, ${field})">              
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex flex-wrap form-group floating-form">
                                <div class="col-8 floating-label">
                                    <input type="text" name="${term}_name" class="form-control floating-input" id="${term}_name" onkeyup="validate_textfield(this)" placeholder=" " autocomplete="off" />   
                                    <label for="inputEmail3">${term.toUpperCase()} <span style="color:red">*</span></label>
                                    <small class="form-text text-muted helper-text" id="${term}_name_msg"></small>
                                </div>
                                ${field == undefined ? `<div class="col-4 floating-label">
                                    <input type="checkbox" id="${term}_status" name="${term}_status" data-toggle="toggle" data-on="ACTIVE" data-off="INACTIVE" data-onstyle="primary" data-offstyle="primary" data-width="100" data-size="normal" checked>
                                </div>` : `<input type="hidden" name="${term}_status" value="1">` }

                                <div class="col-6 floating-label">
                                   <input 
                                        type="number" 
                                        class="form-control floating-input" 
                                        name="${term}_sgst_per" 
                                        id="${term}_sgst_per" 
                                        onkeyup="set_gst(this.value)" 
                                        placeholder=" " 
                                        autocomplete="off" 
                                    />     
                                    <label class="text-uppercase">sgst % </label>
                                    <small class="form-text text-muted helper-text" id="${term}_sgst_per_msg"></small>
                                </div>
                                <div class="col-6 floating-label">
                                    <input 
                                        type="number" 
                                        class="form-control floating-input" 
                                        name="${term}_cgst_per" 
                                        id="${term}_cgst_per" 
                                        placeholder=" "
                                        readonly="readonly"
                                    />   
                                    <label class="text-uppercase">cgst % </label>
                                    <small class="form-text text-muted helper-text" id="${term}_cgst_per_msg"></small>
                                </div>
                                <div class="col-6 floating-label">
                                    <input 
                                        type="number" 
                                        class="form-control floating-input" 
                                        name="${term}_igst_per" 
                                        id="${term}_igst_per"
                                        placeholder=" "
                                        readonly="readonly"
                                    />   
                                    <label class="text-uppercase">igst % </label>
                                    <small class="form-text text-muted helper-text" id="${term}_igst_per_msg"></small>
                                </div>

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
        set_design_field(id);
    }
    $(`#${term}_status`).bootstrapToggle()
}

const set_gst = (per) => {
	$("#design_cgst_per").val(per);
	$("#design_igst_per").val(parseFloat(per * 2));
};

const remove_design_notifier = term => {
    notifier(`${term}_name`)
}
const add_update_design = (id, field) =>{
    event.preventDefault();
    const term  = "design";
    let check   = true;
    remove_design_notifier(term)
    if($(`#${term}_name`).val() == ''){
        notifier(`${term}_name`, 'Required')
        check = false;
    }
    if(check){
        let path = `master/${term}/add_update/${id}`;
        let form_data = $(`#${term}_form`).serialize();
        ajaxCall('POST',path,form_data,'JSON',resp=>{
            remove_design_notifier();
            handle_master_response_select2(id, field, term, resp);
        },errmsg =>{});
    }
}
