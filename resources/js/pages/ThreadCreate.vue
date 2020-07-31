<script>
    export default {
        data() {
            return {
                "title": "",
                "channel_id": "",
                "body": "",
                "persist": false,
                "redirctUrl": "",
            };
        },

        methods: {
            change(data) {
                this.body = data.value;
            },

            submit(e) {
                let data = new FormData;

                data.append('title', this.title);
                data.append('channel_id', this.channel_id);
                data.append('body', this.body);
                data.append('g-recaptcha-response', e.target['g-recaptcha-response'].value);

                axios.post('/threads/create', data)
                    .then(({request}) => {
                        this.redirectUrl = request.responseURL;
                        this.persist = true;
                    })
                    .catch(({response}) => {
                        console.log(response);
                    })
            },

            redirect() {
                window.location.href = this.redirectUrl;
            }
        }
    }
</script>>
