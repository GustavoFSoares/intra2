<template>
        <div class="container">
        
        <div class="closed" v-if="ombudsman.closed">
            <span>Finalizado</span>
        </div>
        <h3 class="text-danger text-right" v-if="permission == 'COMPANION'">Você está acompanhando!</h3>
        <div class='row'>
            <rows class="col-md-9">
                <h2>
                    #{{ombudsman.id}} - {{ ombudsman.type }} (<i>{{ ombudsman.origin.name }}</i>)
                </h2>    
            </rows>
            <rows>
                <import-file :id="filePlace" :only_exibition="true"/>
            </rows>
        </div>

        <hr>
        <row>
            <div id="patient">
                <div class="card border-secondary">
                    <div class="card-body">
                        <h5 class="card-subtitle mb-2 text-muted">Dados Pessoais:</h5>
                        <row>
                            <div class='row'>
                                <rows label="<b>Nome Paciente</b>">
                                    <span class="card-text">{{ ombudsman.ombudsmanUser.patientName }}</span>
                                </rows>

                                <rows label="<b>Data de Nascimento">
                                    <span class="card-text">{{ ombudsman.ombudsmanUser.birthday }}</span>
                                </rows>
                            </div>
                        </row>

                        <row>
                            <div class='row'>
                                <rows label='<b>Nome do Relator</b>'>
                                    <span class="card-text"> {{ ombudsman.ombudsmanUser.declarantName }} </span>
                                </rows>
                                
                                <rows label='<b>Telefone</b>'>
                                    <a :href="'tel:'+ombudsman.ombudsmanUser.phoneNumber">{{ ombudsman.ombudsmanUser.phoneNumber }}</a>
                                </rows>
                                
                                <rows label='<b>E-mail</b>' v-if="ombudsman.ombudsmanUser.email">
                                    <span class="card-text"> {{ ombudsman.ombudsmanUser.email }} </span>
                                </rows>
                            </div>
                        </row>
                        
                        <row label='<b>Endereço</b>'>
                            <span class="card-text"> {{ ombudsman.ombudsmanUser.address }} </span>
                        </row>
                    </div>
                </div>
            </div>
        </row>
        
        <hr>
        <div>
            <row>
                <div id="description" class="col">
                    <div class="card border-secondary">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Descrição:</h6>
                            <p class="card-text">
                                {{ ombudsman.ombudsmanUserDescription }}
                            </p>
                        </div>
                    </div>
                </div>
            </row>
            
            <row v-if="ombudsman.ombudsmanUserSugestion">
                <div id="sugestion" class="col">
                    <div class="card border-secondary">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Sugestão:</h6>
                            <p class="card-text">
                                {{ ombudsman.ombudsmanUserSugestion }}
                            </p>
                        </div>
                    </div>
                </div>
            </row>
        </div>
        
        <hr>
        <row>
            <div class="card border-secondary mb-2">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Demandas:</h6>
                    <div class="row">
                        <div v-for="(demand) in ombudsman.demands" class="col-md-4" :key="demand.id">
                            <p>
                                <icon icon="angle-double-right"/>
                                <span>{{demand.name}}</span>
                            </p>
                        </div>
                    </div>
                    
                    <div v-if="ombudsman.demands.length == 0">
                        <p class="text-center">
                            <b>Sem demandas associadas</b>
                        </p>
                    </div>
                </div>
            </div>
        </row>

        <hr>
        <div class='row'>
            <rows>
                <div v-if="ombudsman.origin.id == 'AMB'">
                    <label>Setor:</label>
                    <p><b>{{ ombudsman.group.name }}</b></p>
                </div>
                <div v-else-if="ombudsman.origin.id == 'INT'">
                    <label>Leito:</label>
                    <p><b>{{ ombudsman.bed }}</b></p>
                </div>
            </rows>

            <rows label="Horário do Relato">
                <p> <icon icon="clock"/>
                    {{ ombudsman.registerTime.date | humanizeDate }}
                </p>
            </rows>
        </div>

        <hr>
        <row>
            <div>
                <div class="card border-secondary">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Relato do Ouvidor:</h6>
                        <p class="card-text">
                            {{ ombudsman.ombudsmanDescription }}
                        </p>
                    </div>
                    <signature label="Ouvidor que relatou" :username="ombudsman.ombudsman.name"/>
                </div>
            </div>
        </row>

        <hr>
        <section class="mb-3" v-if="gotAdminPermission">
            <h4 class="title">Lista de Acompanhantes</h4>
            
            <div class="card border-secondary">
                <div class="card-body">
                    <add-users :manager_list="ombudsman.managerList" :tranmission_list="ombudsman.transmissionList" v-if="gotAdminPermission" @sendUser="addUser" />

                    <div class='row'>
                        <rows>
                            
                            <h4 class="title">Lista de Responsáveis:</h4>
                            <div id="manager" class="list-group">
                                <div v-for="(userAdmin, index) in ombudsman.managerList" :key="index" class="card">

                                    <router-link to="" class="text-left list-group-item list-group-item-action" data-toggle="collapse" :data-target="'#id-m'+index" aria-expanded="true" :aria-controls="'id-m'+index">
                                        <span class="float-left">{{ userAdmin.name }}</span>
                                        <router-link class="float-right" to="" v-if="gotAdminPermission" @click.native="removeUser(userAdmin, index, 'manager')">
                                            <icon class="text-danger" icon="minus-circle"/>
                                        </router-link>
                                    </router-link>

                                    <div :id="'id-m'+index" class="collapse" data-parent="#manager">
                                        <div class="card">
                                            <div class="card-body">
                                                <span class="float-right" v-if="userAdmin.email">{{ userAdmin.email }}</span>
                                                <span class="float-right" v-else><b>Sem email cadastrado</b></span>                                
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <h5 class="text-danger" v-if="ombudsman.managerList.length == 0"> Nenhum Responsável Adicionado </h5>
                            </div>

                        </rows>
                        <rows>
                            
                            <h4 class="title">Observando:</h4>
                            <div id="companion" class="list-group">
                                <div v-for="(userAdmin, index) in ombudsman.transmissionList" :key="index" class="card">

                                    <router-link to="" class="text-left list-group-item list-group-item-action" data-toggle="collapse" :data-target="'#id-c'+index" aria-expanded="true" :aria-controls="'id-c'+index">
                                        <span class="float-left">{{ userAdmin.name }}</span>
                                        <router-link class="float-right" to="" v-if="gotAdminPermission" @click.native="removeUser(userAdmin, index, 'companion')">
                                            <icon class="text-danger" icon="minus-circle"/>
                                        </router-link>
                                    </router-link>

                                    <div :id="'id-c'+index" class="collapse" data-parent="#companion">
                                        <div class="card">
                                            <div class="card-body">
                                                <span class="float-right" v-if="userAdmin.email">{{ userAdmin.email }}</span>
                                                <span class="float-right" v-else><b>Sem email cadastrado</b></span>                                
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <h5 class="text-danger" v-if="ombudsman.transmissionList.length == 0"> Nenhum Observador Adicionado </h5>
                            </div>

                        </rows>
                    </div>
                    
                </div>
            </div>
        </section>

        <section class="mt-3 mb-3">
            <chat :id="'om'+id" model_path="ombudsman-model" title="Acompanhamento da Ouvidoria" v-if="ombudsman.exist()" :can_write="permission != 'COMPANION'" :closed="ombudsman.closed"/>
        </section>

        <section id="closing-area">
            <closing v-model="ombudsman.responseToUser" name="Ombudsman-closing" v-validate data-vv-rules="required" data-vv-as="Fechamento do Ouvidor" :status="ombudsman.status" :ombudsmanClosingName="ombudsman.ombudsmanToResponse.name" :gotAdminPermission="gotAdminPermission" @click.native="showThisGuy = true"/>
            <require-text class="validate-text" :error="errors.has('Ombudsman-closing')" :text="errors.first('Ombudsman-closing')" v-if="showThisGuy"/>
        </section>
        
        <div id="buttons">
            <row>
                <div class="buttons" v-if="gotAdminPermission">
                    <button v-if="(ombudsman.status == 'waiting-manager' || ombudsman.status == 'registered')  && ombudsman.exist()" class="btn btn-outline-warning btn-lg" type="button" @click="closeChat()" :disabled="sending">
                        Finalizar Mensagens <icon icon="spinner" spin v-if="sending"/>
                    </button>
                    <button v-else-if="ombudsman.status == 'closed' && ombudsman.exist()" class="btn btn-outline-clean btn-lg" type="button" @click="finishOmbudsman()" :disabled="sending">
                        Arquivar Ouvidoria <icon icon="spinner" spin v-if="sending"/>
                    </button>
                </div>
                <div class="buttons">
                    <router-link class="btn btn-outline-primary btn-lg" :to="{name: 'ouvidoria'}" tag="button" :disabled="sending">
                        Voltar
                    </router-link>
                </div>
            </row>
        </div>
    </div>
