@extends('layouts.app')

@section('content')
<div class="wrapper">
  <div class="container">
    <div class="main-nav">
      <div class="row">
        <div class="col-sm-12">
            <ul class="nav nav-pills mb-4">
              <li class="nav-item mr-1">
                <router-link to="/articles" class="nav-link">
                  <i class="fa fa-wikipedia-w"></i>
                   Artículos
                </router-link>
              </li>
              <li class="nav-item">
                <router-link to="/stats" class="nav-link">
                  <i class="fa fa-bar-chart"></i>
                   Estadísticas
                </router-link>
              </li>
            </ul>
          </div>
        </div>
      </div>
    <div class="content-wrapper">
      <router-view></router-view>
      <vue-progress-bar></vue-progress-bar>
    </div>
  </div>
</div>
@endsection
