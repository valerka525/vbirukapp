@extends('shopify-app::layouts.default')

@section('content')
    <div class="flex-center position-ref full-height">
        <div class="content">
            <h1>{{ Auth::user()->name }}</h1>
            <h4>Products:</h4>
            <ul>
                @foreach($products as $product)
                    <li>{{ $product['title'] }}</li>
                @endforeach
            </ul>

            <h4>Plans:</h4>
            <ul>
                @foreach($plans as $plan)
                    <li>
                        <strong>${{ $plan->price }}</strong>
                        {{ $plan->name }}
                        @if($plan->id == $current_plan)
                            <span>(Current)</span>
                        @else
                            <a href="{{ route('billing', ['plan' => $plan->id]) }}">Apply</a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection
