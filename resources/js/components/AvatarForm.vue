<template>
    <div>

        <div class="card-header text-muted level">
            <img :src="avatar" class="mr-2 mb-2" width="40" alt="">
            <h1 v-text="profileUser.name"></h1>
        </div>

        <form v-if="canUpdate" enctype="multipart/form-data">
            <image-upload name="avatar" @loaded="onLoad"></image-upload>
        </form>

    </div>
</template>

<script>
    import ImageUpload from './ImageUpload.vue';
    import moment from 'moment';

    export default {
        props: ['profileUser'],

        components: { ImageUpload },

        data() {
            return {
                avatar: this.profileUser.avatar_path
            }
        },

        computed: {
            canUpdate() {
                return this.authorize(user => {
                    return user.id === this.profileUser.id;
                });
            },
        },

        methods: {
            onLoad(avatar) {
                this.avatar = avatar.src;

                this.persist(avatar.file);
            },

            persist(avatar) {
                let data = new FormData;
                data.append('avatar', avatar);

                axios.post(`/api/users/${this.profileUser.id}/avatar`, data)
                    .then(() => flash('Avartar Uploaded!!!'));
            }
        }
    }
</script>
