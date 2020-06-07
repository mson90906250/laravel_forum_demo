let user = window.App.user;

module.exports = {
    owns(items, prop = "user_id") {

        let flag = true;

        if (items.length >= 2) {
            items.forEach(el => {
               flag = flag && this.owns(el);
               if (! flag) return false;
            });
        } else {
            flag = items[prop] === user.id;
        }

        return flag;
    },

    isAdmin() {
        return ['MarkLin'].includes(user.name);
    }
};
