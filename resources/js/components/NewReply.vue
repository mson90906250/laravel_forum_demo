<template>
    <div>
        <div v-if="signIn">
            <div class="bg-white p-1">
                <wysiwyg id="new-reply"
                    name="body"
                    accept-file="false"
                    @trix-change="change"></wysiwyg>
            </div>

            <button class="btn btn-primary mt-3" @click="addReply">reply</button>
        </div>

        <p v-else
            class="text-center">
            Please <a href="/login">sign in</a> to participate in the forum
        </p>
    </div>
</template>

<script>
    import Tribute from "tributejs";

    export default {
        data() {
            return {
                body: '',
                endpoint: location.pathname + '/replies',
            }
        },

        mounted() {
            this.prepareTribute();
        },

        methods: {
            change(data) {
                this.body = data.value;
            },

            addReply() {
                axios.post(this.endpoint, { body: this.body} )
                    .then(({data}) => {
                        this.body = '';
                        flash('Your reply has been posted');
                        this.$emit('created', data);
                    })
                    .catch(({response}) => {
                        flash(response.data.message, 'danger');
                    })
            },

            prepareTribute() {
                if (! this.signIn) return;
                let delayFlag = false; //用來控制axios觸發的時間間隔
                let currentData = [];
                let tribute = new Tribute({
                    fillAttr: 'name',
                    lookup: 'name',
                    values(text, callback) {

                        if (delayFlag) return callback(currentData);
                        delayFlag = true;

                        axios.get('/api/users?name=' + text)
                            .then(({data}) => callback(data));

                        setTimeout(() => delayFlag = false, 500);
                    },
                });

                tribute.attach(document.getElementById('new-reply'));
            }
        }
    }
</script>
