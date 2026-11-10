@extends('layouts.app')

@section('content')
    @include('partials.dashboard-analytics', ['judul' => 'Dashboard Kepala Laboratorium', 'hasilRoute' => 'kalab.penilaian.hasil'])
@endsection