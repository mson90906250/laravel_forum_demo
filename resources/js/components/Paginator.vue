<template>
    <ul class="pagination" v-if="shouldPaginate">

        <li class="page-item" v-show="prevUrl">
            <a class="page-link" href="" aria-label="Previous" rel="prev" @click.prevent="page--">
                <span aria-hidden="true">&laquo; Prev</span>
            </a>
        </li>

        <li class="page-item" v-for="n in dataSet.last_page">
            <a :class="currentPage(n)" href="" @click.prevent="page = n" v-text="n"></a>
        </li>

        <li class="page-item" v-show="nextUrl">
            <a class="page-link" href="" aria-label="Next" rel="next" @click.prevent="page++">
                <span aria-hidden="true">Next &raquo;</span>
            </a>
        </li>

    </ul>
</template>

<style>
    .current-page {
        color: #1d68a7 ! important;
        background-color: #e9ecef ! important;
    }
</style>

<script>
    export default {
        props: ['dataSet', 'ajaxFlag'],

        data() {
            return {
                page: 1,
                prevUrl: false,
                nextUrl: false,
            }
        },

        watch: {
            dataSet() {
                this.page = this.dataSet.current_page;
                this.prevUrl = this.dataSet.prev_page_url;
                this.nextUrl = this.dataSet.next_page_url;
            },

            page() {
                this.broadcast().updateUrl();
            }
        },

        computed: {
            shouldPaginate() {
                return !! this.prevUrl || !! this.nextUrl;
            },
        },

        methods: {
            broadcast() {
                return this.$emit('page-changed', this.page);
            },

            updateUrl() {
                if (this.ajaxFlag) return;
                history.pushState(null, null, '?page=' + this.page);
            },

            currentPage(page) {
                return ['page-link', page == this.page ? 'current-page' : ''];
            }
        }
    }
</script>
