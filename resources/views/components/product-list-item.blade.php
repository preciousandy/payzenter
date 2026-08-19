@props( ['product' => $product ])

<a href="{{route('products.show', $product->slug)}}" class="block ">
    <div class=" border border-gray-300 rounded-2xl p-4 bg-white">
        <div class=" mb-3">
            @if (
            str_starts_with($product->media[0]->type, 'video')
            )
            <video src="{{asset($product->media[0]->full_url)}}" loading="lazy" controls height="240" class=" rounded-lg object-cover"></video>
            @else
            <img src="{{asset( $product->media[0]->full_url)}}" alt="" class=" w-full h-60 rounded-lg object-cover">
            @endif
        </div>
        <h3 class=" font-semibold">{{$product->title}}</h3>

        <p class=" text-primary font-semibold text-xl"> N {{number_format($product->price)}}</p>

        @if ($product->mythicItems->count() > 0)
        <p class=" text-sm text-gray-600 mt-3">
            <strong>Contains:</strong>
            @foreach ($product->mythicItems as $item)
            <span>
                {{$item->name}},
            </span>

            @endforeach
        </p>
        @endif
    </div>
</a>