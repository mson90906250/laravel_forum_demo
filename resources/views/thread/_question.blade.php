{{-- Editing --}}
<div class="card mb-2" v-if="editing">
    <div class="card-header level">

        <div class="flex">
            <input type="text" class="form-control" v-model="form.title">
        </div>

    </div>

    <div class="card-body">
        <wysiwyg name="body"
            :value="form.body"
            :trix-persist="persist"
            @trix-change="change"
            @persist-complete="editing = false"></wysiwyg>
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

    <div class="card-body trix-content" v-html="body"></div>

    <div class="card-footer" v-if="authorize('owns', thread)">
        <button class="btn btn-sm btn-warning" @click="toggleEdit">Edit</button>
    </div>
</div>
