$(document).ready(function(){
	$("#"+link).addClass("active");
	$("#"+sub_link).addClass("active");
    // $(".select2").select2(select2default)
    if(link){
        let el = document.querySelector(`#${link}`);
        el && el.scrollIntoView(true);
    }
    $("#status").select2(select2_default({
        url:`master/common/get_select2_status`,
        placeholder:'STATUS',
    })).on('change', () => trigger_search());
    $("#role").select2(select2_default({
        url:`master/common/get_select2_role`,
        placeholder:'ROLE',
    })).on('change', () => trigger_search());
    $("#drcr").select2(select2_default({
        url:`master/common/get_select2_drcr`,
        placeholder:'DR/CR',
    })).on('change', () => trigger_search());
    $(".datepicker").datepicker({
        format: 'dd-mm-yyyy',
        todayHighlight: true,
        autoclose: true,
        orientation: 'auto bottom',
        startDate: new Date($('#start_year').val()),
        endDate: new Date($('#end_year').val()),
    });
    $(".future-datepicker").datepicker({
            format: 'dd-mm-yyyy',
            todayHighlight: true,
            autoclose: true,
            startDate: new Date(),
            minDate: 0
    })
    get_pending_grn();
    // setInterval(()=>{
    // }, 10000)
});

const set_mobile_no = (element, flag = false) =>{
    const {id} = element
    let mobile_no = $('#'+id).val()
    let length = 10 - parseInt(mobile_no.length)
    if(length >= 0){
        $('#'+id+'_length').html(`(${length})`)
        length == 0 && notifier(id)
        flag && $('#sbt_btn').prop('disabled', false);
    }
    else{
        $('#'+id).val(mobile_no.substring(0,10))  
        let len = parseInt(10 - $('#'+id).val().length)
        $('#'+id+'_length').html(`(${len})`)
        flag && $('#sbt_btn').prop('disabled', true);
    }
}
const set_mobile_no1 = id =>{
    let mobile_no = $('#'+id).val()
    let length = 10 - parseInt(mobile_no.length)
    if(length >= 0){
        $('#'+id+'_length').html(`(${length})`)
        length == 0 && notifier(id)
    }
    else{
        $('#'+id).val(mobile_no.substring(0,10))  
        let len = parseInt(10 - $('#'+id).val().length)
        $('#'+id+'_length').html(`(${len})`)
    }
}
const session_expired = () =>{
    $("body, html").animate({'scrollTop':0},1000);
    callToastify('warn', 'Session Expired. Please wait...', 'right')
    setTimeout(()=>{
        window.location.href = base_url;
    },RELOAD_TIME);
}
const response_error = (flag, msg) =>{
    $("body, html").animate({'scrollTop':0},1000);
    if(flag == 2){
        callToastify('error', msg, 'right')
    }else{
        callToastify('error', 'Something went wrong', 'right')
    }
}
const remove_master = path =>{
    let ans = confirm("Are you sure? You want to delete item.");
    if(ans == true){
        ajaxCall('GET',path,'','JSON',resp =>{
            const {status, flag, msg} = resp;
            if(status){
                if(flag == 1){
                    $("body, html").animate({'scrollTop':0},1000);
                    callToastify('success', msg, 'right')
                }else{
                    response_error(flag, msg)
                }
            }else{
                session_expired();
            }
        },errmsg=>{});
    }
}
const refresh_dropdown = (data, field) => {
    $(`#${field.id}`).append(`<option value=${data.id}>${data.name}</option>`).focus().val(data.id)
}
const refresh_dropdown_select2 = (data, field) => {
    const newOption = new Option(data.name, data.id, true, true)
    $(`#${field.id}`).append(newOption).trigger('change')
    $(`#${field.id}`).select2('open')
}
const drop_down = object => {
    let entries = Object.entries(object);
    return entries.map(entry => `<option value="${entry[0]}">${entry[1]}</option>`)
}
const get_pending_grn = () => {
    let path = "grn/get_pending_count/";   
    ajax('GET',path,'','JSON',resp=>{
        const {status, flag, data, msg} = resp;
        if(status){
            if(flag == 1){
                if(data != 0){
                    $('.grn_pending').html(data)
                }   
            }else{
                response_error(flag, msg)
            }
        }else{
            session_expired()
        }
    },errmsg =>{}, false);
}

const handle_response = (resp) => {
    const { session, status, active = true, msg } = resp;
    if (!session) {
        session_expired();
        return false;
    }
    if (!active) {
        toastr.error("", msg);
        setTimeout(() => {
            window.location.href = base_url;
        }, 3000);
        return false;
    }
    if (status == 2000) {
        toastr.error("", msg);
        setTimeout(() => {
            setTimeout(function () {
                window.location.reload();
            }, RELOAD_TIME);
        }, RELOAD_TIME);
        return false;
    } else {
        if (!status) {
            toastr.error("", msg, { closeButton: true, progressBar: true });
            return false;
        }
    }

    return true;
};

const handle_master_response = (id, field, term, resp, focus='name') => {
    const {status, flag, data, msg} = resp;
    if(status){
        if(flag == 1){
            if(id == 0){
                if(field != undefined){
                    $("#popup_modal_sm").modal('hide');  
                    refresh_dropdown(data, field);
                }else{
                    $(`#${term}_form`)[0].reset();
                    $(`#${term}_${focus}`).focus();
                }
            }else{
                $("#popup_modal_sm").modal('hide');  
            }
            callToastify('success', msg, 'right')
        }else{
            response_error(flag, msg)
        }
    }else{
        session_expired()
    }
}
const handle_master_response_select2 = (id, field, term, resp, focus='name') => {
    const {status, flag, data, msg} = resp;
    if(status){
        if(flag == 1){
            if(id == 0){
                if(field != undefined){
                    $("#popup_modal_sm").modal('hide');  
                    refresh_dropdown_select2(data, field);
                }else{
                    $(`#${term}_form`)[0].reset();
                    $(`#${term}_${focus}`).focus();
                }
            }else{
                $("#popup_modal_sm").modal('hide');  
            }
            callToastify('success', msg, 'right')
        }else{
            response_error(flag, msg)
        }
    }else{
        session_expired()
    }
}
const trigger_search = () => {
    $("#btn_search").trigger('click'); 
}