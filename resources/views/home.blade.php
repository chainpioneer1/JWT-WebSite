@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Home Page</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <iframe src="{{$embedded_url}}" id="embedded_url" style="width: 100%; height: 800px" frameborder="0" allowtransparency="true" style="background:transparent;"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')

@endsection
