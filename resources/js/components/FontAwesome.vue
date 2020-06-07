<script>

    // 用法 <fa-icon icon="bell"/>

    import { library } from '@fortawesome/fontawesome-svg-core'
    import { fas } from '@fortawesome/free-solid-svg-icons' // 如果只想使用某些icon, 可以直接用該icon的名, ex: faUserSecret, faHeart...
    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

    library.add(fas)

    export default FontAwesomeIcon
</script>

