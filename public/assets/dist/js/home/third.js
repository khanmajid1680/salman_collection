$(document).ready(function(){
});
let third_ctx = document.getElementById('third-chart').getContext('2d');
let third_chart = new Chart(third_ctx, {
    type: 'bar',
    data: {
        labels:[],
        datasets:[
            {
                label:'CATEGORY WISE SALE',
                data:[],
            }
        ],
    },
    options: {
        legend:{
            display:false
        }
    },
});
const third = data => {
    let backgroundColor = ['#3e95cd', '#8e5ea2', '#3cba9f', '#e8c3b9'];
    let labels = [];
    let values = [];
    if(data['age']){
        labels.push(`AGE - ${data['age'].name}`)
        values.push(data['age'].cnt)
    }
    if(data['brand']){
        labels.push(`BRAND - ${data['brand'].name}`)
        values.push(data['brand'].cnt)
    }
    if(data['design']){
        labels.push(`DESIGN - ${data['design'].name}`)
        values.push(data['design'].cnt)
    }
    if(data['style']){
        labels.push(`STYLE - ${data['style'].name}`)
        values.push(data['style'].cnt)
    }
    third_chart.data.labels = labels;
    third_chart.data.datasets[0].data = values;
    third_chart.data.datasets[0].backgroundColor = backgroundColor;
    third_chart.update();
}