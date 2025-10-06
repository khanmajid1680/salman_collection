$(document).ready(function(){
	home();
    // setInterval(()=>{
    //     home()
    // }, 5000)
});
const home = () => {
    const path  = `home/get_data`
    ajax('GET',path,'','JSON',resp=>{
        const {status, flag, data, msg} = resp;
        if(status){
            if(flag == 1){
                if(data){
                    if(data.first_data){
                    	first(data.first_data)
                    }
                    if(data.second_data){
                    	second(data.second_data)
                    }
                    if(data.third_data){
                    	third(data.third_data)
                    }
                    if(data.fourth_data){
                    	fourth(data.fourth_data)
                    }
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
const getRandomColor = () => {
  var letters = '0123456789ABCDEF';
  var color = '#';
  for (var i = 0; i < 6; i++) {
    color += letters[i];
  }
  return color;
}