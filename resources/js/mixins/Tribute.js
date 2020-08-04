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
                values(text, callback) {

                    if (delayFlag) return callback(currentData);
                    delayFlag = true;

                    axios.get('/api/users?name=' + text)
                        .then(({data}) => callback(data));

                    setTimeout(() => delayFlag = false, 500);
                },
            });

            tribute.attach(document.getElementById(data.id));
        }
    }
}
