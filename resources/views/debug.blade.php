@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div id="accordion">
                    <div class="card">
                        <div class="card-header" id="wallets">
                            <h5 class="mb-0">
                                <button class="btn btn-link btn-lg" data-toggle="collapse"
                                        data-target="#walletCollapse"
                                        aria-expanded="true" aria-controls="walletCollapse">
                                    Wallets
                                </button>
                            </h5>
                        </div>
                        <div id="walletCollapse" class="collapse show" aria-labelledby="wallets"
                             data-parent="#accordion">
                            <div class="card-body">
                                @forelse(Auth::user()->wallets as $wallet)
                                    <a
                                        href="{{ route('wallet.view.debug', ['id' => $wallet->id]) }}"
                                        class="btn btn-warning"
                                        target="_blank"
                                        referrerpolicy="no-referrer"
                                    >DEBUG Wallet</a>
                                    <pre>
                                    {{ print_r($wallet->toArray(), true) }}
                                </pre>
                                @empty
                                    No wallets
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="transactions">
                            <h5 class="mb-0">
                                <button class="btn btn-link btn-lg" data-toggle="collapse"
                                        data-target="#transactionCollapse"
                                        aria-expanded="false" aria-controls="transactionCollapse">
                                    Transactions
                                </button>
                            </h5>
                        </div>
                        <div id="transactionCollapse" class="collapse show" aria-labelledby="transactions"
                             data-parent="#accordion" aria-expanded="true">
                            <div class="card-body">
                                @forelse(Auth::user()->wallets as $wallet)
                                    <table class="border-5">
                                        <caption>User's wallets</caption>
                                        <tr>
                                            <th id="name">Name</th>
                                            <th id="value">Value</th>
                                        </tr>
                                        <tr>
                                            <td class="border">Wallet Name</td>
                                            <td class="border">{{ $wallet->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="border">ID</td>
                                            <td class="border">{{ $wallet->id }}</td>
                                        </tr>
                                        <tr>
                                            <td class="border">SQL</td>
                                            <td class="border">
                                                <textarea name="trans_sql" id="trans_sql" cols="100" rows="5">
                                                    {{ $wallet->transactions()->toSql() }}
                                                </textarea>
                                            </td>
                                        </tr>
                                    </table>
                                    @forelse ($wallet->transactions as $transaction)
                                        <a
                                            href="{{ route('transaction.view.debug', ['id' => $transaction->id]) }}"
                                            class="btn btn-warning"
                                            target="_blank"
                                            referrerpolicy="no-referrer"
                                        >DEBUG Transaction</a>
                                        <pre>
                                            {{ print_r($transaction->toArray(), true) }}
                                        </pre>
                                    @empty
                                        No transactions for this wallet
                                    @endforelse
                                    <hr>
                                @empty
                                    No wallets -> no transactions
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="transfers">
                            <h5 class="mb-0">
                                <button class="btn btn-link btn-lg" data-toggle="collapse"
                                        data-target="#transferCollapse"
                                        aria-expanded="false" aria-controls="transferCollapse">
                                    Transfers
                                </button>
                            </h5>
                        </div>
                        <div id="transferCollapse" class="collapse show" aria-labelledby="transfers"
                             data-parent="#accordion" aria-expanded="true">
                            <div class="card-body">
                                {{ Auth::user()->transfers()->toSql() }}
                                @forelse(Auth::user()->transfers()->get() as $transfer)
                                    <table class="border-5">
                                        <caption>User's transfers</caption>
                                        <tr>
                                            <th id="key"> Key</th>
                                            <th id="value">Value</th>
                                        </tr>
                                        <tr>
                                            <td class="border">ID</td>
                                            <td class="border">{{ $transfer->id }}</td>
                                        </tr>
                                        <tr>
                                            <td class="border">SQL</td>
                                            <td class="border">
                                            <textarea name="trans_sql" id="trans_sql" cols="100" rows="2">
                                                {{ $transfer->toSql() }}
                                            </textarea>
                                            </td>
                                        </tr>
                                    </table>
                                    <a
                                        href="{{ route('transfer.view.debug', ['id' => $transfer->id]) }}"
                                        class="btn btn-warning"
                                        target="_blank"
                                        referrerpolicy="no-referrer"
                                    >DEBUG Transfer</a>
                                    <pre>
                                        {{ print_r($transfer->toArray(), true) }}
                                    </pre>
                                @empty
                                    No wallets -> no transfers
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
