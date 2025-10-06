const validate_mobile_no = (element, flag = false) =>{
    const {value, id} = element
    if(value.length > 0){
        if(value.length !== 10){
            flag && $('#sbt_btn').prop('disabled', true);
            notifier(id, 'Invalid Mobile No')
        }else{
            flag && $('#sbt_btn').prop('disabled', false);
            notifier(id)
        }
    }
}
const validate_length = (element, msg, check, len = 6) =>{
    if(check == 0){
        const {value, id} = element
        if(value.length != 0){
            if(value.length <= len){
                notifier(id, msg)
            }else{
                notifier(id)
            }
        }else{
            notifier(id, 'Required')
        }    
    }
}
const validate_password = (element, temp, check) =>{
    if(check == 0){
        const {value, id}  = element
        const password = $(`#${temp}`).val()
        if(value.length != 0){
            if(value != password){
                notifier(id, 'Password mismatch.')
            }else{
                notifier(id)
            }
        }else{
            notifier(id, 'Required')
        }
    }
}
const validate_email = mail =>{
    if(mail.length > 0){
        if (/^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(mail)){
            notifier('account_email')
            $('#isMailValid').val(1)
            return true
        }
        notifier('account_email', 'Invalid Email')
        $('#isMailValid').val(0)
    }else{
        notifier('account_email')
        $('#isMailValid').val(1)
    }
}
const validate_dropdown = (element, flag = true) => {
    const {id, value} = element
    if(value == 0){
        flag && $('#sbt_btn').prop('disabled', true);
        notifier(id, 'Required')
    }else{
        flag && $('#sbt_btn').prop('disabled', false);
        notifier(id)
    }
}
const validate_textfield = (element, flag = true) => {
    const {id} = element;
    let name = $(`#${id}`).val();
    if(name.length > 0){
        flag && $('#sbt_btn').prop('disabled', false);
        notifier(id)
    }else{
        if(flag){
            $('#sbt_btn').prop('disabled', true);
            notifier(id, 'Required')
        }
    }
}
const validate_number = (element, flag = false) => {
    const {value}  = element;
    if(isNaN(value) || value == '') value = 0;
    if(value > 0){
        flag && $('#sbt_btn').prop('disabled', false);
        notifier(id)
    }else{
        flag && $('#sbt_btn').prop('disabled', true);
        notifier(id, 'Required')
    }
}