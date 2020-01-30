@extends('layouts.repoapp')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Repository List</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="my-3">
                            @if (count($repoLists) > 0)
                                <ul class="list-group">
                                    @foreach ($repoLists as $repoList)
                                        <li class="list-group-item">{{ $repoList }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="lead">No Repository to show</p>
                            @endif

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
