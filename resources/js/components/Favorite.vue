<template>
    <button :class="classes" @click="toggle">
        <fa-icon icon="heart"/>
        <span v-text="favoritesCount"></span>
    </button>
</template>

<script>
    export default {
        props: ['reply', 'is_auth'],

        data() {
            return {
                favoritesCount: this.reply.favoritesCount,
                isFavorited: this.reply.isFavorited,
            }
        },

        computed: {
            classes() {
                return ['btn', 'btn-sm', this.isFavorited ? 'btn-primary' : 'btn-secondary'];
            },

            endpoint() {
                return '/replies/' + this.reply.id + '/favorite';
            },
        },

        methods: {
            toggle() {

                if (! this.authorize(user => 1)) {
                    alert('請先登入');
                    return;
                }

                return this.isFavorited ? this.unfavorite() : this.favorite();

            },

            favorite() {
                axios.post(this.endpoint);

                this.isFavorited = true;

                this.favoritesCount++;
            },

            unfavorite() {
                axios.delete(this.endpoint);

                this.isFavorited = false;

                this.favoritesCount--;
            }
        }
    }
</script>



