@extends('default.layouts.layout')

@php
    $statusCode = $exception->getStatusCode();
    $messageKey = match ($statusCode) {
        403 => 'forbidden',
        404 => 'not_found',
        419 => 'expired',
        429 => 'rate_limited',
        default => 'unavailable',
    };
    $errorTitle = __('theme::messages.errors.' . $messageKey);
@endphp

@section('title', $errorTitle)
@section('robots', 'noindex,nofollow')

@section('content')
    <section class="bastion-error" aria-labelledby="bastion-error-heading">
        <p class="bastion-error-code">{{ $statusCode }}</p>
        <h1 id="bastion-error-heading">{{ $errorTitle }}</h1>
        <a class="btn btn-primary" href="{{ url('/') }}"><i class="bi bi-arrow-left" aria-hidden="true"></i>
            {{ __('site.home.back_to_home') }}</a>
    </section>
@endsection
