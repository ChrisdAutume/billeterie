@extends('layouts.email')

@section('title')
    Confirmation de don
@endsection

@section('content')
    <p>Bonjour {{ $don->surname }} {{ $don->name }},</p>
    <p></p>
    <p>La Fondation te remercie pour ton don. Grâce à toi, le Hall N sera aménagé afin d'avoir un bel espace de détente et de travail à côté du Foyer.</p>
    <p></p>
    <p>Bien cordialement,
    </p>
    <p></p>
    <p>L'équipe R2D</p>
@endsection
