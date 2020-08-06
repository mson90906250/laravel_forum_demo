@extends('layouts.app');

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-md-8">
                @include('thread._list')

                {{ $threads->links() }}
            </div>

            <div class="col-md-4">
                @if (count($trending))
                    <div class="card mb-2">
                        <div class="card-header">
                            最多瀏覽人數文章
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                @foreach($trending as $thread)
                                    <li class="list-group-item">
                                        <a href="{{ $thread->path }}">{{ "$loop->iteration. " . $thread->title }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
