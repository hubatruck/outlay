@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div id="accordion">
                    <div class="card">
                        <div class="card-header" id="wallets">
                            <h5 class="mb-0">
                                <button class="btn btn-link" data-toggle="collapse"
                                        data-target="#walletCollapse"
                                        aria-expanded="true" aria-controls="walletCollapse">
                                    <h3>Wallets</h3>
                                </button>
                            </h5>
                        </div>
                        <div id="walletCollapse" class="collapse show" aria-labelledby="wallets"
                             data-parent="#accordion">
                            <div class="card-body">
                                <pre>
                                    {{ print_r(Auth::user()->wallets->toArray(), true) ?? 'NO WALLETS' }}
                                </pre>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="transactions">
                            <h5 class="mb-0">
                                <button class="btn btn-link" data-toggle="collapse"
                                        data-target="#transactionCollapse"
                                        aria-expanded="false" aria-controls="transactionCollapse">
                                    <h3>Transactions</h3>
                                </button>
                            </h5>
                        </div>
                        <div id="transactionCollapse" class="collapse show" aria-labelledby="transactions"
                             data-parent="#accordion" aria-expanded="true">
                            <div class="card-body">
                                @foreach(Auth::user()->wallets as $wallet)
                                    <table class="border-5">
                                        <tr>
                                            <td class="border">name</td>
                                            <td class="border">{{ $wallet->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="border">ID</td>
                                            <td class="border">{{ $wallet->id }}</td>
                                        </tr>
                                        <tr>
                                            <td class="border">sql</td>
                                            <td class="border">
                                        <textarea name="trans_sql" id="trans_sql" cols="100" rows="5">
                                            {!! trim($wallet->transactions()->toSql()) !!}
                                        </textarea>
                                            </td>
                                        </tr>
                                    </table>
                                    <pre>
                                        {{ print_r($wallet->transactions->toArray(), true) }}
                                    </pre>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
