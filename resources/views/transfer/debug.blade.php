@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <a class="btn btn-lg btn-warning mb-5"
                           href="{{ previousUrlOr(route('transaction.view.all')) }}">< BACK</a>
                        @if(isset($transfer))
                            <h3>TRANSFER</h3>
                            <h5>ID: {{ $transfer->id }}</h5>
                            <pre>
                                {{ print_r($transfer->attributesToArray(), true) }}
                            </pre>

                            @if(isset($transfer->fromWallet))
                                <h4>FROM Wallet</h4>
                                <a
                                    href="{{ route('wallet.view.debug',['id'=>$transfer->fromWallet->id]) }}"
                                    class="btn btn-warning"
                                    target="_blank"
                                >DEBUG Wallet</a>
                                <pre>
                                    {{ print_r($transfer->fromWallet->toArray(), true) }}
                                </pre>
                            @else
                                <h4>No source wallet</h4>
                            @endif
                            @if(isset($transfer->toWallet))
                                <h4>TO Wallet</h4>
                                <a
                                    href="{{ route('wallet.view.debug',['id'=>$transfer->toWallet->id]) }}"
                                    class="btn btn-warning"
                                    target="_blank"
                                >DEBUG Wallet</a>
                                <pre>
                                    {{ print_r($transfer->toWallet->toArray(), true) }}
                                </pre>
                            @else
                                <h4>No destination wallet</h4>
                            @endif
                        @else
                            <h3>No transfer provided.</h3>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
