<template>
    <div>
        <reply v-for="(reply, index) in items"
            :thread-locked="threadLocked"
            :key="reply.id"
            :reply="reply"
            @deleted="remove(index)"
            @reply-updated="newBody => updateReply(index, newBody)"></reply>

        <paginator :ajaxFlag="ajaxFlag" :dataSet="dataSet" @page-changed="fetch"></paginator>

        <p v-if="threadLocked">此文章已被封鎖, 無法回覆</p>

        <new-reply v-else @created="showReply"></new-reply>
    </div>
</template>

<script>
    import Reply from './Reply.vue';
    import NewReply from './NewReply.vue';
    import collecion from '../mixins/Collection.js';

    export default {
        props: ['data', 'threadLocked'],

        components: { Reply, NewReply },

        mixins: [collecion],

        data() {
            return {
                dataSet: false,
                items: [],
                ajaxFlag: false, //確認axios是否正在進行, 正在進行(true)的話, 則不進行下一個axios
            }
        },

        created() {
            this.fetch();
        },

        methods: {
            fetch(page) {
                if (this.ajaxFlag) return;
                this.ajaxFlag = true;
                axios(this.url(page))
                    .then(this.refresh);
            },

            url(page) {
                if (! page) {
                    let query = location.search.match(/page=(\d+)/);
                    page = query ? query[1] : 1;
                }

                return location.pathname + '/replies?page=' + page;
            },

            refresh({ data }) {
                this.ajaxFlag = false;
                this.dataSet = data;
                this.items = data.data;

                window.scrollTo(0, 0);
            },

            showReply(data) {
                if (data.page == this.dataSet.current_page) {
                    this.add(data.reply);
                } else {
                    this.$emit('replies-count-changed', data.count);
                    this.fetch(data.page);
                }
            },

            updateReply(index, newBody) {
                this.items[index].body = newBody;
                flash('Updated!');
            }
        }
    }
</script>>
