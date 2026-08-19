<div class=" px-4 py-6 max-w-4xl">

  <div class=" flex justify-between items-center">
    <div class=" flex items-center gap-2">
      <img src="{{asset($crypto['icon'])}}" alt="" class=" h-14 w-14 lg:h-20 lg:w-20 object-cover">
      <div>
        <h2 class=" text-lg lg:text-xl font-semibold">
          {{$crypto['name']}}
        </h2>
        <p class=" text-gray-600 lg:text-lg lg:mt-1">
          {{$crypto['symbol']}}
        </p>
      </div>
    </div>

    <a href="{{route('investments.create', ['name' => $crypto['name'], 'symbol' => $crypto['symbol'], 'change_percentage' => $crypto['change_percentage'], 'type' => 'crypto', 'icon_url' => $crypto['icon']])}}">
      <x-primary-button class=" px-6 text-xs mt-4 rounded-xl lg:w-40 md:text-sm lg:text-base ">
        Invest
      </x-primary-button>
    </a>
  </div>

  <p class=" mt-6 lg:mt-8">
    {{$crypto['description']}}
  </p>

  <div class=" bg-white p-4 rounded-lg lg:shadow-sm lg:p-6">
    <h3 class=" text-sm text-gray-700 lg:text-base lg:font-medium">Current Price</h3>

    <p class=" text-xl lg:text-2xl font-semibold mt-2 lg:mt-3">
      $ {{formatNumber($crypto['price_in_usd'])}} ( <span class="  {{$crypto->change_percentage > 0 ? 'text-green-600' : 'text-red-600'}}">{{$crypto->change_percentage}} %</span> )
    </p>

    <p class=" text-gray-700 mt-1 lg:text-lg lg:mt-2">
      <x-naira-symbol /> {{formatNumber($crypto['price_in_usd'] * $naira_to_dollar) }}
    </p>

  </div>


  <div class=" bg-white p-2 rounded-xl mt-8 relative w-full h-[24rem] lg:mt-10 lg:shadow-sm lg:h-[28rem] lg:p-4">
    <canvas id="myChart"></canvas>
  </div>

</div>

@push('scripts')

<script type="module">
  const ctx = document.getElementById('myChart');
  ctx.style.height = '100%';
  ctx.style.width = '100%';



  let labels = @json($historicalDates);
  let prices = @json($crypto->changes);


  const myChart = new Chart(ctx, {
  type: "line",
  data: {
  labels: labels,
  datasets: [
  {
  label: "Price in the last 7 days",
  data: prices,
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
  
  // Function to generate a random price change
  function getRandomPrice(lastPrice) {
  const changePercent = (Math.random() - 0.5) * 2; // Random value between -1 and 1
  return lastPrice * (1 + changePercent / 100); // Add/subtract up to 1% of last price
  }
  
  // Function to update chart data
  function updateChart() {
  // Remove first data point and add a new one at the end
  const lastPrice = prices[prices.length - 1];
  prices = [...prices.slice(1), getRandomPrice(lastPrice)];
  
  // Update chart data
  myChart.data.datasets[0].data = prices;
  
  // Shift labels (optional - if you want moving time labels)
  // const now = new Date();
  // myChart.data.labels = labels.map((_, i) => {
  // const d = new Date(now);
  // d.setDate(d.getDate() - (6 - i));
  // return d.toLocaleDateString("en-US", { weekday: "short" });
  // });
  
  myChart.update("active"); // 'active' gives a smoother animation
  }
  
  // Update every 2 seconds
  setInterval(updateChart, 1000);
</script>

@endpush