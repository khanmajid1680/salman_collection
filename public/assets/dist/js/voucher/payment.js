$(document).ready(function(){
	$("#vm_party_id").select2(select2_default({
        url:`master/account/get_account_select2`,
        placeholder:'NAME',
        param:()=>$('#vm_group').val(),
    })).on('change', () => get_account_balance());
    $("#vm_acc_id").on('change', event => get_account(event.target.value));

    $("#entry_no").select2(select2_default({
        url:`voucher/payment/get_select2_entry_no`,
        placeholder:'ENTRY NO',
    })).on('change', () => trigger_search());

    $("#account_id").select2(select2_default({
        url:`voucher/payment/get_select2_account_id`,
        placeholder:'ACCOUNT NAME',
    })).on('change', () => trigger_search());

    $("#party_id").select2(select2_default({
        url:`voucher/payment/get_select2_party_id`,
        placeholder:'PARTY NAME',
    })).on('change', () => trigger_search());
    $("#from_entry_date, #to_entry_date").on('change', () => trigger_search());
    if($('#vm_redirected').val() == 1){
        set_party($('#_party_id').val())
    }
});
const set_purchase_return_area = () => {
	const group 	= $('#vm_group').val();
	const acc_id 	= $('#vm_acc_id').val();
	const constant 	= $('#vm_constant').val();
	if(group == 'SUPPLIER'){
		$('.pur_sale_area').html('PURCHASE');	
		if(constant != 'DEBIT_NOTE'){
			$('.purchase_area').removeClass('d-none');	
			$('.return_area').addClass('d-none');	
		}else{
			$('.return_area').removeClass('d-none');
			$('.purchase_area').addClass('d-none');
		}
	}else if(group == 'CUSTOMER'){
		$('.pur_sale_area').html('SALES');	
		if(constant != 'DEBIT_NOTE'){
			$('.purchase_area').removeClass('d-none');	
			$('.return_area').addClass('d-none');	
		}else{
			$('.return_area').removeClass('d-none');
			$('.purchase_area').addClass('d-none');
		}
	}else{
		$('.purchase_area, .return_area').addClass('d-none');
	}
}
const set_party = (id = 0) =>{
	$('#vm_party_id').val(null).trigger('change');
	if(id){
		let path= `voucher/payment/get_party/${id}`;   
	    ajaxCall('GET',path,'','JSON',resp =>{
	        let {status, flag, data, msg} = resp;
	            if(status){
	                if(flag == 1){
	                    if(data && data.length != 0){
	                        let account_id   = data[0]['account_id'];
	                        let account_name = data[0]['account_name'].toUpperCase()+' - '+data[0]['account_code'].toUpperCase()+' - '+data[0]['account_mobile'];
	                        $('#vm_party_id').html(`<option value="${account_id}">${account_name}</option>`);
	                        get_account_balance();
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
const set_default = () => {
	$('#vm_bill_amt, #vm_return_amt, #vm_amt_received, #vm_selected_amt').val(0)
	$('#vm_balance_show').val('')
	$('#vm_balance_type').val('')
	$('#vm_total_amt, #vm_round_off').val(0).prop('readonly', '')
	$('#voucher_material_wrapper').html('')
}
const get_account = id => {
    $('#vm_constant').val('')
	if(id > 0){
		let path= `master/account/get_data/${id}`;   
    	ajaxCall('GET',path,'','JSON',resp =>{
	        let {status, flag, data, msg} = resp;
	        if(status){
	            if(flag == 1){
                	if(data && data.length != 0){
                		$('#vm_constant').val(data[0]['account_constant'])
                		get_account_balance()
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
		get_account_balance()
	}
}
const get_account_balance = () =>{
	set_default();
	remove_payment_notifier();
	set_purchase_return_area();
	const group  	= $('#vm_group').val();
	const acc_id 	= $('#vm_acc_id').val();
	const id  	 	= $('#vm_party_id').val();
	const constant  = $('#vm_constant').val();
	if(constant == 'BANK'){
		$('.bank_area').removeClass('d-none');
	}else{
		$('.bank_area').addClass('d-none');
	}
	$('.wrapper_area').addClass('d-none');
	if(id != null && id != ''){
		let path= `master/account/get_account_balance/${id}`;   
    	ajaxCall('GET',path,'','JSON',resp =>{
	        let {status, flag, data, msg} = resp;
	        if(status){
	            if(flag == 1){
                	$('#vm_bill_amt').val(data['bill_amt'] ? data['bill_amt'] : 0);
                	$('#vm_return_amt').val(data['return_amt'] ? data['return_amt'] : 0);
	                if(data['bal_data']){
	                	$('#vm_balance_type').val(data['bal_data'].type)
	                	$('#vm_balance_show').val(`${data['bal_data'].amt} ${data['bal_data'].type}`)
	                	$('#vm_balance').val(`${data['bal_data'].amt}`)
	                }
		            if(data['pur_data'] && data['pur_data'].length != 0){
                		if(group == 'SUPPLIER' && constant != 'DEBIT_NOTE'){
	                		add_pur_to_payment_wrapper(data['pur_data']);
	                		$('.wrapper_area').removeClass('d-none');
                		}
	                }
                	if(data['ret_data'] && data['ret_data'].length != 0){
                		if(group == 'SUPPLIER' && constant == 'DEBIT_NOTE'){
	                		add_ret_to_payment_wrapper(data['ret_data']);
	                		$('.wrapper_area').removeClass('d-none');
                		}
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
const get_bill_for_adjustment = () =>{
	const group 	= $('#vm_group').val();
	const acc_id 	= $('#vm_acc_id').val();
	const id  		= $('#vm_party_id').val();
	const constant  = $('#vm_constant').val();
    if(id != null && id != ''){
		let path= `master/account/get_account_balance/${id}`;   
    	ajaxCall('GET',path,'','JSON',resp =>{
	        let {status, flag, data, msg} = resp;
	        if(status){
	            if(flag == 1){
		            if(data['pur_data'] && data['pur_data'].length != 0){
                		if(group == 'SUPPLIER' && constant != 'DEBIT_NOTE'){
	                		add_pur_to_payment_wrapper(data['pur_data']);
	                		$('.wrapper_area').removeClass('d-none');
                		}
	                }
                	if(data['ret_data'] && data['ret_data'].length != 0){
                		if(group == 'SUPPLIER' && constant == 'DEBIT_NOTE'){
	                		add_ret_to_payment_wrapper(data['ret_data']);
	                		$('.wrapper_area').removeClass('d-none');
                		}
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
	let total_tr 		= $('#payment_material_wrapper tr').length;
	for (let i = 1; i <= total_tr; i++){
		let id = $('#payment_material_wrapper tr:nth-child('+i+')').attr('id');
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
	    		$(`#vt_return_amt_${cnt}`).val(0)
	    		$(`#vt_bill_clear_${cnt}`).val('NO')
	    	}
        }
	}
	let total_checked 	= $('.checkboxes:checked').length;
	$(`#parent_checkbox`).prop('checked', total_tr == total_checked);
	calculate_master_total()
}
const calculate_single_trans = cnt => {
	let acc_id 	= $('#vm_acc_id').val();
	let constant= $('#vm_constant').val();
	if(constant != 'DEBIT_NOTE'){
		let final_amt 		= $(`#vt_total_amt_${cnt}`).val();
		let allocated_amt 	= $(`#vt_allocated_amt_${cnt}`).val();
		let round_off_amt 	= $(`#vt_allocated_round_off_${cnt}`).val();
		let balance_amt 	= $(`#vt_balance_amt_${cnt}`).val();

		let adjust_amt 		= parseFloat( ('0' + $(`#vt_adjust_amt_${cnt}`).val()).replace(/[^0-9-\.]/g, ''), 10 )
		if(isNaN(adjust_amt) || adjust_amt == '') adjust_amt = 0;

		let adjust_round_off= parseFloat( ('0' + $(`#vt_adjust_round_off_${cnt}`).val()).replace(/[^0-9-\.]/g, ''), 10 )
		if(isNaN(adjust_round_off) || adjust_round_off == '') adjust_round_off = 0;

		let bal_amt = parseFloat(final_amt) - (parseFloat(allocated_amt) + parseFloat(round_off_amt)) - (parseFloat(adjust_amt) + parseFloat(adjust_round_off));
		if(isNaN(bal_amt) || bal_amt == '') bal_amt = 0;
		$(`#vt_balance_amt_show_${cnt}`).val(bal_amt.toFixed(2));		
		if(bal_amt < 0){
			$(`#vt_balance_amt_show_${cnt}`).addClass('text-danger');	        	
        }else{
        	$(`#vt_balance_amt_show_${cnt}`).removeClass('text-danger').addClass('text-blue');
        }
	}
}
const calculate_return_trans = cnt => {
	let acc_id 	= $('#vm_acc_id').val();
	let constant= $('#vm_constant').val();
	if(constant == 'DEBIT_NOTE'){
		let final_amt 		= $(`#vt_total_amt_${cnt}`).val();
		if(isNaN(final_amt) || final_amt == '') final_amt = 0;

		let return_amt 	= $(`#vt_return_amt_${cnt}`).val();
		if(isNaN(return_amt) || return_amt == '') return_amt = 0;

		let actual_bill_amt = parseFloat(final_amt) - parseFloat(return_amt);
		if(isNaN(actual_bill_amt) || actual_bill_amt == '') actual_bill_amt = 0;
		
		$(`#vt_balance_amt_show_${cnt}`).val(actual_bill_amt.toFixed(2));		
		if(actual_bill_amt < 0){
			$(`#vt_balance_amt_show_${cnt}`).addClass('text-danger');	        	
        }else{
        	$(`#vt_balance_amt_show_${cnt}`).removeClass('text-danger').addClass('text-blue');
        }
	}
}
const calculate_master_total = () =>{
	remove_payment_notifier();
	let acc_id 					= $('#vm_acc_id').val();
	let constant 				= $('#vm_constant').val();
	let total_tr  				= $('#payment_material_wrapper tr').length;
	let total_adjust_amt		= 0;
	let total_return_amt		= 0;
	let total_adjust_round_off 	= 0;
	for (let i = 1; i <= total_tr; i++){
		let id = $('#payment_material_wrapper tr:nth-child('+i+')').attr('id');
        let lastunderscore = id.lastIndexOf("_");
        let cnt = id.substring(lastunderscore+1);

        if($(`#child_checkbox_${cnt}`).is(':checked')){
			let adjust_amt = $(`#vt_adjust_amt_${cnt}`).val();
			if(isNaN(adjust_amt) || adjust_amt == '') adjust_amt = 0;

			let return_amt = $(`#vt_return_amt_${cnt}`).val();
			if(isNaN(return_amt) || return_amt == '') return_amt = 0;
			
			total_adjust_amt = parseFloat(total_adjust_amt) + parseFloat(adjust_amt);
			if(isNaN(total_adjust_amt) || total_adjust_amt == '') total_adjust_amt = 0;

			total_return_amt = parseFloat(total_return_amt) + parseFloat(return_amt);
			if(isNaN(total_return_amt) || total_return_amt == '') total_return_amt = 0;

			let adjust_round_off = $(`#vt_adjust_round_off_${cnt}`).val();
			if(isNaN(adjust_round_off) || adjust_round_off == '') adjust_round_off = 0;
			
			total_adjust_round_off = parseFloat(total_adjust_round_off) + parseFloat(adjust_round_off);
			if(isNaN(total_adjust_round_off) || total_adjust_round_off == '') total_adjust_round_off = 0;
    	}
	}

	let bal_amt = $('#vm_balance').val();
	if(isNaN(bal_amt) || bal_amt == '') bal_amt = 0;		

	let total_amt = $('#vm_total_amt').val();
	if(isNaN(total_amt) || total_amt == '') total_amt = 0;		

	let pending_total_amt = parseFloat(total_amt) - (parseFloat(total_adjust_amt) + parseFloat(total_return_amt));
	if(isNaN(pending_total_amt) || pending_total_amt == '') pending_total_amt = 0;

	let round_off = $('#vm_round_off').val();
	if(isNaN(round_off) || round_off == '') round_off = 0;		

	let pending_round_off = parseFloat(round_off) - parseFloat(total_adjust_round_off);
	if(isNaN(pending_round_off) || pending_round_off == '') pending_round_off = 0;	

	for (let i = 1; i <= total_tr; i++){
		let id = $('#payment_material_wrapper tr:nth-child('+i+')').attr('id');
        let lastunderscore = id.lastIndexOf("_");
        let cnt = id.substring(lastunderscore+1);

        if($(`#child_checkbox_${cnt}`).is(':checked')){
			let bal_amt = parseFloat( ('0' + $(`#vt_balance_amt_${cnt}`).val()).replace(/[^0-9-\.]/g, ''), 10 );
			if(isNaN(bal_amt) || bal_amt == '') bal_amt = 0;					

			let adjust_amt = parseFloat( ('0' + $(`#vt_adjust_amt_${cnt}`).val()).replace(/[^0-9-\.]/g, ''), 10 );
			if(isNaN(adjust_amt) || adjust_amt == '') adjust_amt = 0;					

			let return_amt = parseFloat( ('0' + $(`#vt_return_amt_${cnt}`).val()).replace(/[^0-9-\.]/g, ''), 10 );
			if(isNaN(return_amt) || return_amt == '') return_amt = 0;					

			let round_off = parseFloat( ('0' + $(`#vt_adjust_round_off_${cnt}`).val()).replace(/[^0-9-\.]/g, ''), 10 );
			if(isNaN(round_off) || round_off == '') round_off = 0;					

			if(constant != 'DEBIT_NOTE'){
				if(adjust_amt < 1){
					if(bal_amt <= pending_total_amt){
						$(`#vt_adjust_amt_${cnt}`).val(bal_amt > 0 ?  bal_amt.toFixed(2) : 0);
						pending_total_amt = parseFloat(pending_total_amt) - parseFloat(bal_amt);
						if(isNaN(pending_total_amt) || pending_total_amt == '') pending_total_amt = 0;
					}else{
						$(`#vt_adjust_amt_${cnt}`).val(pending_total_amt > 0 ? pending_total_amt.toFixed(2) : 0);
						pending_total_amt = parseFloat(pending_total_amt) - parseFloat(pending_total_amt);
						if(isNaN(pending_total_amt) || pending_total_amt == '') pending_total_amt = 0;
					}
				}
				let temp = parseFloat(bal_amt) - parseFloat($(`#vt_adjust_amt_${cnt}`).val());
				if(isNaN(temp) || temp == '') temp = 0;

				if(round_off < 1){
					if(temp <= pending_round_off){
						$(`#vt_adjust_round_off_${cnt}`).val(temp > 0 ?  temp.toFixed(2) : 0);
						pending_round_off = parseFloat(pending_round_off) - parseFloat(temp);
						if(isNaN(pending_round_off) || pending_round_off == '') pending_round_off = 0;
					}else{
						$(`#vt_adjust_round_off_${cnt}`).val(pending_round_off > 0 ? pending_round_off.toFixed(2) : 0);
						pending_round_off = parseFloat(pending_round_off) - parseFloat(pending_round_off);
						if(isNaN(pending_round_off) || pending_round_off == '') pending_round_off = 0;
					}
				}
			}else{
				if(return_amt < 1){
					if(bal_amt <= pending_total_amt){
						$(`#vt_return_amt_${cnt}`).val(bal_amt > 0 ?  bal_amt.toFixed(2) : 0);
						pending_total_amt = parseFloat(pending_total_amt) - parseFloat(bal_amt);
						if(isNaN(pending_total_amt) || pending_total_amt == '') pending_total_amt = 0;
					}else{
						$(`#vt_return_amt_${cnt}`).val(pending_total_amt > 0 ? pending_total_amt.toFixed(2) : 0);
						pending_total_amt = parseFloat(pending_total_amt) - parseFloat(pending_total_amt);
						if(isNaN(pending_total_amt) || pending_total_amt == '') pending_total_amt = 0;
					}
				}
			}
    	}
		calculate_single_trans(cnt)
		calculate_return_trans(cnt)
	}	

	calculate_master_balance();
}
const calculate_master_balance = () =>{
	let acc_id 	= $(`#vm_acc_id`).val();
	let constant= $(`#vm_constant`).val();

	let bal_amt = $(`#vm_balance`).val();
	if(isNaN(bal_amt) || bal_amt == '') bal_amt = 0;

	let total_amt = parseFloat( ('0' + $(`#vm_total_amt`).val()).replace(/[^0-9-\.]/g, ''), 10 );
	if(isNaN(total_amt) || total_amt == '') total_amt = 0;

	let round_off = parseFloat( ('0' + $(`#vm_round_off`).val()).replace(/[^0-9-\.]/g, ''), 10 );
	if(isNaN(round_off) || round_off == '') round_off = 0;

	let bal_type = $(`#vm_balance_type`).val();
	let close_amt = 0;
	if(bal_type == TO_PAY){
		if(constant == 'DEBIT_NOTE'){
			close_amt = parseFloat(bal_amt) + parseFloat(total_amt) + parseFloat(round_off);
		}else{
			close_amt = parseFloat(bal_amt) - parseFloat(total_amt) - parseFloat(round_off);
		}
	}else{
		if(constant == 'DEBIT_NOTE'){
			close_amt = parseFloat(bal_amt) - parseFloat(total_amt) - parseFloat(round_off);
		}else{
			close_amt = parseFloat(bal_amt) + parseFloat(total_amt) + parseFloat(round_off);
		}
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
const add_pur_to_payment_wrapper = purchases => {
	let data = `
		<thead>
			<tr style="font-size: 15px;">
                <th width="2%"><input type="checkbox" id="parent_checkbox" onclick="select_deselect()" /></th>
                <th width="5%">BILL NO</th>
                <th width="7%">BILL DATE</th>
                <th width="6%">TOTAL QTY</th>
                <th width="6%">BILL AMT</th>
                <th width="15%" colspan="2">ALLOCATED AMT & ROUND OFF</th>
                <th width="8%">ADJUST AMT</th>
                <th width="10%">ROUND OFF AMT</th>
                <th width="10%">CURRENT BAL. AMT</th>
            </tr>
        </thead>
        <tbody id="payment_material_wrapper">
	`;
	purchases.forEach(purchase => {
		const {pm_id, pm_bill_no, pm_bill_date, pm_total_qty, pm_final_amt, pm_allocated_amt, pm_allocated_round_off, pm_return_amt} = purchase
		const balance_amt = parseFloat(pm_final_amt) - (parseFloat(pm_allocated_amt) + parseFloat(pm_allocated_round_off));
		data += `
			<tr id="rowid_${pm_id}" class="floating-form">
				<td class="floating-label">
					<input type="hidden" id="vt_id_${pm_id}" name="vt_id[]" value="0" />
					<input type="hidden" id="vt_bill_clear_${pm_id}" name="vt_bill_clear[]" value="NO" />
					<input type="checkbox" class="checkboxes" id="child_checkbox_${pm_id}" onclick="select_deselect(${pm_id})" />
				</td>
				<td class="floating-label">
					<input type="hidden" id="vt_pm_id_${pm_id}" name="vt_pm_id[]" value="${pm_id}" />
					<input type="text" class="form-control floating-input" name="vt_bill_no[]" id="vt_bill_no_${pm_id}" value="${pm_bill_no}" readonly />
				</td>
				<td class="floating-label">
					<input type="text" class="form-control floating-input" name="vt_bill_date[]" id="vt_bill_date_${pm_id}" value="${pm_bill_date}" readonly />
				</td>
				<td class="floating-label">
					<input type="number" class="form-control floating-input" name="vt_total_qty[]" id="vt_total_qty_${pm_id}" value="${pm_total_qty}" readonly />
					<input type="hidden" name="vt_return_qty[]" id="vt_return_qty_${pm_id}" value="0" readonly />
				</td>
				<td class="floating-label">
					<input type="number" class="form-control floating-input" name="vt_total_amt[]" id="vt_total_amt_${pm_id}" value="${pm_final_amt}" readonly />
				</td>
				<td class="floating-label">
					<input type="number" class="form-control floating-input" name="vt_allocated_amt[]" id="vt_allocated_amt_${pm_id}" value="${pm_allocated_amt}" readonly />
				</td>
				<td class="floating-label">
					<input type="number" class="form-control floating-input" name="vt_allocated_round_off[]" id="vt_allocated_round_off_${pm_id}" value="${pm_allocated_round_off}" readonly />
				</td>
				<td class="floating-label">
					<input type="number" class="form-control floating-input" name="vt_adjust_amt[]" id="vt_adjust_amt_${pm_id}" value="0" min="0" oninput="this.value = Math.abs(this.value)" onkeyup="calculate_single_trans(${pm_id})" />
					<input type="hidden" name="vt_return_amt[]" id="vt_return_amt_${pm_id}" value="${pm_return_amt}"/>
				</td>
				<td class="floating-label">
					<input type="number" class="form-control floating-input" name="vt_adjust_round_off[]" id="vt_adjust_round_off_${pm_id}" value="0" min="0" oninput="this.value = Math.abs(this.value)" onkeyup="calculate_single_trans(${pm_id})" />
				</td>
				<td class="floating-label">
					<input type="number" class="form-control floating-input" id="vt_balance_amt_show_${pm_id}" value="${balance_amt}" readonly />
					<input type="hidden" id="vt_balance_amt_${pm_id}" value="${balance_amt}" readonly />
				</td>
			</tr>
		`;
	})
	data +=`
        </tbody>
	`;
	$('#voucher_material_wrapper').html(data)
}
const add_ret_to_payment_wrapper = purchases => {
	let data = `
		<thead>
			<tr style="font-size: 15px;">
                <th width="2%"><input type="checkbox" id="parent_checkbox" onclick="select_deselect()" /></th>
                <th width="10%">BILL NO</th>
                <th width="10%">BILL DATE</th>
                <th width="10%">TOTAL QTY</th>
                <th width="10%">BILL AMT</th>
                <th width="10%">ADJUSTED BILL AMT</th>
                <th width="10%">RETURN QTY</th>
                <th width="10%">DEBIT NOTE AMT</th>
                <th width="10%">ACTUAL BILL AMT</th>
            </tr>
        </thead>
        <tbody id="payment_material_wrapper">
	`;
	purchases.forEach(purchase => {
		const {pm_id, pm_bill_no, pm_bill_date, pm_total_qty, pm_final_amt, pm_allocated_amt, pm_allocated_round_off, pm_return_amt, return_qty} = purchase
		const adjusted_amt= parseFloat(pm_allocated_amt) + parseFloat(pm_allocated_round_off);
		const balance_amt = parseFloat(pm_final_amt) - (parseFloat(pm_allocated_amt) + parseFloat(pm_allocated_round_off));
		const actual_bill_amt = parseFloat(pm_final_amt) - parseFloat(pm_return_amt);
		data += `
			<tr id="rowid_${pm_id}" class="floating-form">
				<td class="floating-label">
					<input type="hidden" id="vt_id_${pm_id}" name="vt_id[]" value="0" />
					<input type="hidden" id="vt_bill_clear_${pm_id}" name="vt_bill_clear[]" value="NO" />
					<input type="checkbox" class="checkboxes" id="child_checkbox_${pm_id}" onclick="select_deselect(${pm_id})" />
				</td>
				<td class="floating-label">
					<input type="hidden" id="vt_pm_id_${pm_id}" name="vt_pm_id[]" value="${pm_id}" />
					<input type="text" class="form-control floating-input" name="vt_bill_no[]" id="vt_bill_no_${pm_id}" value="${pm_bill_no}" readonly />
				</td>
				<td class="floating-label">
					<input type="text" class="form-control floating-input" name="vt_bill_date[]" id="vt_bill_date_${pm_id}" value="${pm_bill_date}" readonly />
				</td>
				<td class="floating-label">
					<input type="number" class="form-control floating-input" name="vt_total_qty[]" id="vt_total_qty_${pm_id}" value="${pm_total_qty}" readonly />
				</td>
				<td class="floating-label">
					<input type="number" class="form-control floating-input" name="vt_total_amt[]" id="vt_total_amt_${pm_id}" value="${pm_final_amt}" readonly />
				</td>
				<td class="floating-label">
					<input type="number" class="form-control floating-input" id="vt_adjusted_amt_${pm_id}" value="${adjusted_amt}" readonly />
					<input type="hidden" name="vt_allocated_amt[]" id="vt_allocated_amt_${pm_id}" value="0" />
					<input type="hidden" name="vt_allocated_round_off[]" id="vt_allocated_round_off_${pm_id}" value="0" />
				</td>
				<td class="floating-label">
					<input type="number" class="form-control floating-input" name="vt_return_qty[]" id="vt_return_qty_${pm_id}" value="${return_qty}" readonly />
				</td>
				<td class="floating-label">
					<input type="number" class="form-control floating-input" name="vt_return_amt[]" id="vt_return_amt_${pm_id}" value="${pm_return_amt}"  min="0" oninput="this.value = Math.abs(this.value)" onkeyup="calculate_return_trans(${pm_id})"/>
					<input type="hidden" name="vt_adjust_amt[]" id="vt_adjust_amt_${pm_id}" value="0" />
					<input type="hidden" name="vt_adjust_round_off[]" id="vt_adjust_round_off_${pm_id}" value="0" />
					<input type="hidden" id="vt_balance_amt_${pm_id}" value="${balance_amt}" readonly />
				</td>
				<td class="floating-label">
					<input type="number" class="form-control floating-input" id="vt_balance_amt_show_${pm_id}" value="${actual_bill_amt}" readonly />
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
	let total_tr 		= $('#payment_material_wrapper tr').length;
	for (let i = 1; i <= total_tr; i++){
		let id = $('#payment_material_wrapper tr:nth-child('+i+')').attr('id');
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
	let total_tr 		= $('#payment_material_wrapper tr').length;
	for (let i = 1; i <= total_tr; i++){
		let id = $('#payment_material_wrapper tr:nth-child('+i+')').attr('id');
	    let lastunderscore = id.lastIndexOf("_");
	    let cnt = id.substring(lastunderscore+1);

		if($(`#child_checkbox_${cnt}`).is(':checked')){
			let bal_amt = parseFloat( ('0' + $(`#vt_balance_amt_${cnt}`).val()).replace(/[^0-9-\.]/g, ''), 10 );
			if(isNaN(bal_amt) || bal_amt == '') bal_amt = 0;					

			let total_amt = parseFloat( ('0' + $(`#vt_total_amt_${cnt}`).val()).replace(/[^0-9-\.]/g, ''), 10 );
			if(isNaN(total_amt) || total_amt == '') total_amt = 0;					

			let adjust_amt = parseFloat( ('0' + $(`#vt_adjust_amt_${cnt}`).val()).replace(/[^0-9-\.]/g, ''), 10 );
			if(isNaN(adjust_amt) || adjust_amt == '') adjust_amt = 0;					

			let round_off = parseFloat( ('0' + $(`#vt_adjust_round_off_${cnt}`).val()).replace(/[^0-9-\.]/g, ''), 10 );
			if(isNaN(round_off) || round_off == '') round_off = 0;					

			let return_amt = parseFloat( ('0' + $(`#vt_return_amt_${cnt}`).val()).replace(/[^0-9-\.]/g, ''), 10 );
			if(isNaN(return_amt) || return_amt == '') return_amt = 0;					

			if($('#vm_constant').val() != 'DEBIT_NOTE'){
				let balance_amt = parseFloat(bal_amt) - parseFloat(adjust_amt) - parseFloat(round_off);
				if(isNaN(balance_amt) || balance_amt == '') balance_amt = 0;

				if(balance_amt < 0) return false;					
			}else{
				let balance_amt = parseFloat(total_amt) - parseFloat(return_amt);
				if(isNaN(balance_amt) || balance_amt == '') balance_amt = 0;

				if(balance_amt < 0) return false;					
			}

			
    	}    
    }
    return true;
}
const remove_payment_notifier = () =>{
    notifier('vm_acc_id')
    notifier('vm_party_id')
    notifier('vm_total_amt')
    notifier('vm_round_off')
}
const add_update_payment = (id) =>{
    remove_payment_notifier()
    let check   	= true;
    let check_trans = true;
    if($("#vm_acc_id").val() == 0){
        notifier('vm_acc_id', 'Required')
        check = false;
    }
    if($("#vm_party_id").val() == null){
        notifier('vm_party_id', 'Required')
        check = false;
    }
    if($("#vm_total_amt").val() <= 0){
        notifier('vm_total_amt', 'Required')
        check = false;
    }
    if($('.checkboxes:checked').length != 0){
    	if($("#vm_constant").val() != 'DEBIT_NOTE'){
	    	if(!is_amt_matched('vm_total_amt', 'vt_adjust_amt')){
	    		notifier('vm_total_amt', 'Total amt and adjusted amt are mismatch.')
	    		check = false;
		    }
	    }else{
	    	if(!is_amt_matched('vm_total_amt', 'vt_return_amt')){
	    		notifier('vm_total_amt', 'Total amt and debit note amt are mismatch.')
	    		check = false;
		    }
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
        callToastify('error', 'Enter proper adjustment or round off amt.', 'right')
        $("body, html").animate({'scrollTop':0},1000);
    }else{
        let path        = "voucher/payment/add_update/"+id;   
        let form_data   = $("#payment_form").serialize();
        ajaxCall('POST',path,form_data,'JSON',resp =>{
            let {status, flag, msg} = resp;
            if(status){
                if(flag == 1){
                    if(id == 0) {
                    } else {
                    }
                    remove_payment_notifier()
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

// payment mode
const get_payment_mode_data = () => {
	let title = `<p>payment mode</p>`;
	let subtitle = '';
	let body = ``;
	let footer = `<button 
					type="button" 
					id="sbt_btn" 
					class="btn btn-md btn-primary btn-block text-uppercase mx-3" 
					onclick="toggle_payment_mode_popup()"
				>close</button>`;
	$(`#payment_mode_wrapper #right-panel-title `).html(title);
	$(`#payment_mode_wrapper #right-panel-subtitle `).html(subtitle);
	$(`#payment_mode_wrapper #right-panel-body `).html(body);
	$(`#payment_mode_wrapper #right-panel-footer `).html(footer);
	const id = $("#vm_id").val();
	const path = `${link}/${sub_link}/get_payment_mode_data/${id}`;
	ajaxCall(
		"GET",
		path,
		"",
		"JSON",
		(resp) => {
			if (handle_response(resp)) {
				const { data, msg } = resp;
				if (data && data.length != 0) { 
					const { payment_mode_data } = data; 
			        if (payment_mode_data && payment_mode_data.length != 0) { 
						const { pay_modes } = get_pay_modes(payment_mode_data);
						 let body = `<div class="row pt-2">
		                        <div class="col-12">
		                          <div style="max-height: 65vh; overflow-x: auto;">
		                            <table class="table table-sm w-100">
		                              <tbody id="payment_mode_tbody">
		                                ${pay_modes}
		                              </tbody>
		                            </table>
		                          </div>              
		                        </div>              
		                    </div> `;		
						$(`#payment_mode_wrapper #right-panel-body `).html(body);

					}
				}
			}
		},
		(errmsg) => {
			console.log(errmsg);
		}
	);
};
const get_pay_modes = (data) => {
	let pay_modes = ``;
	data.forEach((row) => {
		const { ppmt_id, ppmt_amt, ppmt_payment_mode_id, payment_mode_name } = row;
		pay_modes += `<tr id="rowpm_${ppmt_payment_mode_id}">
						<td width="10%" class="border-0 font-weight-bold"></td>
						<td width="30%" class="border-0 font-weight-bold">${payment_mode_name} : </td>
						<td width="50%" class="border-0 floating-label">
							<input 
								type="hidden"
								id="ppmt_id_${ppmt_payment_mode_id}" 
								name="ppmt_id[${ppmt_payment_mode_id}]" 
								value="${ppmt_id}" 
							/>
							<input 
								type="hidden"
								id="ppmt_payment_mode_id_${ppmt_payment_mode_id}" 
								name="ppmt_payment_mode_id[${ppmt_payment_mode_id}]" 
								value="${ppmt_payment_mode_id}" 
							/>
							<input 
								type="number" 
								class="form-control floating-input" 
								id="ppmt_amt_${ppmt_payment_mode_id}" 
								name="ppmt_amt[${ppmt_payment_mode_id}]" 
								value="${ppmt_amt}"
								onkeyup="calculate_payment_amt()"
								placeholder=" " 
								autocomplete="off" 
							/>
						</td>
						<td width="10%" class="border-0 font-weight-bold"></td>
					</tr>`;
	});
	return { pay_modes };
};
const toggle_payment_mode_popup = () => {
	if ($(`#payment_mode_wrapper .right-panel`).hasClass("active")) {
		$(`#payment_mode_wrapper .right-panel `).removeClass("active");
	} else {
		$(`#payment_mode_wrapper .right-panel `).addClass("active");
	}
};
const calculate_payment_amt = () => {
	let payment_amt = 0;
	for (let i = 1; i <= $("#payment_mode_tbody > tr").length; i++) {
		let cnt = $(`#payment_mode_tbody > tr:nth-child(${i})`).attr("id");
		let explode = cnt.split("_");
		let id = explode[1];

		let amt = $(`#ppmt_amt_${id}`).val();
		if (isNaN(amt) || amt == "") amt = 0;

		payment_amt = parseFloat(payment_amt) + parseFloat(amt);
		if (isNaN(payment_amt) || payment_amt == "") payment_amt = 0;
	}
	$("._payment_amt").html(payment_amt);
	$(`#vm_total_amt`).val(payment_amt);
	
	calculate_master_total();
};
// payment mode