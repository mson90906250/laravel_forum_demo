@extends('layouts.app')

@section('content')
    <profile class="container" inline-template>
        <div class="col-md-8"
            :class="currentWidth < 768 ? '' : 'offset-2'">
            <div class="card mb-2">
                <avatar-form :profile-user="{{ $profileUser }}"></avatar-form>
            </div>

            {{-- profile_user's threads --}}
            @forelse($activities as $date => $activity)

                <h3 class="mt-4 mb-1 font-weight-bold">{{ $date }}</h3>

                @foreach($activity as $record)
                    @if (view()->exists(sprintf('profile.activity.%s', $record->type)))
                        @include(sprintf('profile.activity.%s', $record->type), ['activity' => $record])
                    @endif
                @endforeach

            @empty

                There is no activity for this user

            @endforelse

        </div>
    </profile>
@endsection
