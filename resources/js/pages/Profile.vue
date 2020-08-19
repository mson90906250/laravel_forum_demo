<script>
    export default {

        data() {
            return {
                currentWidth: document.body.clientWidth
            };
        },

        mounted() {
            window.addEventListener('resize', () => this.currentWidth = document.body.clientWidth);
        },

        destroyed() {
            window.removeEventListener('resize');
        }
    }
</script>>
