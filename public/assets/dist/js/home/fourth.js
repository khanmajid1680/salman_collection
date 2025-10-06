$(document).ready(function(){
});
let fourth_ctx = document.getElementById('fourth-chart').getContext('2d');
let fourth_chart = new Chart(fourth_ctx, {
    type: 'line',
    data: {
        labels:[],
        datasets: [],
    },
    options:{
        legend:{
            display:true
        },
        elements:{
            line:{
                tension : 0,
            }
        },
        animation: false,
    }
});
const fourth = data => {
    let borderColors = ['#3e95cd', '#8e5ea2', '#3cba9f', '#e8c3b9'];
    let labels = [];
    let sr_no  = 0;
    for (const [mode, temp] of Object.entries(data)) {
        let amts = [];
        for (const [month_year, amt] of Object.entries(temp)) {
            amts.push(amt);
            if(labels.indexOf(month_year) === -1){
                labels.push(month_year)
            }
        }
        fourth_chart.data.datasets[sr_no] = {
            label : mode,
            data : amts,
            fill: false,
            borderColor: borderColors[sr_no],
        };
        sr_no++;
    }
    fourth_chart.data.labels = labels;
    fourth_chart.update();
}