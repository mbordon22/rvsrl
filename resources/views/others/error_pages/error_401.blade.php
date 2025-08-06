@extends('layouts.errors.master')

@section('css')
@endsection

@section('main_content')
    <!-- error-401 start-->
    <div class="error-wrapper">
        <div class="container"><img class="img-100" src="{{ asset('assets/images/other-images/sad.png') }}" alt="">
            <div class="error-heading">
                <h2 class="headline font-warning">401</h2>
            </div>
            <div class="col-md-8 offset-md-2">
                <p class="sub-content">The page you are attempting to reach is currently not available. This may be because
                    the page does not exist or has been moved.</p>
            </div>
            <div><a class="btn btn-warning btn-lg" href="{{ route('admin.dashboard') }}">BACK TO HOME PAGE</a></div>
        </div>
    </div>
    <!-- error-401 end-->
@endsection

@section('scripts')
@endsection
