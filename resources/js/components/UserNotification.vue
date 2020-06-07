<template>
    <li class="nav-item dropdown" v-if="notifications.length">
        <a id="navbarDropdown" class="nav-link" href="#" role="button" data-toggle="dropdown">
            <fa-icon icon="bell" :style="{ color: 'tomato' }"/>
        </a>

        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

            <a class="dropdown-item"
                :href="notification.data.link"
                v-for="notification in notifications"
                v-text="notification.data.message"
                @click="markAsRead(notification)"></a>

        </div>
    </li>
</template>

<script>
    export default {
        data() {
            return {
                notifications: false,
                userInfo: window.App.user,
            }
        },

        computed: {
            endpoint() {
                return '/profiles/'+ this.userInfo.name +'/notifications';
            }
        },

        created() {
            axios.get(this.endpoint)
                .then(response => this.notifications = response.data);
        },

        methods: {
            markAsRead(notification) {
                axios.delete(this.endpoint + '/' + notification.id);
            }
        }
    }
</script>
