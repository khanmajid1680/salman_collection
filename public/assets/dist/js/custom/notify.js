const notifier = (id, msg = false) =>{
    if(msg === false){
        $('#'+id+'_msg').html('')
        return
    }
    $("#"+id).addClass('shake');
    $('#'+id).on('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(e)
    {
      $('#'+id).delay(200).removeClass('shake');
    });
    $('#'+id+'_msg').html(msg)
}
const callToastify = (alertType, text, position, gravity = 'top') => {
    let backgroundColor = ''
    if(alertType === 'success'){
        backgroundColor = 'linear-gradient(to right, #20F507, #0E6404)'
    }else if(alertType === 'error'){
        backgroundColor = 'linear-gradient(to right, #FB0909, #78040E)'   
    }else if(alertType === 'warn'){
        backgroundColor = 'linear-gradient(to right, #F1C40F, #B7950B)'   
    }

    Toastify({
        text,
        duration: 3000,
        newWindow: true,
        close: false,
        gravity, // `top` or `bottom`
        position, // `left`, `center` or `right`
        backgroundColor,
        stopOnFocus: true // Prevents dismissing of toast on hover     
    }).showToast();
}