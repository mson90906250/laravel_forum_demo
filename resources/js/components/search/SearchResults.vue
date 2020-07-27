<template>
    <div>
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
</template>

<script>
    export default {
        props: ['results'],

        methods: {
            getHighlight(field, result) {
                let highlight = result.highlight[field] ?
                    result.highlight[field][0] :
                    result._source[field];

                return highlight.substring(0, 100) + '...';
            }
        }
    }
</script>
