<template>
    <div>
        <div v-if="signIn">
            <textarea id="body" class="form-control" rows="5" placeholder="say something to reply ?" v-model="body"></textarea>
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
                endpoint: location.pathname + '/replies'
            }
        },

        mounted() {
            this.prepareTribute();
        },

        methods: {
            addReply() {
                axios.post(this.endpoint, { body: this.body} )
                    .catch(({response}) => {
                        flash(response.data, 'danger');
                    })
                    .then(({data}) => {
                        this.body = '';
                        flash('Your reply has been posted');
                        this.$emit('created', data);
                    });
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

                tribute.attach(document.getElementById('body'));
            }
        }
    }
</script>>
