<template>
    <div class="card mb-2">
        <div :id="replyId" class="card-header" :class="isBest ? 'bg-success' : ''">
            <div class="level">

                <div class="flex">
                    <a :href="'/profiles/' + reply.owner.name" v-text="reply.owner.name">

                    </a>
                    said <span v-text="ago"></span>
                </div>

                <favorite :reply="reply"></favorite>

            </div>
        </div>

        <div class="card-body">
            <div v-if="editing">
                <form @submit.prevent="update">
                    <textarea class="mb-1 form-control" v-model="body" required></textarea>
                    <button class="btn btn-sm btn-primary">Update</button>
                    <button type="button" class="btn btn-sm btn-link" @click="editing = false; body = reply.body">Cancel</button>
                </form>
            </div>

            <div v-else v-html="body"></div>
        </div>

        <div class="card-footer level" v-if="authorize('owns', reply) || authorize('owns', reply.thread)">
            <div v-if="authorize('owns', reply)">
                <button class="ml-1 btn btn-info btn-sm" @click="editReply">Edit</button>
                <button class="ml-1 btn btn-danger  btn-sm" @click="destroy">Delete</button>
            </div>

            <button class="ml-a btn btn-success btn-sm" v-show="authorize('owns', reply.thread) && ! isBest" @click="markBestReply">Best Reply</button>
        </div>

    </div>
</template>


<script>
    import Favorite from './Favorite.vue';
    import moment from 'moment';

    export default {
        props: ['reply'],

        data() {
            return {
                editing: false,
                body: this.reply.body,
                isBest: this.reply.isBest,
            }
        },

        computed: {
            replyId() {
                return 'reply-' + this.reply.id
            },

            ago() {
                return moment(this.created_at).fromNow();
            }
        },

        components: {
            Favorite
        },

        created() {
            window.events.$on('best-reply-marked', replyId => {
                this.isBest = this.reply.id === replyId;
            });
        },

        methods: {
            update() {
                axios.patch('/replies/' + this.reply.id, {
                    body: this.body
                })
                .then(({data}) => {
                    this.editing = false;
                    this.body = data.body;
                    this.$emit('reply-updated', data.body);
                })
                .catch(error => {
                    flash(error.response.data, 'danger');
                    this.body = this.reply.body;
                });
            },

            destroy() {
                axios.delete('/replies/' + this.reply.id);

                this.$emit('deleted', this.reply.id);
            },

            editReply() {
                this.editing = true;
                this.body = this.body.replace(/<a .+>(@[\w\-]+)<\/a>/, '$1');
            },

            markBestReply() {
                axios.post('/thread/'+ this.reply.id +'/best');

                window.events.$emit('best-reply-marked', this.reply.id);
            }
        }
    }
</script>

