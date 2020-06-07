<script>
    import Replies from '../components/Replies.vue';
    import SubscribeButton from '../components/SubscribeButton.vue';

    export default {
        props: ['thread', 'active'],

        components: { Replies, SubscribeButton },

        data() {
            return {
                repliesCount: this.thread.replies_count,
                isActive: this.active,
                locked: this.thread.locked,
                title: this.thread.title,
                body: this.thread.body,
                editing: false,
                form: {}
            }
        },

        created() {
            this.resetForm();
        },

        methods: {
            toggleLock() {
                let uri = `/lock-thread/${this.thread.slug}`;

                axios[this.locked ? 'delete' : 'post'](uri);

                this.locked = ! this.locked;
            },

            update() {
                let uri = `/threads/${this.thread.channel.slug}/${this.thread.slug}`;

                axios.patch(uri, this.form)
                    .then(() => {
                        this.title = this.form.title;
                        this.body = this.form.body;

                        this.editing = false;

                        flash('Your thread has been updated!!');
                    })
                    .catch(({response}) => flash(response.data, 'error'));
            },

            resetForm() {
                this.form = {
                    title: this.title,
                    body: this.body
                };

                this.editing = false;
            }
        }
    }
</script>>