</template>

<script>
import Ombudsman from "@/entity/Ombudsman";
import { FormRw, FormRws, Require, VueSelect } from "@/components/shared/Form";
import model, { getter } from '@/model/ombudsman-model'
import Alert from '@/components/shared/Alert'

export default {
    data() {
        return {
            id: this.$route.params.id,
            ombudsman: new Ombudsman(),
            sending: false,
            permission: 'undefined',
            values: {
                groups: [],
                ombudsmans: [],
                demands: [],
            },
            showThisGuy: false,
        }
    }, 
    methods: {
        closeChat() {
            this.$validator.validateAll().then(success => {
                
                if( success ) {

                    Alert.YesNo('Você está <u>finalizando</u> esta ouvidoria', 'Tem certeza que podemos finalizar?').then(res => {
                        if(res) {
                            this.sending = true
                            model.closeChat(this.ombudsman).then(res => {
                                this.sending = false
                                this.ombudsman = Object.assign( this.ombudsman, res )
                            }).catch(err => {
                                this.sending = false
                            })
                        }
                    })
                
                }
            })
        },
        finishOmbudsman() {
            Alert.YesNo('Você está <u>arquivando</u> esta ouvidoria', 'Tem certeza que podemos arquivar?').then(res => {
                if(res) {

                    this.sending = true
                    model.finishOmbudsman( this.ombudsman ).then(res => {
                        this.sending = false
                        this.$router.push({ name: 'ouvidoria'})
                    }).catch(err => {
                        this.sending = false
                    })

                }
            })
        },
        addUser(user, type) {
            switch (type) {
                case 'manager':
                    this.ombudsman.managerList.push(user)
                    
                    break;
                
                case 'companion':
                    this.ombudsman.transmissionList.push(user)
                    break;
            }
        },
        removeUser(user, index, type) {
            Alert.YesNo("Tem certeza?", "Você está removendo um responsável. Deseja Continuar?").then( res => {
                if(res) {
                    switch (type) {
                    case 'manager':
                        this.ombudsman.managerList.splice(index, 1)
                        
                        break;
                    
                    case 'companion':
                        this.ombudsman.transmissionList.splice(index, 1)
                        break;
                    }
                    model.removeManager(this.id, user, type)
                }
            })
        },
        getPermission() {
            if(this.permission == 'undefined') {
                model.gotPermission(this.id).then(permission => { this.permission = permission; } )
            } else {
                return this.permission
            }
        },
    },
    computed: {
        gotAdminPermission() {
            return (this.getPermission() != 'MANAGER' && this.getPermission() != 'COMPANION') ? true : false
        },
        filePlace() {
            let id = false
            if(this.ombudsman.id) {
                id = `Ombudsman/${this.ombudsman.id}`
            }
            return id
        },
    },
    components: {
        'row': FormRw,
        'rows': FormRws,
        'add-users': require('./AddUsers.vue').default,
        'closing': require('./Closing.vue').default,
        'chat': require('@/components/shared/chat').default,
        'import-file': require('@/components/shared/VFile/V-file-pdf').default,
        'signature': require('@/components/shared/Signature.vue').default,
    },
    mounted() {
        this.getPermission()
        getter.getOmbudsmanById(this.id).then(res => { this.ombudsman = res ? new Ombudsman(res) : new Ombudsman(); })
    }
}
</script>

<style lang="scss" scoped>
    .closed {
        color: var(--danger);
        font-size: 15rem;
        z-index: 1;
        opacity: 0.3;
        position: absolute;
        transform: translate(0em, 1.5251em) rotate(-45deg);
        pointer-events: none;
    }

    .buttons {
        display: inline;
    }

    #closing-area .validate-text {
        position: absolute;
        margin-top: -40px;
    }
</style>
