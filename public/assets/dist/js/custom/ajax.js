const disable_enable_background = flag =>{
    if(flag) //disable background
    {
        $('#ftco-loader').addClass('show');
        $('.wrapper').removeClass('unblur').addClass('blur');
        $('.master_block_btn, #sbt_btn').prop('disabled', true)
    }
    else //enable background
    {
        $('#ftco-loader').removeClass('show');
        $('.wrapper').removeClass('blur').addClass('unblur');
        $('.master_block_btn, #sbt_btn').prop('disabled', false)
    }    
}
const window_reload = () =>{
    $("#table_reload").load(window.location + " #table_tbody");
    $("#table_reload1").load(window.location + " #table_tbody1");
    $("#count_reload").load(window.location + " #total_rows");
}
const ajaxCall = (callType,path,form_data,datatype,res_callback,err_callback, async=true) =>{
    disable_enable_background(true)
    $.ajax({
        type: ''+callType+'',
        url:`${base_url}/${path}`,
        data:form_data,
        dataType:''+datatype+'',
        async:async,
        success: response =>{
            console.log(response)
            res_callback(response);
            disable_enable_background(false)
            window_reload()
        },
        error: error =>{
            callToastify('error', 'Something went wrong', 'right')
            console.log(error)
            err_callback(error);
            disable_enable_background(false)
        }   
    });
}
const ajax = (callType,path,form_data,datatype,res_callback,err_callback, async = true) =>{
    $.ajax({
        type: ''+callType+'',
        url:`${base_url}/${path}`,
        data:form_data,
        dataType:''+datatype+'',
        async:async,
        success: response =>{
            console.log(response)
            res_callback(response);
            disable_enable_background(false)
            window_reload()
        },
        error: error =>{
            callToastify('error', 'Something went wrong', 'right')
            console.log(error)
            err_callback(error);
            disable_enable_background(false)
        }   
    });
}
const fileUpAjaxCall = (callType,path,form_data,datatype,res_callback,err_callback, async=true) =>{
    disable_enable_background(true)
    $.ajax({
        type: ''+callType+'',
        url:`${base_url}/${path}`,
        data:form_data,
        dataType:''+datatype+'',
        contentType:false,
        processData:false,
        async:async,
        success:response=>{
            console.log(response)
            res_callback(response);
            disable_enable_background(false)
            window_reload()
        },
        error: error =>{
            callToastify('error', 'Something went wrong', 'right', gravity = 'bottom')
            console.log(error)
            err_callback(error);
            disable_enable_background(false)
        }   
    });
}
const fetch = options =>{
    console.log({options})
    let type            = options.type ? `'${options.type}'` : 'GET';
    let url             = options.url ? options.url : '';
    let data            = options.data ? options.data : '';
    let dataType        = options.dataType ? `'${options.dataType}'` : 'JSON';
    let cache           = options.cache ? options.cache : true;
    let async           = options.async ? options.async : true;
    let res_callback    = options.res_callback ? options.res_callback : resp => {};
    let err_callback    = options.err_callback ? options.err_callback : err => {};

    $.ajax({
        type,
        url,
        data,
        dataType,
        cache,
        async,
        success: response =>{
            console.log(response)
            res_callback(response);
            disable_enable_background(false)
            window_reload()
        },
        error: error =>{
            callToastify('error', 'Something went wrong', 'right')
            console.log(error)
            err_callback(error);
            disable_enable_background(false)
        }   
    });
}