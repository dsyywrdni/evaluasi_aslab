@extends('layouts.app')

@section('content')
    @include('partials.dashboard-analytics', ['judul' => 'Dashboard Administrator', 'hasilRoute' => 'admin.penilaian.hasil'])
@endsection