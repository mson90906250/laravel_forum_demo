export default {
    data() {
        return {
            delayTimer: 0
        }
    },

    methods: {
        delay(callback, ms) {
            clearTimeout(this.delayTimer);
            this.delayTimer = setTimeout(callback, ms);
        }
    }
}
