<template>
    <div>
        <input id="trix" type="hidden" :name="name" :value="value">
        <trix-editor input="trix"
            @trix-change="change"></trix-editor>
    </div>
</template>

<script>
    import Trix from "trix";

    export default {
        props: ['name', 'value'],

        methods: {
            change(e) {
                this.$emit('trix-change', {"value": e.target.innerHTML});
            }
        }
    }
</script>
