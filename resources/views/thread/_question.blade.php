{{-- Editing --}}
<div class="card mb-2" v-if="editing">
    <div class="card-header level">

        <div class="flex">
            <input type="text" class="form-control" v-model="form.title">
        </div>

    </div>

    <div class="card-body">
        <textarea class="form-control" rows="5" v-model="form.body"></textarea>
    </div>

    <div class="card-footer">

        <div class="level">

            <button class="btn btn-sm btn-primary mr-1" @click="update">Update</button>

            <button class="btn btn-sm btn-secondary" @click="resetForm">Cancel</button>

            @can('update', $thread)
                <form action="{{ route('thread.destroy', $thread->pathParams()) }}" method="POST" class="mb-0 ml-a">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-link">Delete Thread</button>
                </form>
            @endcan

        </div>

    </div>
</div>

{{-- View --}}
<div class="card mb-2" v-else>
    <div class="card-header level">

        <div class="flex">
            <a href="{{ route('profile.show', $thread->owner) }}">
                <img src="{{ $thread->owner->avatar_path }}" width="25" alt="">
                {{ $thread->owner->name }}
            </a>
            posted : <span v-text="title"></span>
        </div>

    </div>

    <div class="card-body" v-text="body"></div>

    <div class="card-footer" v-if="authorize('owns', thread)">
        <button class="btn btn-sm btn-warning" @click="editing = true">Edit</button>
    </div>
</div>