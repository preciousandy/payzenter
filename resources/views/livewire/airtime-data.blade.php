<div class=" bg-white">
    <div class=" px-4 py-6  max-w-4xl min-h-screen lg:p-8">

        <section x-data="{tab: 'data'}">
            <div class=" grid grid-cols-2 gap-x-2">
                <button @click="tab = 'airtime'" class=" font-medium px-4 text-center rounded-lg py-3 lg:py-4" :class="tab== 'airtime' && 'bg-primary text-white' ">
                    Airtime
                </button>
                <button @click="tab = 'data'" class=" font-medium px-4 text-center rounded-lg py-3" :class="tab== 'data' && 'bg-primary text-white' ">
                    Data
                </button>
            </div>


            <div x-show="tab== 'airtime'" x-cloak>
                <livewire:bills.airtime />
            </div>
            <div x-show="tab== 'data'" x-cloak>
                <livewire:bills.data />
            </div>
        </section>
    </div>
</div>

<x-slot:headerTitle>
    Buy Airtime & Data
</x-slot:headerTitle>