<div class=" px-4 bg-white min-h-screen py-6 lg:py-12">
    <h2 class=" text-2xl lg:text-3xl  font-bold text-center">Bills</h2>

    <div class=" grid grid-cols-2 gap-4 lg:gap-6 w-4/5 mx-auto mt-12 lg:mt-16 max-w-xl lg:w-full">
        <a href="{{route('bills.electricity')}}" class=" block p-4 md:p-6 lg:p-8 lg:py-12 rounded-lg shadow-md hover:bg-blue-50">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class=" h-12 md:h-16  text-primary bg-indigo-100 p-2 rounded-md block mx-auto">
                <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
            </svg>

            <h3 class=" text-primary text-sm md:text-base lg:text-lg last:mt-4 text-center mt-2 font-medium">Electricity</h3>
        </a>
        <a href="{{route('bills.cable')}}" class=" block p-4 md:p-6 lg:p-8 lg:py-12 rounded-lg shadow-md hover:bg-blue-50">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class=" h-12 md:h-16  text-primary bg-indigo-100 p-2 rounded-md block mx-auto">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 0 1 7.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 0 1 1.06 0Z" />
            </svg>



            <h3 class=" text-primary text-sm md:text-base lg:text-lg last:mt-4 text-center mt-2 font-medium">TV & Internet</h3>
        </a>
        <a href="{{route('bills.airtime-and-data')}}" class=" block p-4 md:p-6 lg:p-8 lg:py-12 rounded-lg shadow-md hover:bg-blue-50">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class=" h-12 md:h-16  text-primary bg-indigo-100 p-2 rounded-md block mx-auto">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
            </svg>

            <h3 class=" text-primary text-sm md:text-base lg:text-lg last:mt-4 text-center mt-2 font-medium">Airtime & Data</h3>
        </a>


        <a href="{{route('bills.betting')}}" class=" block p-4 md:p-6 lg:p-8 lg:py-12 rounded-lg shadow-md hover:bg-blue-50">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class=" h-12 md:h-16  text-primary bg-indigo-100 p-2 rounded-md block mx-auto">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
            </svg>

            <h3 class=" text-primary text-sm md:text-base lg:text-lg last:mt-4 text-center mt-2 font-medium">Betting</h3>
        </a>
    </div>

</div>