@forelse($threads as $thread)
    <div class="card mb-2">
        <div class="card-header">
            <div class="level">

                <div class="flex min-width-0">
                    <h4 class="text-omit">

                        @if (auth()->check() && $thread->hasUpdatesFor(auth()->user()))
                            <strong>
                                <a href="{{ route('thread.show', $thread->pathParams()) }}">
                                    {{ $thread->title }}
                                </a>
                            </strong>
                        @else
                            <a href="{{ route('thread.show', $thread->pathParams()) }}">
                                {{ $thread->title }}
                            </a>
                        @endif

                    </h4>

                    <h6>
                        Posted by <a href="{{ route('profile.show', ['user' => $thread->owner->name]) }}">{{ $thread->owner->name }}</a>
                        <img src="{{ $thread->owner->avatar_path }}" width="25px" alt="">
                    </h6>
                </div>

                <a href="{{ route('thread.show', $thread->pathParams()) }}">
                    <strong>{{ sprintf('%s %s', $thread->replies_count, Str::plural('reply', $thread->replies_count)) }}</strong>
                </a>

            </div>
        </div>
        <div class="card-body">
            <p>{{ $thread->body }}</p>
        </div>
        <div class="card-footer level">
            {{ sprintf(
                '%s %s',
                $thread->visits()->count(),
                Str::plural('Visit', $thread->visits()->count())) }}
        </div>
    </div>
@empty
    There are no relavent threads at this time.
@endforelse
