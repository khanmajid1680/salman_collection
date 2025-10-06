$(document).ready(function(){
	setInterval(()=>{
        balance()
    }, 10000)
});
const balance = () =>{
	const path  = `home/sales`
    ajax('GET',path,'','JSON',resp=>{
        const {status, flag, data, msg} = resp;
        if(status){
            if(flag == 1){
                if(data){
                    let {time, cash, bank} = data;
                    $('.cash_time').html(time)
                    
                    $('#cash_open_bal').html(cash.open_bal)
                    $('#cash_sales_amt').html(cash.sales_amt)
                    $('#cash_return_amt').html(cash.return_amt)
                    $('#cash_receipt_amt').html(cash.receipt_amt)
                    $('#cash_payment_amt').html(cash.payment_amt)
                    $('#cash_close_bal').html(cash.close_bal)

                    $('#bank_open_bal').html(bank.open_bal)
                    $('#bank_sales_amt').html(bank.sales_amt)
                    $('#bank_return_amt').html(bank.return_amt)
                    $('#bank_receipt_amt').html(bank.receipt_amt)
                    $('#bank_payment_amt').html(bank.payment_amt)
                    $('#bank_close_bal').html(bank.close_bal)
                }else{
                 response_error(0, msg)
                }   
            }else{
                response_error(flag, msg)
            }
        }else{
            session_expired()
        }
    },errmsg =>{});
}

