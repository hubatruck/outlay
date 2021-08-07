@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <a class="btn btn-lg btn-warning mb-5" href="{{ url()->previous() }}">< BACK</a>
                        {{--                        <h3>User's all transactions</h3>--}}
                        {{--                        <pre>--}}
                        {{--                        {{ print_r(Auth::user()->transactions()->get()) }}--}}
                        {{--                            </pre>--}}
                        @if (isset($transaction))
                            <h3>Transaction</h3>
                            <pre>
                                {{ print_r($transaction->attributesToArray(), true) ?? 'NO TRANSACTION PROVIDED' }}
                            </pre>
                            <hr/>

                            <h3>Source wallet:</h3>
                            @if (isset($transaction->wallet))
                                <pre>
                                    {{ print_r($transaction->wallet->attributesToArray(), true) }}
                                </pre>
                            @else
                                <h5>No source wallet</h5>
                            @endif
                            <hr/>

                            <h3>Destination wallet:</h3>
                            @if (isset($transaction->destinationWallet))
                                <pre>
                                    {{ print_r($transaction->destinationWallet->attributesToArray(), true) }}
                                </pre>
                            @else
                                <h5>No destination wallet</h5>
                            @endif
                        @else
                            <h1>NO TRANSACTION!</h1>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
