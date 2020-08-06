<template>
    <div>
        <div v-if="signIn">
            <div class="bg-white p-1">
                <wysiwyg id="new-reply"
                    name="body"
                    accept-file="false"
                    @trix-mounted="prepareTribute"
                    @trix-change="change"></wysiwyg>
            </div>

            <button class="btn btn-primary mt-3" @click="addReply">回覆</button>
        </div>

        <p v-else
            class="text-center">
            請先 <a href="/login">登入</a> 來參與回覆
        </p>
    </div>
</template>

<script>
    import tribute from '../mixins/Tribute.js';

    export default {
        data() {
            return {
                body: '',
                endpoint: location.pathname + '/replies',
            }
        },

        mixins: [tribute],

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
            }
        }
    }
</script>
