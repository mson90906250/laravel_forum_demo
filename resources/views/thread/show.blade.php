@extends('layouts.app');

@section('head')
    <link rel="stylesheet" href="{{ asset('css/vendor/tribute.css') }}">
@endsection

@section('content')
    <thread-view :active="{{ json_encode($thread->isSubscribedTo) }}"
        :thread = "{{ $thread }}"
        inline-template
        v-cloak>

        <div class="container">
            <div class="row">

                <div class="col-md-8">

                    @include('thread._question')

                    <replies :data="{{ $thread->replies }}"
                        @removed="repliesCount--"
                        @added="repliesCount++"
                        @replies-count-changed="newCount => repliesCount = newCount"></replies>

                </div>

                <div class="col-md-4">
                    <div class="card mb-2">

                        <div class="card-body">
                            <p>This thread was posted {{ $thread->created_at->diffForHumans() }} by <a href="#">{{ $thread->owner->name }}</a>.</p>
                            Now it has <span v-text="repliesCount"></span> {{ Str::plural('comment', $thread->replies_count) }}.

                            <div class="level mt-3">

                                <subscribe-button
                                    class="mr-2"
                                    :active="isActive"
                                    @subscribed="isActive = true"
                                    @unsubscribed="isActive = false"></subscribe-button>

                                <button v-if="authorize('isAdmin')"
                                    @click="toggleLock"
                                    class="btn btn-sm btn-outline-danger"
                                    v-text="locked ? 'Unlock' : 'Lock'"></button>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </thread-view>
@endsection
