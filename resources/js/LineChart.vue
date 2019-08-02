<script>
  import { Line, mixins } from 'vue-chartjs'
  import moment from 'moment';

  export default {
    data() {
      return {
        chartData: {
          labels: [
            moment().subtract(6, 'days').format('DD/MM'),
            moment().subtract(5, 'days').format('DD/MM'),
            moment().subtract(4, 'days').format('DD/MM'),
            moment().subtract(3, 'days').format('DD/MM'),
            moment().subtract(2, 'days').format('DD/MM'),
            'Ayer',
            'Hoy'
          ],
          datasets: [{
              label: 'Artículos creados',
              data: [],
              backgroundColor: 'rgba(0,123,255,0.6)',
              borderWidth: 4,
              color: 'rgba(0,123,255)',
              pointColor: 'rgba(0,123,255)'
          }]
        },
        chartOptions: {
          maintainAspectRatio: false,
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true,
                fontFamily: "Segoe UI"
              }
            }],
            xAxes: [{
              ticks: {
                fontFamily: "Segoe UI",
              }
            }]
          },
          legend: {
            labels: {
              fontFamily: 'Segoe UI'
            }
          }
        }
      }
    },
    extends: Line,
    methods: {
      loadApiData() {
        let day = 0;
        let dataChart = [];
        axios.get("api/stats/4").then(({ data }) => {
          let daysArray = data.map(a => a.day);
          for (let i = 6; i >= 0; i--) {
            if(daysArray[day] === moment().subtract(i, 'days').format('YYYY-MM-DD')){
              dataChart.push(data[day].total);
              day = day + 1;
            } else {
              dataChart.push(0);
            }
          }
          this.chartData.datasets[0].data = dataChart;
          this.renderChart(this.chartData, this.chartOptions);
        });
      }
    },
    created() {
      this.loadApiData();
    }
  }
</script>
