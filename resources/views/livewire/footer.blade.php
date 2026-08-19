<footer class=" bg-[#161C2D] px-4 py-6 lg:py-20">
    <div class="  px-3 py-4 lg:px-6 lg:py-14 rounded-3xl max-w-7xl mx-auto text-white ">
        <div class=" grid gap-12 lg:grid-cols-2 lg:gap-20">
            <div>
                <img src="{{asset('storage/'  . get_website_content('logo'))}}" alt="" class=" h-14 lg:h-16">

                <p class=" mt-2 text-gray-200">
                    {{get_website_content('description')}}
                </p>

                <ul class="grid grid-cols-1 mt-4 lg:mt-8 text-sm md:text-base">
                    <li class=" underline p-2"><a href=""><span class="fa fa-caret-right "></span> About Us</a></li>
                    <li class=" underline p-2"><a href="{{route('contact-us')}}"><span class="fa fa-caret-right "></span> Contact Us</a></li>
                    <li class=" underline p-2"><a href="{{route('terms-and-conditions')}}"><span class="fa fa-caret-right "></span> Terms & Conditions</a></li>
                    <li class=" underline p-2"><a href="{{route('privacy-policy')}}"><span class="fa fa-caret-right "></span> Privacy Policy</a></li>
                </ul>
            </div>

            <div>
                <h3 class=" text-lg lg:text-xl font-semibold">Contact Us</h3>

                <div class=" mt-4">
                    <h5 class=" font-medium ">
                        For Support Contact:
                    </h5>
                    <a href="mailto:support@payzenter.com">
                        support@payzenter.com
                    </a>
                </div>
                <div class=" mt-2">
                    <h5 class=" font-medium ">
                        For Partnership Contact:
                    </h5>
                    <a href="mailto:partner@payzenter.com">
                        partner@payzenter.com
                    </a>
                </div>
                <div class=" mt-2">
                    <h5 class=" font-medium ">
                        Location:
                    </h5>
                    <a href="mailto:partner@payzenter.com">
                        Garki,
                        Abuja, Federal Capital Territory
                    </a>
                </div>
            </div>
        </div>

        <hr class=" my-6 border lg:my-12">

        <div class=" flex flex-col gap-6 lg:flex-row lg:justify-between">
            <p class=" text-gray-200">
                CopyRight &copy; {{now()->year}} <strong>{{config('app.name')}}</strong> All Rights Reserved.
            </p>

            {{-- social media links --}}
            <ul class=" flex gap-4  items-center justify-evenly text-3xl">

                @foreach ($socialLinks as $socialLink)
                <li><a href="{{$socialLink->link}}" target="_blank"><span class=" fab fa-{{$socialLink->icon}}"></span></a></li>

                @endforeach
            </ul>
        </div>

    </div>
</footer>
