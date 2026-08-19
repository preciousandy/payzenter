<div x-data="{
            expanded: false,
            showButton: false,

            checkOverflow() {
                // Wait for DOM to render
                this.$nextTick(() => {
                    const el = this.$refs.content;
                    
                    // Check if content overflows 3 lines
                    const isOverflowing = el.scrollHeight > (el.clientHeight * 3);
                    
                    // Show button only if text overflows
                    this.showButton = isOverflowing;
                });
            },

            toggle() {
                this.expanded = !this.expanded;
            }
        }" x-init="checkOverflow()" class="relative">
    <p x-ref="content" x-bind:class="{
            'line-clamp-3': !expanded,
            'line-clamp-none': expanded
        }" class=" transition-all duration-300 ease-in-out overflow-hidden">
        {{$text}}
    </p>

    <button x-on:click="toggle()" class="mt-2  text-primary font-semibold">
        <span x-text="expanded ? 'See Less' : 'See More'"></span>
    </button>
</div>