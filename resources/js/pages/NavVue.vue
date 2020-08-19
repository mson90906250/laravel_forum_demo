<script>
    export default {

        data() {
            return {
                currentWidth: document.body.clientWidth
            };
        },

        methods: {
            // 根據寬度的變化, 改變搜尋按鈕位置
            checkWidth(data) {

                if (data.width < 768) {
                    this.$refs.container.insertBefore(data.el, this.$refs.navbarToggler);
                } else {
                    this.$refs.searchBar.appendChild(data.el);
                }

                this.currentWidth = data.width;
            }
        }
    }
</script>>
