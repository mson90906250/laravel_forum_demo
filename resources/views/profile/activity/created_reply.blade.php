@component('profile.activity.activity')

    @slot('heading')
        {{ $profileUser->name }} replied to

        <a href="{{ route('thread.show', $activity->subject->thread->pathParams()) }}">
            {{ $activity->subject->thread->title }}
        </a>
    @endslot

    @slot('body')
        {{ $activity->subject->body }}
    @endslot

@endcomponent

