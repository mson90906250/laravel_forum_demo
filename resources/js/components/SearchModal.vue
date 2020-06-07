<template>
    <div>
        <button class="btn" type="button" data-toggle="modal" data-target="#modal"><fa-icon icon="search"/></button>

        <div id="modal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header search-modal-header">
                        <input type="text"
                            class="form-control"
                            placeholder="Search Threads ..."
                            @input="search"
                            v-model="keyword">
                    </div>

                    <div class="modal-body">

                        <div v-if="results.length > 0">
                            <div class="list-group">

                                <div class="card mb-1" v-for="result in results">
                                    <a href="#">
                                        <div class="card-body">
                                            <h5 class="card-title" v-html="getHighlight('title', result)"></h5>
                                            <p class="card-text" v-html="getHighlight('body', result)"></p>
                                        </div>
                                    </a>
                                </div>

                            </div>
                        </div>

                        <div v-else>No results</div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="reset">Cancel</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                isSearching: false,
                test: 'list',
                keyword: '',
                results: [],
            }
        },

        methods: {
            reset() {
                this.results=[];
                this.keyword="";
            },

            search() {
                if (this.keyword.length == 0) return;

                axios.get('/threads/search?q=' + this.keyword)
                    .then(({data}) => {
                        this.results = data.hits.hits;
                    });
            },

            getHighlight(field, result) {
                let highlight = result.highlight[field] ?
                    result.highlight[field][0] :
                    result._source[field];

                return highlight.substring(0, 100) + '...';
            }
        }
    }
</script>
