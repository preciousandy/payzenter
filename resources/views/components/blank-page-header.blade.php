<div class=" flex items-center justify-between">
    <button @click="history.back()" class=" md:text-xl lg:text-2xl">
        <span class="fa fa-arrow-left"></span>
    </button>

    <h3 class=" text-lg font-semibold md:text-xl lg:text-2xl">
        {{$title ?? ''}}
    </h3>
    <div>
        {{$slot}}
    </div>
</div>