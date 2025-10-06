$(document).ready(function(){
    /************** SALES RETURN SUMMARY ***********/
        $("#srm_entry_no").select2(select2_default({
            url:`sales_return/get_select2_entry_no`,
            placeholder:'ENTRY NO',
        })).on('change', () => trigger_search());
        $("#srm_acc_id").select2(select2_default({
            url:`sales_return/get_select2_acc_id`,
            placeholder:'CUSTOMER',
        })).on('change', () => trigger_search());    
        $("#srt_user_id").select2(select2_default({
            url:`sales_return/get_select2_user_id`,
            placeholder:'SALES PERSON',
        })).on('change', () => trigger_search());    
        $("#bm_id").select2(select2_default({
            url:`sales_return/get_select2_bm_id`,
            placeholder:'BARCODE',
        })).on('change', () => trigger_search());    
        $("#style_id").select2(select2_default({
            url:`sales_return/get_select2_style_id`,
            placeholder:'STYLE',
        })).on('change', () => trigger_search());
        $("#design_id").select2(select2_default({
            url:`sales_return/get_select2_design_id`,
            placeholder:'DESIGN',
        })).on('change', () => trigger_search());
        $("#brand_id").select2(select2_default({
            url:`sales_return/get_select2_brand_id`,
            placeholder:'BRAND',
        })).on('change', () => trigger_search());
        $("#age_id").select2(select2_default({
            url:`sales_return/get_select2_age_id`,
            placeholder:'AGE GROUP',
        })).on('change', () => trigger_search());
        $("#from_entry_date").on('change', () => trigger_search());
        $("#to_entry_date").on('change', () => trigger_search());
    /************** SALES RETURN SUMMARY ***********/


});