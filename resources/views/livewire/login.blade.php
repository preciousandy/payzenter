<div class=" min-h-screen lg:h-screen ">
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="  p-4 mt-12 lg:p-8 rounded-xl w-full max-w-xl mx-auto lg:hidden ">
        <x-login-form />
    </div>

    <div class=" hidden lg:grid grid-cols-3  h-full">
        <div class=" p-6 mt-12 ">
            <div class=" max-w-xl mx-auto">
                <x-login-form />
            </div>
        </div>

       
    </div>


</div>