<div class=" px-4 py-6">
    <div>
        <h2 class=" text-2xl font-bold">
            Manage Website Content
        </h2>
    </div>

    <div x-data="menu" class=" mt-8">
        <div class=" mb-6">
            <template x-for="(tab, tabIndex) in tabMenu" :key="tabIndex">
                <button class=" py-3 px-5 bg-indigo-100 text-primary rounded-t-xl whitespace-nowrap" :class="currentTab == tab && '  font-semibold  bg-primary text-white text-lg'" @click="currentTab = tab">
                    <span x-text="tab"></span>
                </button>

            </template>
        </div>
        <div x-show="currentTab == 'Website Content'" x-cloak>
            @livewire('admin.edit-website-content')
        </div>
        <div x-show="currentTab == 'Welcome Page'" x-cloak>
            @livewire('edit-welcome-page-content')
        </div>
        <div x-show="currentTab == 'Testimonials'" x-cloak>
            @livewire('manage-user-testimonials')
        </div>
        <div x-show="currentTab == 'Social Links'" x-cloak>
            @livewire('manage-social-links')
        </div>
        <div x-show="currentTab == 'FAQs'" x-cloak>
            @livewire('manage-faq')
        </div>
        <div x-show="currentTab == 'T and C'" x-cloak>
            @livewire('admin.edit-tand-c')
        </div>
        <div x-show="currentTab == 'Privacy Policy'" x-cloak>
            @livewire('admin.edit-privacy-policy')
        </div>
        <div x-show="currentTab == 'About Us'" x-cloak>
            @livewire('admin.edit-about-us-page')
        </div>
    </div>



</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('menu', () => ({
            currentTab: 'Website Content',
            tabMenu: [
                'Website Content',
                'Welcome Page',
                'Testimonials',
                'Social Links',
                "FAQs",
                "T and C",
                "Privacy Policy",
                "About Us",
            ],
            changeTab(tab) {
                this.currentTab = tab
            }
        }))
    })
</script>
@endpush