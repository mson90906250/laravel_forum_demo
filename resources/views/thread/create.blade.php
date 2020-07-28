@extends('layouts.app');

@section('head')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                @include('components.alert')

                <div class="card">
                    <div class="card-header">Create Thread</div>

                    <div class="card-body">
                        <form id="thread-form" action="{{ route('thread.store') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="channel">Channel</label>
                                <select class="form-control" name="channel_id" id="channel" required>
                                    <option value="">Choose One......</option>
                                    @foreach($channels as $channel)
                                        <option value="{{ $channel->id }}" {{ old('channel_id') == $channel->id ? 'selected' : '' }}>
                                            {{ $channel->slug }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="body">Body</label>
                                <wysiwyg name="body"></wysiwyg>
                            </div>

                            <div class="form-group">
                                <div class="g-recaptcha" data-sitekey="6LfidrIZAAAAAIGayWLjFkRNUsj9wuZoQY4JL8T-"></div>
                            </div>

                            <button type="submit"
                                    class="btn btn-primary">Publish</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
