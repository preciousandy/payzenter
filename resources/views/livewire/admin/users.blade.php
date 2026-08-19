<div class=" ">
    <div class=" flex items-center justify-between mb-6">
        <h2 class=" text-2xl font-bold mb-6">
            All Users
        </h2>

        <div class=" flex items-center gap-x-4">

            {{-- leave it here for the bulk actions --}}
            <livewire:send-message-to-multiple-users>
                <livewire:admin.send-notifications-to-users />
                <livewire:add-user />
        </div>
    </div>


    <livewire:admin.user-table :rolesFilter=$rolesFilter />



</div>