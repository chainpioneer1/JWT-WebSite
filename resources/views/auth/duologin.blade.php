@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="row">

            <div class="col-md-6 col-md-offset-3">

                <script src="{{asset('js/Duo-Web-v2.min.js')}}"></script>

                <script>
                    Duo.init({
                        'host':'{{$duoinfo['HOST']}}',
                        'post_action':'{{$duoinfo['POST']}}',
                        'sig_request':'{{$duoinfo['SIG']}}'
                    });
                </script>

                <iframe id="duo_iframe" width="590" height="500" frameborder="0" allowtransparency="true" style="background:transparent;"></iframe>

            </div>

        </div>

    </div>
@endsection
