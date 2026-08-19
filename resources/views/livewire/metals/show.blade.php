<div class=" px-4 py-6 max-w-5xl">

    <div class=" flex justify-between items-center">
        <div class=" flex items-center gap-2 mdgap4
        ">
            <img src="{{asset('metal.png')}}" alt="" class=" h-14 lg:h-20">

            <div>
                <h2 class="text-lg md:text-xl lg:text-2xl font-semibold">
                    {{ucfirst(str_replace('_', ' ', $metal->metal))}}
                </h2>
                {{-- <p class=" text-gray-600">
                    {{$metal->code}}
                </p> --}}
            </div>
        </div>

        @if (!$holding)
        <a href="{{route('holdings.create', ['name' => ucfirst(str_replace('_', ' ', $metal->metal)) , 'symbol' => $metal->metal, 'price' => $metal->price, 'type' => 'metal', 'icon_url' => asset('metal.png')])}}">
            <x-primary-button class=" px-6 md:px-10 lg:px-14 md:text-sm lg:text-base text-xs mt-4 rounded-xl ">
                Invest
            </x-primary-button>
        </a>
        @endif
    </div>


    <div class=" bg-white p-4 rounded-lg mt-6 lg:shadow-sm lg:p-6">
        <h3 class="text-sm md:text-base lg:text-lg text-gray-600">Current price</h3>

        <p class=" text-xl md:text-2xl lg:text-3xl font-semibold mt-1 md:mt-2">
            <x-naira-symbol /> {{formatNumber($metal->price) }}
        </p>

    </div>

    <div class=" bg-white p-3 rounded-md mt-8 relative w-full h-[22rem] lg:h-[28rem] lg:mt-12 lg:shadow-sm lg:p-6">
        <canvas id="myChart"></canvas>

    </div>


    @if ($holding)
    <div class=" grid grid-cols-2 gap-4  bg-white p-4 rounded-xl mt-6 lg:p-6 lg:mt-8 lg:shadow-sm lg:gap-6">
        <div class=" ">
            <p class=" text-sm text-gray-500 lg:text-base lg:text-gray-700">
                Amount Invested
            </p>
            <p class=" font-semibold  lg:text-xl md:text-lg lg:mt-2">
                <x-naira-symbol /> {{formatNumber($holding->amount)}}
            </p>
        </div>

        <div class=" ">
            <p class=" text-sm text-gray-500 lg:text-base lg:text-gray-700">
                {{$holding->status == 'completed' ? 'Amount Earned' : 'Possible Earnings'}}
            </p>
            <p class=" font-semibold lg:text-xl md:text-lg lg:mt-2 {{$earnings > $holding->amount ? 'text-green-700' : 'text-red-700' }} ">
                <x-naira-symbol /> {{formatNumber($earnings) }}
            </p>
        </div>

        <div class=" ">
            <p class=" text-sm text-gray-500 lg:text-base lg:text-gray-700">
                Initial Rate
            </p>
            <p class=" font-semibold  lg:text-xl md:text-lg lg:mt-2">
                <x-naira-symbol /> {{formatNumber($holding->naira_start_rate)}}
            </p>
        </div>

        <div class=" ">
            <p class=" text-sm text-gray-500 lg:text-base lg:text-gray-700">
                Current Rate
            </p>
            <p class=" font-semibold lg:text-xl md:text-lg lg:mt-2 {{$current_rate > $holding->naira_start_rate ? 'text-green-700' : 'text-red-700' }} ">

                <x-naira-symbol /> {{formatNumber($current_rate)}}
            </p>
        </div>

        <div class=" ">
            <p class=" text-sm text-gray-500 lg:text-base lg:text-gray-700">
                Holding Status
            </p>
            <span class=" {{$holding->status}} inline-block mt-1">
                {{$holding->status}}
            </span>
        </div>
        <div class=" ">
            <p class=" text-sm text-gray-500 lg:text-base lg:text-gray-700">
                Asset Invested
            </p>
            <p class=" font-semibold  lg:text-xl md:text-lg lg:mt-2">
                {{$holding->asset_amount}} ({{$holding->asset_symbol}})
            </p>
        </div>


        <div class=" ">
            <p class=" text-sm text-gray-500 lg:text-base lg:text-gray-700">
                End Date
            </p>
            <p class=" font-semibold  lg:text-xl md:text-lg lg:mt-2">
                {{$holding->end_date->format('d M Y')}}
            </p>
        </div>


    </div>
    @endif

</div>

@push('scripts')

<script type="module">
    const ctx = document.getElementById('myChart');
  ctx.style.height = '100%';
  ctx.style.width = '100%';

  let labels = @json($dates);

  let prices = @json($prices);

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
      display: false // This hides just the day labels
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
  setInterval(updateChart, 2000);
</script>

@endpush