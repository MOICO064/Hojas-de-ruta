@extends('errors.error')
@section('content')
    <div class="mb-3">
        <!-- img -->
        <img src="{{ asset('assets/images/errors/403.png') }}" alt="Image" class="img-fluid" />
    </div>
    <!-- text -->
    <h1 class="display-5">¡Ups! No estas autorizado para ingresar a este modulo.</h1>

@endsection
