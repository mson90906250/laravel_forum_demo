@extends('layouts.app');

@section('head')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endsection

@section('content')

    <thread-create inline-template>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">

                    @include('components.alert')

                    <div class="card">
                        <div class="card-header">發布新文章</div>

                        <div class="card-body">

                            <form @submit.prevent="submit">

                                <div class="form-group">
                                    <label for="title">標題</label>
                                    <input type="text"
                                        v-model="title"
                                        class="form-control"
                                        value="{{ old('title') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="channel">文章類型</label>
                                    <select class="form-control"
                                        v-model="channel_id"
                                        id="channel" required>

                                        <option value="">Choose One......</option>
                                        @foreach($channels as $channel)
                                            <option value="{{ $channel->id }}" {{ old('channel_id') == $channel->id ? 'selected' : '' }}>
                                                {{ $channel->slug }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="body">內容</label>
                                    <wysiwyg name="body"
                                        :trix-persist="persist"
                                        @trix-change="change"
                                        @persist-complete="redirect"></wysiwyg>
                                </div>

                                <div class="form-group">
                                    <div class="g-recaptcha"
                                        data-sitekey="6LfidrIZAAAAAIGayWLjFkRNUsj9wuZoQY4JL8T-"></div>
                                </div>

                                <button type="submit" class="btn btn-primary">發布</button>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </thread-create>
@endsection
