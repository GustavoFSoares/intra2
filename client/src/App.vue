<template>
    <div id="app">
        <nav-bar v-model="userUpdating" />
        
        <modal v-model="banner" ref="modal" @close="banner = false" title="Semana Sipat HPSC">
            <img src="@/../static/img/pos-sipat.jpg" alt="relatorio-cipa-hpsc" style="width: 48em; height: 32em"/>
        </modal>

        <div class="container-fluid">
            <div class="row">
                <rows>
                    <top-alert v-if="alert.warning" :title="alert.warning.title" :description="alert.warning.description" type="warning"/>
                </rows>

                <rows>
                    <top-alert v-if="alert.danger" :title="alert.danger.title" :description="alert.danger.description" type="danger"/>
                </rows>
            </div>
        </div>

        <div>
            <router-link to="/">
                <img id="logo" class="rounded mx-auto" src="@/../static/img/logo-prefeitura.png">
            </router-link>
        </div>

        <router-view @rootEvent="userUpdating++"/>
        <footer-app/>
        
    </div>
</template>

<script>
import AlertGetters from '@/services/alerts/getters.js'
import NavBar from '@/components/NavBar.vue'
import Footer from '@/components/Footer.vue'
import Modal from "@/components/shared/Modal";

import { FormRws, TopAlert } from "@/components/shared/Form/index.js"
import Vue from 'vue'

export default {
    name: 'App',
    data() {
        return {
            alert: {
                warning: '',
                danger: '',
            },
            userUpdating: 0,
            banner: false,	
        }	
    },	
    watch: {	
        banner(val) {	
            if(val) {	
                this.$refs.modal.show()	
            } else {	
                this.$refs.modal.close()	
            }
        }
    },
    components: {
        'nav-bar': NavBar,
        'footer-app': Footer,
        'rows': FormRws,
        'top-alert': TopAlert,
        'modal': Modal,
    },
    methods: {
        getWarningAlert() {
            AlertGetters.getWarning().then(res => this.alert.warning = res)
        },
        getDangerAlert() {
            AlertGetters.getDanger().then(res => this.alert.danger = res)
        },
        autoload() {
            window.$session = this.$session
        },
        mountPrototype() {
            var context = this
            Vue.prototype.$alert = {
                success: function(content = 'conteudo', title = ' Sucesso!' ) {
                    context.$uiv_notify({
                        type: 'success',
                        title:  title,
                        content: content,
                        customClass: 'show',
                        html: true,
                    })
                },
                danger: function(content = 'conteudo', title = ' Erro!' ) {
                    context.$uiv_notify({
                        type: 'danger',
                        title: title,
                        content: content,
                        customClass: 'show',
                        html: true,
                    })
                },
                warning: function(content = 'conteudo', title = ' Aviso!' ) {
                    context.$uiv_notify({
                        type: 'warning',
                        title: title,
                        content: content,
                        customClass: 'show',
                        html: true,
                    })
                },
                info: function(content = 'conteudo', title = ' Informação!' ) {
                    context.$uiv_notify({
                        type: 'info',
                        title: title,
                        content: content,
                        customClass: 'show',
                        html: true,
                    })
                },
                
            }
            window.$alert = this.$alert
        }
            
    },
    mounted() {
        this.getWarningAlert()
        this.getDangerAlert()
    },
    created() {
        this.mountPrototype()
        this.autoload()
    }

}
</script>

<style lang="scss">
    :root {
        --font-color: #2c3e50;
        --disabled: rgba(105, 105, 105, 0.548)
    }

    #app {
        font-family: 'Avenir', Helvetica, Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-align: center;
        color: var(--font-color);
        margin-bottom: 100px;
    }

    #logo {
        width: 20%;
        margin-top: 10px;
    }

    .bold {
        font-weight: bold;
    }

    .disabled {
        .btn, button {
            pointer-events: none;
            cursor: none;
            opacity: 0.4;
        }
    }
    
    .text-disabled {
        color: var(--disabled);
    }
    .text-default {
        color: var(--font-color);
    }

    .table-disabled {
        cursor: default;
        text-decoration: none;
        color: #8a8a8a9c;
    }

    .btn-outline-warning:hover:not(:disabled)  {
        color: #ffffff;
    }

    .btn-outline-light {
        color: black;
    }

    .btn-outline-clean {
        color: var(--font-color);
        background-color: transparent;
        background-image: none;
        border-color: var(--font-color);

        &.active, &:hover:not([disabled='disabled']) {
            color: #fff;
            background-color: var(--font-color);
            border-color: var(--font-color);
        }
    }

    div {
        /* outline: 1px dotted gray; */
    }

    .fade.alert, .vue-tooltip {
        z-index: 2000;
    }
</style>
