<template>
    <div
        class="alert alert-flash"
        :class="'alert-' + level"
        role="alert"
        v-show="show"
        v-text="body"></div>
</template>

<script>
    export default {
        props: ['initMessage'],

        data() {
            return {
                body: this.message,
                level: '',
                show: false
            }
        },

        created() {
            if (this.initMessage) {
                this.flash({message: this.initMessage});
            }

            window.events.$on('flash', data => this.flash(data));
        },

        methods: {
            flash(data) {
                this.body = data.message;
                this.level = data.level ?? 'success';
                this.show = true;

                this.hide();
            },

            hide() {
                setTimeout(() => {
                    this.show = false;
                }, 3000);
            }
        }
    }
</script>

<style>
    .alert-flash {
        position: fixed !important;
        right: 25px;
        bottom: 25px;
    }

    .alert-error {
        background-color: rgb(238, 167, 167);
    }
</style>
