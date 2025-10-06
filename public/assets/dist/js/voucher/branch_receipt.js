$(document).ready(function(){
    $("#vm_acc_id").on('change', event => get_account(event.target.value));
	$("#vm_branch").select2(select2_default({
        url:`voucher/branch_receipt/get_select2/_branch_id`,
        placeholder:'SELECT',
        param:()=>$('#vm_constant').val(),
    })).on('change', () => get_branch_balance());

    $("#_entry_no").select2(select2_default({
        url:`voucher/branch_receipt/get_select2/_entry_no`,
        placeholder:'ENTRY NO',
    })).on('change', () => trigger_search());

    $("#_account_name").select2(select2_default({
        url:`voucher/branch_receipt/get_select2/_account_name`,
        placeholder:'ACCOUNT',
    })).on('change', () => trigger_search());

    $("#_party_name").select2(select2_default({
        url:`voucher/branch_receipt/get_select2/_party_name`,
        placeholder:'BRANCH',
    })).on('change', () => trigger_search());
});
const set_default = () => {
	$('#vm_bill_amt, #vm_return_amt, #vm_amt_received, #vm_selected_amt').val(0)
	$('#vm_balance_show').val('')
	$('#vm_balance_type').val('')
	$('#vm_total_amt, #vm_round_off').val(0)
	$('#voucher_material_wrapper').html('')
}
const get_account = id => {
    $('#vm_constant').val('');
    $('#vm_branch').val(null).trigger('change');
    $('#vm_party_id').val(0);
	if(id > 0){
		let path= `master/account/get_data/${id}`;   
    	ajaxCall('GET',path,'','JSON',resp =>{
	        let {status, flag, data, msg} = resp;
	        if(status){
	            if(flag == 1){
                	if(data && data.length != 0){
                		$('#vm_constant').val(data[0]['account_constant'])
                		get_branch_balance()
	                }
	            }else{
	                response_error(flag, msg)
	            }
	        }else{
	            session_expired()               
	        }                
	    },errmsg => {
	    });
	}else{
		get_branch_balance()
	}
}
const get_branch_balance = () =>{
	set_default();
	remove_receipt_notifier();
	const group  	= $('#vm_group').val();
	const acc_id 	= $('#vm_acc_id').val();
	const branch_id = $('#vm_branch').val();
	const constant  = $('#vm_constant').val();
	$('.wrapper_area').addClass('d-none');
	if(branch_id){
		let path= `voucher/branch_receipt/get_branch_balance/${branch_id}/${constant}`;   
    	ajaxCall('GET',path,'','JSON',resp =>{
	        let {status, flag, data, msg} = resp;
	        if(status){
	            if(flag == 1){
	            	const {acc_data, bal_data, bill_amt, outward_data, receipt_amt} = data;

                	$('#vm_total_amt').val(receipt_amt ? receipt_amt : 0);
                	$('#vm_bill_amt').val(bill_amt ? bill_amt : 0);
                	if(bal_data){
	                	$('#vm_balance_type').val(bal_data.type)
	                	$('#vm_balance_show').val(`${bal_data.amt} ${bal_data.type}`)
	                	$('#vm_balance').val(`${bal_data.amt}`)
	                }
	                if(acc_data && acc_data.length != 0){
	                	$('#vm_party_id').val(acc_data[0]['account_id'])
	                }
		            if(data['outward_data'] && data['outward_data'].length != 0){
                		add_outward_to_receipt_wrapper(data['outward_data']);
                		$('.wrapper_area').removeClass('d-none');
	                }
	            }else{
	                response_error(flag, msg)
	            }
	        }else{
	            session_expired()               
	        }                
	    },errmsg => {
	    });
    }
}
const select_deselect = (count = 0) =>{
	let total_tr = $('#receipt_material_wrapper tr').length;
	for (let i = 1; i <= total_tr; i++){
		let id = $('#receipt_material_wrapper tr:nth-child('+i+')').attr('id');
        let lastunderscore = id.lastIndexOf("_");
        let cnt = id.substring(lastunderscore+1);
        let balance_amt = $(`#vt_balance_amt_${cnt}`).val();
        if(count == 0){
        	let parent_checked = $(`#parent_checkbox`).is(':checked');
    		$(`#child_checkbox_${cnt}`).prop('checked', parent_checked)
        }
        if(count == 0 || cnt == count){
        	if($(`#child_checkbox_${cnt}`).is(':checked')){
	    		$(`#vt_bill_clear_${cnt}`).val('YES')
	    		// total_checked++; 
	    	}else{
	    		$(`#vt_adjust_amt_${cnt}`).val(0)
	    		$(`#vt_adjust_round_off_${cnt}`).val(0)
	    		$(`#vt_bill_clear_${cnt}`).val('NO')
	    	}
        }
	}
	let total_checked 	= $('.checkboxes:checked').length;
	$(`#parent_checkbox`).prop('checked', total_tr == total_checked);
	calculate_master_total()
}
const calculate_master_total = () =>{
	remove_receipt_notifier();
	let acc_id 					= $('#vm_acc_id').val();
	let constant 				= $('#vm_constant').val();
	let total_tr  				= $('#receipt_material_wrapper tr').length;
	let total_total_amt			= 0;
	for (let i = 1; i <= total_tr; i++){
		let id = $('#receipt_material_wrapper tr:nth-child('+i+')').attr('id');
        let lastunderscore = id.lastIndexOf("_");
        let cnt = id.substring(lastunderscore+1);
		
		let total_amt = $(`#vt_total_amt_${cnt}`).val();
		if(isNaN(total_amt) || total_amt == '') total_amt = 0;
		
		let return_amt= $(`#vt_return_amt_${cnt}`).val();
		if(isNaN(return_amt) || return_amt == '') return_amt = 0;

		let bal_amt= parseFloat(total_amt) - parseFloat(return_amt);
		if(isNaN(bal_amt) || bal_amt == '') bal_amt = 0;

        if($(`#child_checkbox_${cnt}`).is(':checked')){

			$(`#vt_adjust_amt_${cnt}`).val(return_amt)
			$(`#vt_balance_amt_show_${cnt}`).val(bal_amt);		
			
			total_total_amt = parseFloat(total_total_amt) + parseFloat(return_amt);
			if(isNaN(total_total_amt) || total_total_amt == '') total_total_amt = 0;
    	}else{
    		$(`#vt_adjust_amt_${cnt}`).val(0)
			$(`#vt_balance_amt_show_${cnt}`).val(total_amt);		
    	}
	}
	$('#vm_total_amt').val(total_total_amt.toFixed(2));
	calculate_master_balance();
}
const calculate_master_balance = () =>{
	let bal_amt = $(`#vm_bill_amt`).val();
	if(isNaN(bal_amt) || bal_amt == '') bal_amt = 0;

	let total_amt = parseFloat( ('0' + $(`#vm_total_amt`).val()).replace(/[^0-9-\.]/g, ''), 10 );
	if(isNaN(total_amt) || total_amt == '') total_amt = 0;

	let bal_type = $(`#vm_balance_type`).val();
	let close_amt = 0;
	if(bal_type == TO_PAY){
		close_amt = parseFloat(bal_amt) + parseFloat(total_amt);
	}else{
		close_amt = parseFloat(bal_amt) - parseFloat(total_amt);
	}
	if(isNaN(close_amt) || close_amt == '') close_amt = 0;
	if(close_amt < 0){
		close_amt = Math.abs(close_amt)
		if(bal_type == TO_PAY){
			bal_type = TO_RECEIVE
		}else{
			bal_type = TO_PAY
		}
	}

	$('#vm_balance_show').val(`${close_amt} ${bal_type}`);

	if(total_amt > 0){
		$('.master_block_btn').prop('disabled', false)
	}else{
		$('.master_block_btn').prop('disabled', true)
	}
}
const add_outward_to_receipt_wrapper = outwards => {
	let data = `
		<thead>
			<tr style="font-size: 15px;">
                <th width="2%"><input type="checkbox" id="parent_checkbox" onclick="select_deselect()" checked/></th>
                <th width="5%">ENTRY NO</th>
                <th width="7%">ENTRY DATE</th>
                <th width="6%">OUTWARD QTY</th>
                <th width="6%">GRN QTY</th>
                <th width="6%">OUTWARD AMT</th>
                <th width="6%">GRN AMT</th>
                <th width="8%">ADJUST AMT</th>
                <th width="10%">CURRENT BAL. AMT</th>
            </tr>
        </thead>
        <tbody id="receipt_material_wrapper">
	`;
	outwards.forEach(outward => {
		const {om_id, om_entry_no, om_entry_date, om_total_qty, om_gm_total_qty, om_final_amt, om_gm_final_amt, om_allocated_amt, om_allocated_round_off} = outward
		const balance_amt = parseFloat(om_final_amt) - parseFloat(om_gm_final_amt);
		data += `
			<tr id="rowid_${om_id}" class="floating-form">
				<td width="2%" class="floating-label">
					<input type="hidden" id="vt_id_${om_id}" name="vt_id[]" value="0" />
					<input type="hidden" id="vt_bill_clear_${om_id}" name="vt_bill_clear[]" value="YES" />
					<input type="checkbox" class="checkboxes" id="child_checkbox_${om_id}" onclick="select_deselect(${om_id})" checked/>
				</td>
				<td width="5%" class="floating-label">
					<input type="hidden" id="vt_om_id_${om_id}" name="vt_om_id[]" value="${om_id}" />
					<input type="text" class="form-control floating-input" name="vt_bill_no[]" id="vt_bill_no_${om_id}" value="${om_entry_no}" readonly />
				</td>
				<td width="7%" class="floating-label">
					<input type="text" class="form-control floating-input" name="vt_bill_date[]" id="vt_bill_date_${om_id}" value="${om_entry_date}" readonly />
				</td>
				<td width="6%" class="floating-label">
					<input type="number" class="form-control floating-input" name="vt_total_qty[]" id="vt_total_qty_${om_id}" value="${om_total_qty}" readonly />
				</td>
				<td width="6%" class="floating-label">
					<input type="number" class="form-control floating-input" name="vt_return_qty[]" id="vt_return_qty_${om_id}" value="${om_gm_total_qty}" readonly />
				</td>
				<td width="6%" class="floating-label">
					<input type="number" class="form-control floating-input" name="vt_total_amt[]" id="vt_total_amt_${om_id}" value="${om_final_amt}" readonly />
					<input type="hidden" class="form-control floating-input" name="vt_allocated_amt[]" id="vt_allocated_amt_${om_id}" value="${om_allocated_amt}" readonly />
					<input type="hidden" name="vt_allocated_round_off[]" id="vt_allocated_round_off_${om_id}" value="${om_allocated_round_off}" />
				</td>
				<td width="6%" class="floating-label">
					<input type="number" class="form-control floating-input" name="vt_return_amt[]" id="vt_return_amt_${om_id}" value="${om_gm_final_amt}" readonly />
				</td>
				<td width="8%" class="floating-label">
					<input type="number" class="form-control floating-input" name="vt_adjust_amt[]" id="vt_adjust_amt_${om_id}" value="${om_gm_final_amt}" readonly />
					<input type="hidden" name="vt_adjust_round_off[]" id="vt_adjust_round_off_${om_id}" value="0" />
				</td>
				<td width="10%" class="floating-label">
					<input type="number" class="form-control floating-input" id="vt_balance_amt_show_${om_id}" value="${balance_amt}" readonly />
					<input type="hidden" id="vt_balance_amt_${om_id}" value="${balance_amt}" readonly />
				</td>
			</tr>
		`;
	})
	data +=`
        </tbody>
	`;
	$('#voucher_material_wrapper').html(data)
}
const is_amt_matched = (master, trans) =>{
	let master_amt = $(`#${master}`).val();
	if(isNaN(master_amt) || master_amt == '') master_amt = 0;
	let total_trans_amt = 0;
	let total_tr 		= $('#receipt_material_wrapper tr').length;
	for (let i = 1; i <= total_tr; i++){
		let id = $('#receipt_material_wrapper tr:nth-child('+i+')').attr('id');
	    let lastunderscore = id.lastIndexOf("_");
	    let cnt = id.substring(lastunderscore+1);

		if($(`#child_checkbox_${cnt}`).is(':checked')){
			let trans_amt = $(`#${trans}_${cnt}`).val();
			if(isNaN(trans_amt) || trans_amt == '') trans_amt = 0;
			
			total_trans_amt = parseFloat(total_trans_amt) + parseFloat(trans_amt);
			if(isNaN(total_trans_amt) || total_trans_amt == '') total_trans_amt = 0;
    	}    
    }

	if(master_amt == total_trans_amt) return true;
    return false;
}
const check_current_balance = () =>{
	let total_tr = $('#receipt_material_wrapper tr').length;
	let flag 	 = true;
	let last_id  = 0;
	for (let i = 1; i <= total_tr; i++){
		let id = $('#receipt_material_wrapper tr:nth-child('+i+')').attr('id');
	    let lastunderscore = id.lastIndexOf("_");
	    let cnt = id.substring(lastunderscore+1);

		if($(`#child_checkbox_${cnt}`).is(':checked')){
			let return_amt = parseFloat( ('0' + $(`#vt_return_amt_${cnt}`).val()).replace(/[^0-9-\.]/g, ''), 10 );
			if(isNaN(return_amt) || return_amt == '') return_amt = 0;					

			let adjust_amt = parseFloat( ('0' + $(`#vt_adjust_amt_${cnt}`).val()).replace(/[^0-9-\.]/g, ''), 10 );
			if(isNaN(adjust_amt) || adjust_amt == '') adjust_amt = 0;					

			if(adjust_amt > 0){
				if(return_amt != adjust_amt){
					$(`#vt_total_amt_${cnt}`).addClass('text-danger');
					$(`#vt_adjust_amt_${cnt}`).addClass('text-danger');
					flag = false
					last_id = cnt;
				}else{
					$(`#vt_total_amt_${cnt}`).removeClass('text-danger');
					$(`#vt_adjust_amt_${cnt}`).removeClass('text-danger');
				}	
			}
    	}    
    }
    if(!flag){
    	if(last_id > 0){
	        $('html, body').animate({
	            scrollTop: $(`#rowid_${last_id}`).offset().top
	        }, RELOAD_TIME);
    	}
    }
    return flag;
}
const remove_receipt_notifier = () =>{
    notifier('vm_acc_id')
    notifier('vm_branch')
    notifier('vm_total_amt')
    notifier('vm_round_off')
}
const add_update_receipt = (id) =>{
    remove_receipt_notifier()
    let check   	= true;
    let check_trans = true;
    if($("#vm_acc_id").val() == 0){
        notifier('vm_acc_id', 'Required')
        check = false;
    }
    if($("#vm_branch").val() == null){
        notifier('vm_branch', 'Required')
        check = false;
    }else{
	    if($("#vm_party_id").val() == 0 || $("#vm_party_id").val() == ''){
	        notifier('vm_branch', 'Account not found')
	        check = false;
	    }
    }
    if($("#vm_total_amt").val() <= 0){
        notifier('vm_total_amt', 'Required')
        check = false;
    }
    if($('.checkboxes:checked').length != 0){
    	if(!is_amt_matched('vm_total_amt', 'vt_adjust_amt')){
    		notifier('vm_total_amt', 'Total amt and adjusted amt are mismatch.')
    		check = false;
	    }
	    if(!is_amt_matched('vm_round_off', 'vt_adjust_round_off')){
	    	notifier('vm_round_off', 'Round off amt and adjusted off amt are mismatch.')
	    	check = false;
	    }	
    }
    if(!check_current_balance()){
    	check_trans = false;
    }
    
    if(!check){
        callToastify('error', 'Oh snap ! You forgot to enter some information', 'right')
        $("body, html").animate({'scrollTop':0},1000);
    }else if(!check_trans){
        callToastify('error', 'Outward amt and adjusted amt should be same.', 'right')
        $("body, html").animate({'scrollTop':0},1000);
    }else if(id == 0 && $('.checkboxes:checked').length == 0){
        callToastify('error', 'Please select outward to adjust amt.', 'right')
        $("body, html").animate({'scrollTop':0},1000);
    }else{
        let path        = "voucher/branch_receipt/add_update/"+id;   
        let form_data   = $("#receipt_form").serialize();
        ajaxCall('POST',path,form_data,'JSON',resp =>{
            let {status, flag, msg} = resp;
            if(status){
                if(flag == 1){
                    if(id == 0) {
                    } else {
                    }
                    remove_receipt_notifier()
                    callToastify('success', msg, 'right')
                    $("body, html").animate({'scrollTop':0},1000);
                    setTimeout(function(){window.location.reload(); },RELOAD_TIME); 
                }else{
                    response_error(flag, msg)
                }
            }else{
                session_expired()               
            }                
        },errmsg => {
        });
    }
}