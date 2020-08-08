<template>
    <div class="card mb-2">
        <div :id="replyId" class="card-header" :class="isBest ? 'bg-success' : ''">
            <div class="level">

                <div class="flex">
                    <a :href="'/profiles/' + reply.owner.name" v-text="reply.owner.name">

                    </a>
                    回覆於 <span v-text="ago"></span>
                </div>

                <favorite :reply="reply"></favorite>

            </div>
        </div>

        <div class="card-body">
            <div v-if="editing">
                <form @submit.prevent="update">
                    <wysiwyg :id="tributeId"
                        name="body"
                        accept-file="false"
                        :value="body"
                        @trix-mounted='prepareTribute'
                        @trix-change="change"></wysiwyg>
                    <button class="btn btn-sm btn-primary">更新</button>
                    <button type="button" class="btn btn-sm btn-link" @click="editing = false; body = reply.body">取消</button>
                </form>
            </div>

            <div v-else
                v-html="body"
                class="trix-content"></div>
        </div>

        <div class="card-footer level" v-if="(authorize('owns', reply) || authorize('owns', reply.thread)) && ! threadLocked">
            <div v-if="authorize('owns', reply)">
                <button class="ml-1 btn btn-info btn-sm" @click="editReply">編輯</button>
                <button class="ml-1 btn btn-danger  btn-sm" @click="destroy">刪除</button>
            </div>

            <button class="ml-a btn btn-success btn-sm" v-show="authorize('owns', reply.thread) && ! isBest" @click="markBestReply">最佳回覆</button>
        </div>

    </div>
</template>


<script>
    import Favorite from './Favorite.vue';
    import moment from 'moment';
    import tribute from '../mixins/Tribute.js';

    export default {
        props: ['reply', 'threadLocked'],

        data() {
            return {
                editing: false,
                body: this.reply.body,
                isBest: this.reply.isBest,
                tributeId: ''
            }
        },

        mixins: [tribute],

        computed: {
            replyId() {
                this.tributeId='edit-reply-' + this.reply.id;
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
            change(data) {
                this.body = data.value;
            },

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
                    flash(error.response.data.message, 'danger');
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

