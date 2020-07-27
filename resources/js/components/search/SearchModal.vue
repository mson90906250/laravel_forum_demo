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
                            @input="search"
                            v-model="keyword">

                    </div>

                    <!-- body -->
                    <div class="modal-body">

                        <!-- <search-tags ></search-tags> -->

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

    export default {
        data() {
            return {
                isSearching: false,
                test: 'list',
                keyword: '',
                currentKeyword: '',
                isSearching: false,
                results: [],
            }
        },

        components: { SearchResults },

        methods: {
            reset() {
                this.results=[];
                this.keyword="";
            },

            search() {
                if (this.isSearching || this.keyword.length == 0 || this.currentKeyword === this.keyword) return;

                this.isSearching = true;
                this.currentKeyword = this.keyword;

                axios.get('/threads/search?q=' + this.keyword)
                    .then(({data}) => {
                        this.results = data.hits.hits;
                        this.isSearching = false;
                    });

                setTimeout(() => this.isSearching = false, 200);
            }
        }
    }
</script>
