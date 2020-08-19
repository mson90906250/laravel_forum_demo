<template>
    <div>
        <button class="btn" type="button" data-toggle="modal" data-target="#modal"><fa-icon icon="search"/></button>

        <div id="modal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <!-- header -->
                    <div class="modal-header search-modal-header">

                        <input type="text"
                            class="form-control"
                            placeholder="Search Threads ..."
                            @input="delay(search, 200)"
                            v-model="keyword">

                    </div>

                    <!-- body -->
                    <div class="modal-body">

                        <search-results :results="results"></search-results>

                    </div>

                    <!-- footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="reset">Cancel</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import SearchResults from "./SearchResults.vue";
    import delayTimer from "../../mixins/DelayTimer.js";

    export default {
        data() {
            return {
                test: 'list',
                keyword: '',
                currentKeyword: '',
                results: []
            }
        },

        mixins: [delayTimer],

        components: { SearchResults },

         mounted() {
            let $vm0 = this;
            window.addEventListener('resize', function (e) {
                $vm0.$emit('resize', {el: $vm0.$el, width: window.screen.width});
            });
        },

        destroyed() {
            window.removeEventListener('resize');
        },

        methods: {
            reset() {
                this.results=[];
                this.keyword="";
            },

            search() {
                if (this.keyword.length == 0 || this.currentKeyword === this.keyword) return;

                this.currentKeyword = this.keyword;

                axios.get('/threads/search?q=' + this.keyword)
                    .then(({data}) => {
                        this.results = data.hits.hits;
                    });
            }
        }
    }
</script>
