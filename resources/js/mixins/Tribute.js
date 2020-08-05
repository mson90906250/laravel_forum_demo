import Tribute from "tributejs";

export default {

    methods: {
        prepareTribute(data) {
            if (! this.signIn) return;
            let delayFlag = false; //用來控制axios觸發的時間間隔
            let currentData = [];
            let tribute = new Tribute({
                fillAttr: 'name',
                lookup: 'name',
                selectTemplate: function (item) {
                    return '@' + item.original.name + '&#141;'; //在後面添加無法顯示的符號 以方便做區隔 '&#141;' => 'U+008D'
                },
                values(text, callback) {

                    if (delayFlag) return callback(currentData);
                    delayFlag = true;

                    axios.get('/api/users?name=' + text)
                        .then(({data}) => callback(data));

                    setTimeout(() => delayFlag = false, 200);
                },
            });

            tribute.attach(document.getElementById(data.id));
        }
    }
}
