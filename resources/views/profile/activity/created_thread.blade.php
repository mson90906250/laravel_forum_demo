@component('profile.activity.activity')

    @slot('heading')
        {{ $profileUser->name }} published

        <a href="{{ route('thread.show', $activity->subject->pathParams()) }}">
            {{ $activity->subject->title }}
        </a>
    @endslot

    @slot('body')
        {!! $activity->subject->body !!}
    @endslot

@endcomponent
