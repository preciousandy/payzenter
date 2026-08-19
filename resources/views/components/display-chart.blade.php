<div class=" bg-white p-4 rounded-xl mt-6 lg:mt-8 lg:p-6 lg:shadow-sm ">

  <canvas id="{{$id}}"></canvas>
</div>

@push('scripts')

<script type="module">
  const chartElement = document.getElementById('{{$id}}');

  const labels = @json($chartLabels);
  const data = @json($chartData);


  //  new Chart(chartElement, {
  //   type: 'line',
  //   data: {
  //     labels: labels,
  //     datasets: [{
  //       label: '{{$title}}',
  //       data: data,
  //       borderWidth: 2,
  //       borderColor: '#33adff',
  //       backgroundColor: 'pink'
  //     }]
  //   },
  //   options: {
  //     scales: {
  //       y: {
  //         beginAtZero: true
  //       }
  //     },
  //     responsive: true,
  //   }
  // });

  const myChart = new Chart(chartElement, {
  type: "line",
  data: {
  labels: labels,
  datasets: [
  {
  label: '{{$title}}',
  data: data,
  fill: true,
  tension: 0.4,
  backgroundColor: "rgba(54, 162, 235, 0.2)",
  borderColor: "rgba(54, 162, 235, 1)",
  borderWidth: 2,
  pointRadius: 1,
  pointHoverRadius: 1,
  // pointBackgroundColor: "rgba(54, 162, 235, 1)",
  // pointBorderColor: "#fff",
  // pointBorderWidth: 2,
  },
  ],
  },
  options: {
  animation: {
  duration: 750, // Animation duration in milliseconds
  easing: "easeInOutQuart", // Smooth easing function
  },
  scales: {
  x: {
  ticks: {
  display: false, // This hides just the day labels
  },
  grid: {
  display: false,
  }
  },
  y: {
  beginAtZero: true,
  },
  },
  plugins: {
  legend: {
  display: false,
  },
  },
  elements: {
  line: {
  tension: 0.4,
  },
  },
  },
  });

  
</script>

@endpush