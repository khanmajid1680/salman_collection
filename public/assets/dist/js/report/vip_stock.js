$(document).ready(function(){
    $("#bm_id").select2(select2_default({
        url:`master/barcode/get_select2`,
        placeholder:'BARCODE',
        barcode:'bm_id',
        param:1,
    })).on('change', () => trigger_search());
    $("#bm_acc_id").select2(select2_default({
        url:`master/barcode/get_select2_acc_id`,
        placeholder:'SUPPLIER',
    })).on('change', () => trigger_search());
    $("#bm_style_id").select2(select2_default({
        url:`master/barcode/get_select2_style_id`,
        placeholder:'STYLE',
    })).on('change', () => trigger_search());
    $("#bm_design_id").select2(select2_default({
        url:`master/barcode/get_select2_design_id`,
        placeholder:'DESIGN',
    })).on('change', () => trigger_search());
    $("#bm_brand_id").select2(select2_default({
        url:`master/barcode/get_select2_brand_id`,
        placeholder:'BRAND',
    })).on('change', () => trigger_search());
    $("#bm_age_id").select2(select2_default({
        url:`master/barcode/get_select2_age_id`,
        placeholder:'AGE GROUP',
    })).on('change', () => trigger_search());
});