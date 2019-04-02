const io = require('socket.io-client')
const Socket = class {
    constructor(id, userName) {
        this.id = id
        this.userName = userName
        if (window.location.hostname == 'gamp-web') {
            this.io = io(`http://10.100.1.30:3000`);
        } else {
            this.io = io(`http://${window.location.hostname}:3000`);
        }
        this.setUserName(userName)
    }

    setUserName(user) {
        this.io.emit('join', { 'id': this.id, 'username': user, 'time': { date: new Date() } });
    }
    sendMessage(message) {
        this.io.emit('message', { 'id': this.id, 'msg': message, 'time': { date: new Date() } });
    }
    isYou(user) {
        return window.$session.get('user').name == user ?
            true : false
    }
    listenMessages(id) {
        return new Promise(resolve => {
            this.io.on(`${id}`, (message) => {
                message.id = message.id.substr(2)
                
                if( !this.isYou(message.user) ) {
                    resolve(message)
                }
            });
        })   
    }
}
export default Socket