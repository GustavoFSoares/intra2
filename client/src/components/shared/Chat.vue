<template>
    <div class='container'>
        <section v-if="!closed" id="message">
            <div v-if="title">
                <h2 class="text-center">{{ title }}</h2>
                <hr>
            </div>
            <div class="row">
                <div id="chatbox">
                    <div class="chat" v-for="(chat, index) of chats" :key="index">
                        <div class="card anotherchat" v-bind:class="{'youchat':chat.user==user}" @mouseover="chat.read = true">
                            <div class="card-body">
                                <blockquote class="content-chat">
                                    <h5 v-if="chat.user!=user" class="card-title">
                                        {{ chat.user }}
                                        <icon class="icon text-warning float-right" icon="exclamation-triangle" v-if="!chat.read"/>
                                    </h5>
                                    <p v-bind:class="{'anotherchat-message':chat.user!=user}" class="card-text">{{ chat.message }}</p>
                                </blockquote>
                                <footer class="time blockquote-footer text-right">
                                    {{ chat.time.date | humanizeDate }}
                                </footer>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="sendMessage-box" class="row" v-if="can_write">
                <div class="col-md-10" id="sendMessage-content">
                    <textarea v-model="message" line="10" class="form-control" @click="readAllMessages()" :disabled="chats == null" placeholder="Escreva seu relato:"/>
                </div>
                <div class="col-md" id="sendMessage-button">
                    <router-link to="" class="btn btn-outline-secondary btn-lg" tag="button" @click.native="setMessage()" :disabled="!this.message">
                        Enviar
                    </router-link>
                </div>
            </div>
        </section>

        <section v-else id="logs">
            <div class="text-left">
                <div class="card">
                    <div class="card-body historic">
                        <h6 class="card-subtitle mb-2 text-muted">Histórico do Relato   :</h6>
                        <div class="card-text">
                            <span class="historic-body" v-for="(line, index) of chats" :key="index">
                                <span class="time">
                                    <span v-if="line.time.date">{{ moment(line.time.date).format('YYYY/MM/DD - HH:mm:ss') }}</span>
                                    <span v-else>{{ line.time }}</span>
                                </span>
                                <span class="user">{{ line.user }}:</span>
                                <span class="message">{{ line.message }}</span>
                                <br/>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
var socket
import Socket from "@/model/chat-model";

export default {
    data() {
        return {
            message: '',
            user: this.$session.get('user').name,
            socket: null,
            moment: require('moment'),
            chats: null
        }
    },
    props: {
        id: '',
        model_path: '',
        can_write: { default: true},
        title: { default: false},
        closed: { default: false},
    },
    methods: {
        setMessage: function () {
            this.sendMessage(this.$route.params.id, this.message)
            this.message = '';
        },
        doScroll() {
            setTimeout(() => {
                let chatbox = document.getElementById("chatbox");
                    if(chatbox) {
                        chatbox.scrollTop = chatbox.scrollHeight;
                    }
            }, 0);
        },
        readAllMessages() {
            this.chats.forEach(chat => {
                chat.read = true
            });
        },
        prepareFunctions: function () {
            return new Promise(resolve => {
                
                let model = require("@/model/"+this.model_path)
                if( typeof model.chat.getChats != 'function' ) {
                    throw new Error('"getChats" not implemented on model.getter!')
                } else {
                    this.getChats = model.chat.getChats
                }
                
                if( typeof model.chat.saveMessage != 'function' ) {
                    throw new Error('"saveMessage" not implemented on model.default!')
                } else {
                    this.saveMessage = model.chat.saveMessage
                }
                resolve(true)
            })
        },
        saveMessage(){}, // this methods will be subscribed
        getChats(){}, // this methods will be subscribed
        sendMessage(id, message) {
            this.saveMessage(id, message)
            this.socket.sendMessage(message)
        }
    },
    created: function () {
        this.prepareFunctions().then(res => {
            this.getChats(this.$route.params.id).then(res => {
                this.chats = res
                this.doScroll()
            })
        })
        
        this.socket = new Socket(this.id, this.user)
    },
    mounted() {
        this.socket.io.on(`${this.id}/message`, (message) => {
            this.socket.isYou(message.user) ? message.read = true : ''
            
            this.chats.push(message);
            this.doScroll()
        });
    }
}
</script>

<style lang="scss" scoped>
    #chatbox {
        width: 100%;
        height: 300px;
        overflow-y: scroll;
    }

    .chat {
        margin-top: 10px;
        border-radius: 10px;
        display: flex;
    }

    .anotherchat {
        margin-right: 30%;    
        margin-left: 0%;
        background-color: rgba(206, 206, 206, 0.329);
    }
    .anotherchat-message {
        text-align: left;
    }

    .youchat {
        margin-right: 5px;
        margin-left: auto;
        max-width: 80%;
        background-color: rgba(197, 241, 185, 0.514);
        text-align: right;
    }

    #sendMessage-box {
        margin-top: 20px;
    }
    #sendMessage-button {
        margin-top: 5px;
    }

    .icon {
        margin-left: 15px;
    }

    .historic {
        background-color: rgba(224, 224, 224, 0.425);
    }

    .historic-body {
        font-size: 15px;

        .time {
            color: rgb(8, 184, 8);
        }
    }
</style>
