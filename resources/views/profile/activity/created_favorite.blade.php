@component('profile.activity.activity')

    @slot('heading')
        {{ $profileUser->name }} favorited a

        <a href="{{ route('thread.show', $activity->subject->favorited->thread->pathParams()) . sprintf('#reply-%d', $activity->subject->favorited->id) }}">
            reply
        </a>
    @endslot

    @slot('body')
        {{ $activity->subject->favorited->body }}
    @endslot

@endcomponent

