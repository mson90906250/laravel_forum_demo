import Tribute from "tributejs";
import delayTimer from "./DelayTimer.js";

export default {

    methods: {
        prepareTribute(data) {
            if (! this.signIn) return;
            let tribute = new Tribute({
                fillAttr: 'name',
                lookup: 'name',
                selectTemplate: function (item) {
                    return '@' + item.original.name + '&#141;'; //在後面添加無法顯示的符號 以方便做區隔 '&#141;' => 'U+008D'
                },
                values(text, callback) {
                    delayTimer.methods.delay(() => {
                        if (text.length < 1) return;
                        axios.get('/api/users?name=' + text)
                            .then(({data}) => callback(data));
                    }, 500);
                },
            });

            tribute.attach(document.getElementById(data.id));
        }
    }
}
