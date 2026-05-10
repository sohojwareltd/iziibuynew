@php
    $products = Cart::session(request('user_name'))->getContent();

    $products = $products
        ->sortByDesc(function ($item) {
            $options = is_array($item->options ?? null) ? $item->options : [];
            $timestamp = $options['timestamp'] ?? null;
            if ($timestamp === null) {
                $timestamp = now()->subYears(100);
            }
            if ($timestamp instanceof \DateTimeInterface) {
                return $timestamp->getTimestamp();
            }

            try {
                return \Illuminate\Support\Carbon::parse($timestamp)->getTimestamp();
            } catch (\Throwable) {
                return 0;
            }
        })
        ->values();
@endphp

<div class="row mt-5 container">
    @foreach ($products as $product)
        @if ($product && $product->model)
            <div class="col-12">

                <div class="row ">
                    <div class="col-md-4 col-sm-12">
                        <img src="{{ Iziibuy::image($product->model->image) }}" class="img-fluid" alt="produkt">
                    </div>
                    <div class="col-md-8 col-sm-12">
                        <h4>{{ $product->model->name }} </h4>
                        <h6 class=" my-3">{{ Iziibuy::price($product->price) }} <a class="d-inline"
                                style="font-size:15px"
                                href="{{ route('cart.destroy', ['id' => $product->id, 'user_name' => request('user_name')]) }}"><i
                                    class="fas fa-trash-alt"></i></a> </h6>
                        <form action="{{ route('cart.update', request('user_name')) }}" method="post">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}" />
                            <div class="input-group ">
                                <input name="quantity" class="form-control" min="1" step="1" type="number"
                                    value="{{ $product->quantity }}" style="width:50px">
                                <div class="input-group-append">
                                    <input type="submit" class="btn btn-inline-iziibuy py-0 px-2"
                                        value="{!! strip_tags(__('words.cart_table_update_btn')) !!}">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <hr>
            </div>
        @endif
    @endforeach
</div>
