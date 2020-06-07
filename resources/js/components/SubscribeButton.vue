<template>
    <div v-if="signIn">
        <button :class="classes" @click="subscribe">{{ active ? 'Subscribed' : 'Subscribe' }}</button>
    </div>
</template>

<script>
    export default {
        props: ['active'],

        computed: {
            classes() {
                return ['btn', 'btn-sm', this.active ? 'btn-primary' : 'btn-outline-primary'];
            }
        },

        methods: {
            subscribe() {
                axios[this.active ? 'delete' : 'post'](location.pathname + '/subscriptions');

                flash(this.active ? 'Unsubscribed!!' : 'Subscribed!!');

                this.$emit(this.active ? 'unsubscribed' : 'subscribed');
            }
        }
    }
</script>
