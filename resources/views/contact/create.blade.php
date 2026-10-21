@extends('layouts.app')
@section('title', ($locale === 'en' ? 'Contact' : 'Liên hệ').' | '.($site['site_title'] ?? 'Hoang Thuoc Medical'))
@section('content')
@include('partials.contact-layout')
@endsection
