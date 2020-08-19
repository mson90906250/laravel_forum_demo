<script>
    export default {

        data() {
            return {
                currentWidth: document.body.clientWidth
            };
        },

        mounted() {
            window.addEventListener('resize', this.checkWidth);
            this.checkWidth();
        },

        destroyed() {
            window.removeEventListener('resize', this.checkWidth);
        },

        methods: {
            // 根據寬度的變化, 改變搜尋按鈕位置
            checkWidth() {
                if (document.body.clientWidth < 768) {
                    this.moveSearch(true);
                    this.moveNotification(true);
                } else {
                    this.moveSearch(false);
                    this.moveNotification(false);
                }

                this.currentWidth = document.body.clientWidth;
            },

            moveSearch(isMobile = false) {
                let search = this.$refs.search.$el;

                if (isMobile) {
                    this.$refs.container.insertBefore(
                        search,
                        this.$refs.navbarToggler
                    );
                } else {
                    this.$refs.searchContainer.appendChild(search);
                }
            },

            moveNotification(isMobile) {
                if (! this.$refs.notification) return;

                let notification = this.$refs.notification.$el;
                let search = this.$refs.search.$el;

                if (isMobile) {
                    this.$refs.container.insertBefore(
                        notification,
                        search
                    );
                } else {
                    this.$refs.notificationContainer.appendChild(notification);
                }
            }
        }
    }
</script>>
