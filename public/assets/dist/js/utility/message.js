$(document).ready(()=>{
    $("#mm_account_type").on('change', ()=> get_data_for_message());
    
});
let message_cnt = 1;
const get_data_for_message = () => {
	let account_type = $('#mm_account_type').val();
	if(account_type != ''){
		let path = `master/account/get_data_for_message/${account_type}`;   
        ajaxCall('GET',path,'','JSON',resp =>{
            let {status, flag, data, msg} = resp;
            if(status){
                if(flag == 1){
                    add_to_wrapper(data);
                }else{
                    response_error(flag, msg)
                }
            }else{
                session_expired()               
            }                
        },errmsg => {
        });
	}else{
		$('#message_wrapper').empty()
		select_deselect();
	}
}
const add_to_wrapper = data => {
	let tr = '';
	if(data && data.length != 0){
		data.forEach(acc => {
			tr +=`
				<tr id="rowid_${message_cnt}">
					<td width="2%">
						<input type="checkbox" class="checkboxes" id="child_checkbox_${message_cnt}" checked onchange="select_deselect(${message_cnt})"/>
						<input type="hidden" id="send_sms_${message_cnt}" name="send_sms[]" value="YES"/>
						<input type="hidden" id="mt_id_${message_cnt}" name="mt_id[]" value="0"/>
					</td>
					<td width="40%">
						${acc.account_name.toUpperCase()}
						<input type="hidden" id="mt_account_name_${message_cnt}" name="mt_account_name[]" value="${acc.account_name}" />
					</td>
					<td width="40%">
						${acc.account_mobile}
						<input type="hidden" id="mt_account_mobile_${message_cnt}" name="mt_account_mobile[]" value="${acc.account_mobile}" />
					</td>
					<td width="18%">
						<input type="hidden" id="mt_status_${message_cnt}" name="mt_status[]" value="0" />
					</td>
				</tr>
			`;
			message_cnt++;
		})
	}else{
		tr = `
			<tr>
				<td class="font-weight-bold text-danger" align="center"> No Record Found!!!</td>
			</tr>
		`;
	}
	$('#message_wrapper').html(tr);
	$('.parent').prop('checked', true);
	select_deselect();
}
const select_deselect = (count = 0) => {
	let total_tr = $('#message_wrapper tr').length;
	for (let i = 1; i <= total_tr; i++){
		let id = $('#message_wrapper tr:nth-child('+i+')').attr('id');
        let lastunderscore = id.lastIndexOf("_");
        let cnt = id.substring(lastunderscore+1);
        if(count == 0){
        	let parent_checked = $(`#parent_checkbox`).is(':checked');
    		$(`#child_checkbox_${cnt}`).prop('checked', parent_checked)
        }
        if(count == 0 || cnt == count){
        	if($(`#child_checkbox_${cnt}`).is(':checked')){
	    		$(`#send_sms_${cnt}`).val('YES')
	    	}else{
	    		$(`#send_sms_${cnt}`).val('NO')
	    	}
        }
	}
	let total_checked 	= $('.checkboxes:checked').length;
	$(`#parent_checkbox`).prop('checked', total_tr == total_checked);
	$('#mm_total_qty').val(total_checked);
	if(total_checked != 0){
		$('#bulk_message_btn').prop('disabled', false)
	}else{
		$('#bulk_message_btn').prop('disabled', true)
	}
}
const sender_popup = () =>{
    let title= 'ADD';
    let data = `
                <form class="form-horizontal" id="sender_form" onsubmit="add_sender()">              
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="d-flex flex-wrap form-group floating-form">
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                                        <input type="text" id="sender_name" name="sender_name" value="" class="form-control floating-input" placeholder=" " autocomplete="off" />   
                                        <label for="inputEmail3">SENDER NAME</label>
                                        <small class="form-text text-muted helper-text" id="sender_name_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                                        <input type="number" id="sender_mobile" name="sender_mobile" value="" class="form-control floating-input" placeholder=" " onkeyup="set_mobile_no(this)" onfocusout="validate_mobile_no(this)" autocomplete="off" />   
                                        <label for="inputEmail3">Mobile <span style="color:red">*</span><span id="sender_mobile_length">(10)</span></label>
                                        <small class="form-text text-muted helper-text" id="sender_mobile_msg"></small>
                                    </div>
                                </div>              
                            </div>              
                        </div>              
                    </div>
                </form>

               `;
    let btn =`
        <button type="button" class="btn btn-success" onclick="add_sender()" >${title}</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">CANCEL</button>        
    `;
    $(".modal-body-sm").html(data);
    $(".modal-title-sm").html(`${title} SENDER`);
    $('.modal-footer-sm').html(btn)
    $("#popup_modal_sm").modal('show');  
    setTimeout(()=>{
        $('#sender_name').focus();
    },RELOAD_TIME);
}
const add_sender = () => {
	event.preventDefault();
	remove_message_notifier();
	let sender_name 	= $('#sender_name').val();
	let sender_mobile 	= $('#sender_mobile').val();
	let check = true;
	let dup   = true;
	// if(sender_name == ''){
	// 	notifier('sender_name', 'Required');
	// 	check = false;
	// }

	if(sender_mobile == ''){
		notifier('sender_mobile', 'Required');
		check  = false;
	}else{
		if(sender_mobile.length != 10){
			notifier('sender_mobile', 'Invalid Mobile No');
			check  = false;	
		}
	}
	let total_tr = $('#message_wrapper tr').length;
	for (let i = 1; i <= total_tr; i++){
		let id = $('#message_wrapper tr:nth-child('+i+')').attr('id');
        let lastunderscore = id.lastIndexOf("_");
        let cnt = id.substring(lastunderscore+1);
        if($(`#mt_account_mobile_${cnt}`).val() == sender_mobile){
        	notifier('sender_mobile', 'Mobile No already added');
        	dup = false;
        }
	}
	if(!check){
		callToastify('error', 'Oh snap ! You forgot to enter some information', 'right')
        $("body, html").animate({'scrollTop':0},1000);
	}else if(!dup){
		callToastify('error', 'Mobile No already added', 'right')
        $("body, html").animate({'scrollTop':0},1000);
	}else{
		let tr =`
				<tr id="rowid_${message_cnt}">
					<td width="2%">
						<input type="checkbox" class="checkboxes" id="child_checkbox_${message_cnt}" checked onchange="select_deselect(${message_cnt})"/>
						<input type="hidden" id="send_sms_${message_cnt}" name="send_sms[]" value="YES"/>
						<input type="hidden" id="mt_id_${message_cnt}" name="mt_id[]" value="0"/>
					</td>
					<td width="40%">
						${sender_name.toUpperCase()}
						<input type="hidden" id="mt_account_name_${message_cnt}" name="mt_account_name[]" value="${sender_name}" />
					</td>
					<td width="40%">
						${sender_mobile}
						<input type="hidden" id="mt_account_mobile_${message_cnt}" name="mt_account_mobile[]" value="${sender_mobile}" />
					</td>
					<td width="18%">
						<input type="hidden" id="mt_status_${message_cnt}" name="mt_status[]" value="0" />
					</td>
				</tr>
			`;
		message_cnt++;
		$('#message_wrapper').prepend(tr);
		callToastify('success', 'New sender added', 'right')
		$("#popup_modal_sm").modal('hide');
		select_deselect(message_cnt);  
	}
}
const remove_message_notifier = () =>{
	notifier('mm_description');
	notifier('mm_trial_mobile');
	notifier('mm_account_type');
	notifier('sender_name')
	notifier('sender_mobile')
}
const send_trial_message = () => {
	event.preventDefault();
	remove_message_notifier();
	let check = true;
	let message = $('#mm_description').val();
	let mobile = $('#mm_trial_mobile').val();
	if(message == ''){
		notifier('mm_description', 'Required');
		check = false;
	}
	if(mobile == ''){
		notifier('mm_trial_mobile', 'Required');
		check = false;
	}else{
		if(mobile.length != 10){
			notifier('mm_trial_mobile', 'Invalid Mobile No.');
			check = false;	
		}
	}
	if(!check){
        callToastify('error', 'Oh snap ! You forgot to enter some information', 'right')
        $("body, html").animate({'scrollTop':0},1000);
	}else{
		$('#trial_message_btn').prop('disabled', true);
		$('#trial_message_btn_text').html('SMS SENDING...');
		let path        = "message/send_trial_message/";   
        let form_data   = {mobile, message};
        ajaxCall('POST',path,form_data,'JSON',resp =>{
            let {status, flag, msg} = resp;
            if(status){
                if(flag == 1){
                    remove_message_notifier()
                    callToastify('success', msg, 'right')
                    $("body, html").animate({'scrollTop':0},1000);
                }else{
                    response_error(flag, msg)
                }
                $('#trial_message_btn').prop('disabled', false);
				$('#trial_message_btn_text').html('SEND TRIAL MESSAGE');
            }else{
                session_expired()               
            }                
        },errmsg => {
        });
	}
}
const add_update = id => {
	event.preventDefault();
	remove_message_notifier();
	let check = true;
	let message = $('#mm_description').val();
	if(message == ''){
		notifier('mm_description', 'Required');
		check = false;
	}
	if(!check){
        callToastify('error', 'Oh snap ! You forgot to enter some information', 'right')
        $("body, html").animate({'scrollTop':0},1000);
	}else{
		$('#bulk_message_btn').prop('disabled', true);
		$('#bulk_message_btn_text').html('SMS SENDING...');
		let path        = `message/add_update/${id}`;   
        let form_data   = $("#message_form").serialize();
        ajaxCall('POST',path,form_data,'JSON',resp =>{
            let {status, flag, data, msg} = resp;
            if(status){
                if(flag == 1){
                	if(id == 0){

                	}else{

                	}
                    remove_message_notifier()
                    $("body, html").animate({'scrollTop':0},1000);
                    redirectPage(`message?action=edit&id=${data.id}`);
                }else{
                    response_error(flag, msg)
                }
                $('#bulk_message_btn').prop('disabled', false);
				$('#bulk_message_btn_text').html('SEND TRIAL MESSAGE');
            }else{
                session_expired()               
            }                
        },errmsg => {
        });
	}
}