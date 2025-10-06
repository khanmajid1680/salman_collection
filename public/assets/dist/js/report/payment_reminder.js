$(document).ready(function(){
	$("#_acc_id").select2(select2_default({
	    url:`report/payment_reminder/get_select2_supplier`,
	    placeholder:'SUPPLIER',
	})).on('change', () => trigger_search());
	$("#pm_bill_no").select2(select2_default({
	    url:`report/payment_reminder/get_select2_bill_no`,
	    placeholder:'BILL NO',
	})).on('change', () => trigger_search());
	$("#from_bill_date, #to_bill_date, #as_on_date").on('change', () => trigger_search());
});