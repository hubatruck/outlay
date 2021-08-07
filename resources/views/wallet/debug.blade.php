@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <a class="btn btn-lg btn-warning mb-5" href="{{ url()->previous() }}">< BACK</a>
                        @if (isset($wallet))
                            <h3>Wallet</h3>
                            <pre>
                                {{ print_r($wallet->attributesToArray(), true) ?? 'NO TRANSACTION PROVIDED' }}
                            </pre>
                            <hr/>
                            <h3>Transactions:</h3>
                            sql:<br>
                            <textarea name="trans_sql" id="trans_sql" cols="100" rows="5">
                                {{$wallet->transactions()->toSql()}}
                            </textarea>
                            <pre>
                                {{ print_r($wallet->transactions->toArray(), true) }}
                            </pre>
                        @else
                            <h1>NO WALLET!</h1>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
