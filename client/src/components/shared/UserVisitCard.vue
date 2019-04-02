<template>
    <div v-if="user" id="content">
        <div class="card">
            <span class="card-header">
                {{ user.name }} <span v-if="user.complement">(Colaborador Externo)</span>
                <span id="icon" v-if="!edit">
                    <router-link @click.native="added()" to="" v-if="icon == 'add'"><icon class="float-right" icon="plus"/></router-link>
                    <router-link @click.native="removed()" to="" v-if="icon == 'remove'"><icon class="float-right" icon="minus"/></router-link>
                </span>
            </span>
            <div class="card-body">
                <div class="row">
                    <span class="col text-left" v-if="user.code">Matricula: {{ user.code }}</span>
                    <span class="col text-right" v-if="user.turn">Turno: {{ user.turn }}</span>
                </div>
                <div class="row">
                    <span class="col text-left">Cargo: {{ user.occupation }}</span>
                </div>
                <div class="row">
                    <span class="col text-left">Setor: {{ user.group.name }}</span>
                </div>
                <div class="row">
                    <span class="col text-left">E-mail: 
                        <span v-if="user.email"> {{ user.email }} </span>
                        <span v-else class="bold"> Não possui E-mail cadastrado </span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import User from "@/entity/User";

export default {
    props: {
        user: '',
        edit: { default: false },
        icon: { default: false },
    },
    methods: {
        added() {
            this.$emit('added')
        },
        removed() {
            this.$emit('removed')
        }
    }
}
</script>

<style lang="scss" scoped>
    #content {
        max-width: 550px;
    }
</style>


