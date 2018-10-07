@extends('layouts.master')

@section('sublayout-css')
<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
<link href="{{ @asset('/css/AdminLTE.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ @asset('/css/skins/skin-blue.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('bodycontent')
    <div class="skin-blue layout-top-nav">
        <div class="wrapper">
            <header class="main-header">
                <nav class="navbar navbar-static-top">
                    <div class="container">
                        <div class="navbar-header">
                            <a href="{{ route('home') }}" class="navbar-brand"><b>{{ config('billeterie.event.name') }}</b> {{ config('billeterie.event.subname') }}</a>
                        </div>

                        <div class="collapse navbar-collapse" id="navbar-collapse">
                            <ul class="nav navbar-nav navbar-right">
                                @if(Auth::check())
                                 @if(Auth::user()->isAdmin(10))
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Contenu <span class="caret"></span></a>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a href="{{ route('lists_pages') }}">Pages</a></li>
                                                <li><a href="{{ route('admin_list_files') }}">Gestionnaire de fichiers</a></li>
                                                <li><a href="{{ route('lists_mail_templates') }}">Template mails</a></li>
                                            </ul>
                                        </li>
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Tarification <span class="caret"></span></a>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a href="{{ route('admin_prices_index') }}">Tarifs</a></li>
                                            </ul>
                                        </li>
                                    @endif
                                    @if(Auth::user()->isLevel(2))
                                        <li class="dropdown">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Listes <span class="caret"></span></a>
                                            <ul class="dropdown-menu" role="menu">
                                                <li><a href="{{ route('lists_items_add') }}">Gestion</a></li>
                                            </ul>
                                        </li>
                                    @endif
                                    @if(Auth::user()->isLevel(2))
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Guichets <span class="caret"></span></a>
                                                <ul class="dropdown-menu" role="menu">
                                                    @if(Auth::user()->isLevel(10))
                                                        <li><a href="{{ route('admin_guichet_create') }}">Gestion</a></li>
                                                    @endif
                                                    <li><a href="{{ route('admin_guichet') }}">Validation entrée</a></li>
                                                    <li><a href="{{ route('admin_orders_create') }}">Vente manuelle</a></li>
                                                    <li><a href="{{ route('admin_express_orders') }}">Vente express</a></li>
                                                    <li><a href="{{ route('guichet_export') }}">Télécharger la liste des billets</a></li>
                                                </ul>
                                            </li>
                                    @endif
                                    @if(Auth::user()->isAdmin())
                                        <li><a href="{{ route('admin_orders_list') }}">Liste des commandes</a></li>
                                        <li><a href="{{ route('admin_sell') }}">Stats</a></li>
                                        <li><a href="{{ route('admin_users_index') }}">Utilisateurs</a></li>
                                        <li class="dropdown">
                                          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Application<span class="caret"></span></a>
                                          <ul class="dropdown-menu" role="menu">
                                              <li><a href="{{ route('admin_events_list') }}">Evenements</a></li>
                                              <li><a href="{{ route('admin_partners_list') }}">Partenaires</a></li>
                                              <li><a href="{{ route('admin_push') }}">Notifications</a></li>
                                          </ul>
                                        </li>
                                    @endif
                                <li class="dropdown user user-menu">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <span class="hidden-xs">{{ Auth::user()->name }}</span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <!-- User image -->
                                        <li class="user-header">
                                            <p>
                                                {{ Auth::user()->name }}
                                            </p>
                                        </li>
                                        <!-- Menu Footer-->
                                        <li class="user-footer">
                                            <div class="pull-left">
                                                <!--<a href="#" class="btn btn-default btn-flat">Profile</a>-->

                                            </div>
                                            <div class="pull-right">
                                                <a href="{{ route('logout') }}" class="btn btn-default btn-flat">Sign out</a>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                                    @else
                                    <li><a href="{{ route('login') }}">Se connecter</a></li>
                                    @endif
                            </ul>
                        </div><!-- /.navbar-collapse -->
                    </div><!-- /.container-fluid -->
                </nav>
            </header>

            <div class="content-wrapper">
                <div class="container">
                    <section class="content-header">
                        @include('display-errors')
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <h1>
                            @yield('title')
                            <small>@yield('smalltitle')</small>
                        </h1>
                    </section>
                    <section class="content">
                        @yield('content')
                    </section>
                </div>
            </div>

            <footer class="main-footer">
                <div class="container">
                    <div class="pull-right">
                        <a href="{{ route('view_page', ['slug'=>'cgu-cgv']) }}">CGU - CGV</a>
                    </div>
                    <strong>En cas de problème,</strong> <a href="mailto:{{ config('billeterie.contact') }}">contactez nous</a>
                </div>
            </footer>
        </div>
    </div>
@endsection
