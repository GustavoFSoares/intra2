const express = require('express')
const app = express()
const http = require('http').Server(app)
const io = require('socket.io')(http)

io.on('connection', (socket) => {
    socket.on('message', (res) => {
        io.emit(`${res.id}/message`, { 'user': socket.username, 'message': res.msg, 'read': false, time: res.time }) 
        io.emit( res.id.substr(0,2) , { 'user': socket.username, 'id': res.id, 'time': res.time })
        return io
    })
    socket.on('join', (res) => {
        if (res.username != null) {
            socket.username = res.username
        }
    })
})

http.listen(3000, () => console.log('Running Socket IO on port 3000'))
