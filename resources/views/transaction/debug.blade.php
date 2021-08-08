@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <a
                            href="{{ previousUrlOr(route('transaction.view.all')) }}"
                            class="btn btn-lg btn-warning mb-5"
                        >< BACK</a>
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

                            <h3>Wallet:</h3>
                            @if (isset($transaction->wallet))
                                <a
                                    href="{{ route('wallet.view.debug',['id'=>$transaction->wallet->id]) }}"
                                    class="btn btn-warning"
                                    target="_blank"
                                    referrerpolicy="no-referrer"
                                >DEBUG Wallet</a>
                                <pre>
                                    {{ print_r($transaction->wallet->attributesToArray(), true) }}
                                </pre>
                            @else
                                <h5>No wallet</h5>
                            @endif
                        @else
                            <h1>No transaction.</h1>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
