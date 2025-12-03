@extends('errors.error')
@section('content')
    <div class="mb-3">
        <!-- img -->
        <img src="{{ asset('assets/images/errors/404.png') }}" alt="Image" class="img-fluid" />
    </div>
    <!-- text -->
    <h1 class="display-4">¡Ups! La página no se encuentra.</h1>

@endsection