<template>
    <div id="navbar">
        
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <router-link class="navbar-brand" to="/">Home</router-link>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item" v-for="(screen, index) in screens" :key="index">
                        <a v-if="screen.externalLink" class="nav-link" :href="screen.link">{{ screen.name }}</a>
                        <router-link v-else class="nav-link" :to="screen.link">{{ screen.name }}</router-link>
                    </li>
                </ul>

                <div class="navbar-nav">
                    <menu-login v-model="value"/>
                </div>
                
            </div>
        </nav>

    </div>
</template>

<script>
import MenuLogin from '@/components/login/Menu.vue';

export default {
    data(){
        return {
            "message": "",
            "screens": [
                {name: "Convênios", link:"/convenios", externalLink: false },
                {name: "Ramais", link:"/ramais", externalLink: false },
                {name: "Arquivos", link:"http://gamp-web/?tela=arquivos", externalLink: true },
                {name: "POPS", link:"/pops", externalLink: false },
                {name: "Institucional", link:"http://gamp-web/?tela=institucional", externalLink: true },
                {name: "Comissões", link:"http://gamp-web/?tela=comissoes", externalLink: true },
            ],
        }
    },
    props: { value: { default: 0} },
    methods: { },
    components: {
        'menu-login': MenuLogin
    }
}
</script>

<style lang="scss" scoped>
    #navbar {
        width: 100%;
        height: 100%;
        display: block;
        position: initial;
    }
</style>