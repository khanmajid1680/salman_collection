$(document).ready(function () {
	$("#vm_branch")
		.select2(
			select2_default({
				url: `voucher/branch_payment/get_select2/_branch_id`,
				placeholder: "SELECT",
				param: true,
			})
		)
		.on("change", () => get_branch_balance());
	$("#vm_acc_id").on("change", (event) => get_account(event.target.value));

	$("#_entry_no")
		.select2(
			select2_default({
				url: `voucher/branch_payment/get_select2/_entry_no`,
				placeholder: "ENTRY NO",
			})
		)
		.on("change", () => trigger_search());

	$("#_account_name")
		.select2(
			select2_default({
				url: `voucher/branch_payment/get_select2/_account_name`,
				placeholder: "ACCOUNT",
			})
		)
		.on("change", () => trigger_search());

	$("#_party_name")
		.select2(
			select2_default({
				url: `voucher/branch_payment/get_select2/_party_name`,
				placeholder: "BRANCH",
			})
		)
		.on("change", () => trigger_search());
});
const set_default = () => {
	$("#vm_bill_amt, #vm_return_amt, #vm_amt_received, #vm_selected_amt").val(0);
	$("#vm_balance_show").val("");
	$("#vm_balance_type").val("");
	$("#vm_total_amt, #vm_round_off").val(0);
	$("#voucher_material_wrapper").html("");
};
const get_account = (id) => {
	$("#vm_constant").val("");
	if (id > 0) {
		let path = `master/account/get_data/${id}`;
		ajaxCall(
			"GET",
			path,
			"",
			"JSON",
			(resp) => {
				let { status, flag, data, msg } = resp;
				if (status) {
					if (flag == 1) {
						if (data && data.length != 0) {
							$("#vm_constant").val(data[0]["account_constant"]);
							get_branch_balance();
						}
					} else {
						response_error(flag, msg);
					}
				} else {
					session_expired();
				}
			},
			(errmsg) => {}
		);
	} else {
		get_branch_balance();
	}
};
const get_branch_balance = () => {
	set_default();
	remove_payment_notifier();
	const group = $("#vm_group").val();
	const acc_id = $("#vm_acc_id").val();
	const branch_id = $("#vm_branch").val();
	const constant = $("#vm_constant").val();
	if (constant == "BANK") {
		$(".bank_area").removeClass("d-none");
	} else {
		$(".bank_area").addClass("d-none");
	}
	$(".wrapper_area").addClass("d-none");
	if (branch_id) {
		let path = `voucher/branch_payment/get_branch_balance/${branch_id}`;
		ajaxCall(
			"GET",
			path,
			"",
			"JSON",
			(resp) => {
				let { status, flag, data, msg } = resp;
				if (status) {
					if (flag == 1) {
						const { acc_data, bal_data, bill_amt, grn_data } = data;

						$("#vm_bill_amt").val(bill_amt ? bill_amt : 0);
						if (bal_data) {
							$("#vm_balance_type").val(bal_data.type);
							$("#vm_balance_show").val(`${bal_data.amt} ${bal_data.type}`);
							$("#vm_balance").val(`${bal_data.amt}`);
						}
						if (acc_data && acc_data.length != 0) {
							$("#vm_party_id").val(acc_data[0]["account_id"]);
						}
						if (data["grn_data"] && data["grn_data"].length != 0) {
							add_grn_to_payment_wrapper(data["grn_data"]);
							$(".wrapper_area").removeClass("d-none");
						}
					} else {
						response_error(flag, msg);
					}
				} else {
					session_expired();
				}
			},
			(errmsg) => {}
		);
	}
};
const select_deselect = (count = 0) => {
	let total_tr = $("#payment_material_wrapper tr").length;
	for (let i = 1; i <= total_tr; i++) {
		let id = $("#payment_material_wrapper tr:nth-child(" + i + ")").attr("id");
		let lastunderscore = id.lastIndexOf("_");
		let cnt = id.substring(lastunderscore + 1);
		let balance_amt = $(`#vt_balance_amt_${cnt}`).val();
		if (count == 0) {
			let parent_checked = $(`#parent_checkbox`).is(":checked");
			$(`#child_checkbox_${cnt}`).prop("checked", parent_checked);
		}
		if (count == 0 || cnt == count) {
			if ($(`#child_checkbox_${cnt}`).is(":checked")) {
				$(`#vt_bill_clear_${cnt}`).val("YES");
				// total_checked++;
			} else {
				$(`#vt_adjust_amt_${cnt}`).val(0);
				$(`#vt_adjust_round_off_${cnt}`).val(0);
				$(`#vt_return_amt_${cnt}`).val(0);
				$(`#vt_bill_clear_${cnt}`).val("NO");
			}
		}
	}
	let total_checked = $(".checkboxes:checked").length;
	$(`#parent_checkbox`).prop("checked", total_tr == total_checked);
	calculate_master_total();
};
const calculate_master_total = () => {
	remove_payment_notifier();
	let acc_id 			= $("#vm_acc_id").val();
	let constant 		= $("#vm_constant").val();
	let total_tr 		= $("#payment_material_wrapper tr").length;
	let total_adjust_amt= 0;
	for (let i = 1; i <= total_tr; i++) {
		let id = $("#payment_material_wrapper tr:nth-child(" + i + ")").attr("id");
		let lastunderscore = id.lastIndexOf("_");
		let cnt = id.substring(lastunderscore + 1);
		
		if($(`#child_checkbox_${cnt}`).is(':checked')){
			let adjust_amt = $(`#vt_adjust_amt_${cnt}`).val();
			if(isNaN(adjust_amt) || adjust_amt == '') adjust_amt = 0;

			total_adjust_amt = parseFloat(total_adjust_amt) + parseFloat(adjust_amt);
			if(isNaN(total_adjust_amt) || total_adjust_amt == '') total_adjust_amt = 0;
			
    	}
	}
	let total_amt = $('#vm_total_amt').val();
	if(isNaN(total_amt) || total_amt == '') total_amt = 0;		

	let pending_total_amt = parseFloat(total_amt) - parseFloat(total_adjust_amt);
	if(isNaN(pending_total_amt) || pending_total_amt == '') pending_total_amt = 0;

	for (let i = 1; i <= total_tr; i++){
		let id = $('#payment_material_wrapper tr:nth-child('+i+')').attr('id');
        let lastunderscore = id.lastIndexOf("_");
        let cnt = id.substring(lastunderscore+1);

        if($(`#child_checkbox_${cnt}`).is(':checked')){
			let bal_amt = parseFloat( ('0' + $(`#vt_balance_amt_${cnt}`).val()).replace(/[^0-9-\.]/g, ''), 10 );
			if(isNaN(bal_amt) || bal_amt == '') bal_amt = 0;					

			let adjust_amt = parseFloat( ('0' + $(`#vt_adjust_amt_${cnt}`).val()).replace(/[^0-9-\.]/g, ''), 10 );
			if(isNaN(adjust_amt) || adjust_amt == '') adjust_amt = 0;					

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
    	}
		calculate_single_trans(cnt)
	}

	calculate_master_balance();
};
const calculate_single_trans = cnt => {
	let acc_id 			= $('#vm_acc_id').val();
	let constant		= $('#vm_constant').val();
	let final_amt 		= $(`#vt_total_amt_${cnt}`).val();
	let allocated_amt 	= $(`#vt_allocated_amt_${cnt}`).val();
	let balance_amt 	= $(`#vt_balance_amt_${cnt}`).val();

	let adjust_amt 		= parseFloat( ('0' + $(`#vt_adjust_amt_${cnt}`).val()).replace(/[^0-9-\.]/g, ''), 10 )
	if(isNaN(adjust_amt) || adjust_amt == '') adjust_amt = 0;

	let bal_amt = parseFloat(final_amt) - parseFloat(allocated_amt) - parseFloat(adjust_amt);
	if(isNaN(bal_amt) || bal_amt == '') bal_amt = 0;
	$(`#vt_balance_amt_show_${cnt}`).val(bal_amt.toFixed(2));		
	if(bal_amt < 0){
		$(`#vt_balance_amt_show_${cnt}`).addClass('text-danger');	        	
    }else{
    	$(`#vt_balance_amt_show_${cnt}`).removeClass('text-danger').addClass('text-blue');
    }
}
const calculate_master_balance = () => {
	let acc_id = $(`#vm_acc_id`).val();
	let constant = $(`#vm_constant`).val();

	let bal_amt = $(`#vm_balance`).val();
	if (isNaN(bal_amt) || bal_amt == "") bal_amt = 0;

	let total_amt = parseFloat(
		("0" + $(`#vm_total_amt`).val()).replace(/[^0-9-\.]/g, ""),
		10
	);
	if (isNaN(total_amt) || total_amt == "") total_amt = 0;

	let round_off = parseFloat(
		("0" + $(`#vm_round_off`).val()).replace(/[^0-9-\.]/g, ""),
		10
	);
	if (isNaN(round_off) || round_off == "") round_off = 0;

	let bal_type = $(`#vm_balance_type`).val();
	let close_amt = 0;
	if (bal_type == TO_PAY) {
		close_amt =
			parseFloat(bal_amt) - parseFloat(total_amt) - parseFloat(round_off);
	} else {
		close_amt =
			parseFloat(bal_amt) + parseFloat(total_amt) + parseFloat(round_off);
	}
	if (isNaN(close_amt) || close_amt == "") close_amt = 0;
	if (close_amt < 0) {
		close_amt = Math.abs(close_amt);
		if (bal_type == TO_PAY) {
			bal_type = TO_RECEIVE;
		} else {
			bal_type = TO_PAY;
		}
	}

	$("#vm_balance_show").val(`${close_amt} ${bal_type}`);

	if (total_amt > 0) {
		$(".master_block_btn").prop("disabled", false);
	} else {
		$(".master_block_btn").prop("disabled", true);
	}
};
const add_grn_to_payment_wrapper = (grns) => {
	let data = `
		<thead>
			<tr style="font-size: 15px;">
                <th width="2%"><input type="checkbox" id="parent_checkbox" onclick="select_deselect()" /></th>
                <th width="5%">ENTRY NO</th>
                <th width="7%">ENTRY DATE</th>
                <th width="6%">TOTAL QTY</th>
                <th width="6%">GRN AMT</th>
                <th width="6%">ALLOCATED AMT</th>
                <th width="8%">ADJUST AMT</th>
                <th width="10%">CURRENT BAL. AMT</th>
            </tr>
        </thead>
        <tbody id="payment_material_wrapper">
	`;
	grns.forEach((grn) => {
		const {
			gm_id,
			gm_entry_no,
			gm_entry_date,
			gm_total_qty,
			gm_final_amt,
			gm_allocated_amt,
			gm_allocated_round_off,
		} = grn;
		const balance_amt =
			parseFloat(gm_final_amt) -
			(parseFloat(gm_allocated_amt) + parseFloat(gm_allocated_round_off));
		data += `
			<tr id="rowid_${gm_id}" class="floating-form">
				<td width="2%" class="floating-label">
					<input type="hidden" id="vt_id_${gm_id}" name="vt_id[]" value="0" />
					<input type="hidden" id="vt_bill_clear_${gm_id}" name="vt_bill_clear[]" value="NO" />
					<input type="checkbox" class="checkboxes" id="child_checkbox_${gm_id}" onclick="select_deselect(${gm_id})" />
				</td>
				<td width="5%" class="floating-label">
					<input type="hidden" id="vt_gm_id_${gm_id}" name="vt_gm_id[]" value="${gm_id}" />
					<input type="text" class="form-control floating-input" name="vt_bill_no[]" id="vt_bill_no_${gm_id}" value="${gm_entry_no}" readonly />
				</td>
				<td width="7%" class="floating-label">
					<input type="text" class="form-control floating-input" name="vt_bill_date[]" id="vt_bill_date_${gm_id}" value="${gm_entry_date}" readonly />
				</td>
				<td width="6%" class="floating-label">
					<input type="number" class="form-control floating-input" name="vt_total_qty[]" id="vt_total_qty_${gm_id}" value="${gm_total_qty}" readonly />
				</td>
				<td width="6%" class="floating-label">
					<input type="number" class="form-control floating-input" name="vt_total_amt[]" id="vt_total_amt_${gm_id}" value="${gm_final_amt}" readonly />
				</td>
				<td width="6%" class="floating-label">
					<input type="number" class="form-control floating-input" name="vt_allocated_amt[]" id="vt_allocated_amt_${gm_id}" value="${gm_allocated_amt}" readonly />
					<input type="hidden" name="vt_allocated_round_off[]" id="vt_allocated_round_off_${gm_id}" value="${gm_allocated_round_off}" />
				</td>
				<td width="8%" class="floating-label">
					<input type="number" class="form-control floating-input" name="vt_adjust_amt[]" id="vt_adjust_amt_${gm_id}" value="0" readonly />
					<input type="hidden" name="vt_adjust_round_off[]" id="vt_adjust_round_off_${gm_id}" value="0" />
				</td>
				<td width="10%" class="floating-label">
					<input type="number" class="form-control floating-input" id="vt_balance_amt_show_${gm_id}" value="${balance_amt}" readonly />
					<input type="hidden" id="vt_balance_amt_${gm_id}" value="${balance_amt}" readonly />
				</td>
			</tr>
		`;
	});
	data += `
        </tbody>
	`;
	$("#voucher_material_wrapper").html(data);
};
const is_amt_matched = (master, trans) => {
	let master_amt = $(`#${master}`).val();
	if (isNaN(master_amt) || master_amt == "") master_amt = 0;
	let total_trans_amt = 0;
	let total_tr = $("#payment_material_wrapper tr").length;
	for (let i = 1; i <= total_tr; i++) {
		let id = $("#payment_material_wrapper tr:nth-child(" + i + ")").attr("id");
		let lastunderscore = id.lastIndexOf("_");
		let cnt = id.substring(lastunderscore + 1);

		if ($(`#child_checkbox_${cnt}`).is(":checked")) {
			let trans_amt = $(`#${trans}_${cnt}`).val();
			if (isNaN(trans_amt) || trans_amt == "") trans_amt = 0;

			total_trans_amt = parseFloat(total_trans_amt) + parseFloat(trans_amt);
			if (isNaN(total_trans_amt) || total_trans_amt == "") total_trans_amt = 0;
		}
	}

	if (master_amt == total_trans_amt) return true;
	return false;
};
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

			let balance_amt = parseFloat(bal_amt) - parseFloat(adjust_amt) - parseFloat(round_off);
			if(isNaN(balance_amt) || balance_amt == '') balance_amt = 0;

			if(balance_amt < 0) return false;					
    	}    
    }
    return true;
}
const remove_payment_notifier = () => {
	notifier("vm_acc_id");
	notifier("vm_branch");
	notifier("vm_total_amt");
	notifier("vm_round_off");
};
const add_update_payment = (id) => {
	remove_payment_notifier();
	let check = true;
	let check_trans = true;
	if ($("#vm_acc_id").val() == 0) {
		notifier("vm_acc_id", "Required");
		check = false;
	}
	if ($("#vm_branch").val() == null) {
		notifier("vm_branch", "Required");
		check = false;
	} else {
		if ($("#vm_party_id").val() == 0 || $("#vm_party_id").val() == "") {
			notifier("vm_branch", "Account not found");
			check = false;
		}
	}
	if ($("#vm_total_amt").val() <= 0) {
		notifier("vm_total_amt", "Required");
		check = false;
	}
	if ($(".checkboxes:checked").length != 0) {
		if (!is_amt_matched("vm_total_amt", "vt_adjust_amt")) {
			notifier("vm_total_amt", "Total amt and adjusted amt are mismatch.");
			check = false;
		}
		if (!is_amt_matched("vm_round_off", "vt_adjust_round_off")) {
			notifier(
				"vm_round_off",
				"Round off amt and adjusted off amt are mismatch."
			);
			check = false;
		}
	}
	if (!check_current_balance()) {
		check_trans = false;
	}

	if (!check) {
		callToastify(
			"error",
			"Oh snap ! You forgot to enter some information",
			"right"
		);
		$("body, html").animate({ scrollTop: 0 }, 1000);
	} else if (!check_trans) {
		callToastify("error", "Grn amt and adjusted amt should be same.", "right");
		$("body, html").animate({ scrollTop: 0 }, 1000);
	} else if (id == 0 && $(".checkboxes:checked").length == 0) {
		callToastify("error", "Please select grn to adjust amt.", "right");
		$("body, html").animate({ scrollTop: 0 }, 1000);
	} else {
		let path = "voucher/branch_payment/add_update/" + id;
		let form_data = $("#payment_form").serialize();
		ajaxCall(
			"POST",
			path,
			form_data,
			"JSON",
			(resp) => {
				let { status, flag, msg } = resp;
				if (status) {
					if (flag == 1) {
						if (id == 0) {
						} else {
						}
						remove_payment_notifier();
						callToastify("success", msg, "right");
						$("body, html").animate({ scrollTop: 0 }, 1000);
						setTimeout(function () {
							window.location.reload();
						}, RELOAD_TIME);
					} else {
						response_error(flag, msg);
					}
				} else {
					session_expired();
				}
			},
			(errmsg) => {}
		);
	}
};
