@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @if (isset($wallet))
                            <a
                                class="btn btn-lg btn-warning mb-5"
                                href="{{ previousUrlOr(route('wallet.view.details',['id'=>$wallet->id])) }}"
                            > < BACK</a>
                            <h3>Wallet debug data</h3>
                            <pre>
                                {{ print_r($wallet->attributesToArray(), true) ?? 'NO TRANSACTION PROVIDED' }}
                            </pre>
                            <hr/>
                            <h3>Transactions:</h3>
                            SQL:<br>
                            <textarea name="trans_sql" id="trans_sql" cols="100" rows="5">
                                {{$wallet->transactions()->toSql()}}
                            </textarea>
                            <pre>
                                {{ print_r($wallet->transactions->toArray(), true) }}
                            </pre>
                        @else
                            <h1>No wallet provided.</h1>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
