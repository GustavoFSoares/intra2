<template>
    <div class="container-fluid">
        <div>
            <div class="mt-3">
                <h1>{{ title }}</h1>

                 <router-link class="button btn btn-outline-secondary btn-lg" :to="{name: 'hht/add'}" tag="button">
                    Cadastrar Treinamento
                </router-link>
            </div>
        </div>

        <div class="icon-chart">
            <router-link :to="{'name': 'hht/relatorio'}">
                <icon icon="chart-pie" size="2x"/>
            </router-link>
        </div>

        <div class="training-filters">

            <div class="places ">
                <button class="btn btn-outline-clean btn-lg" v-bind:class="{ 'active': filter.place=='HU' }" @click="filter.place = 'HU'">
                    Hospital Universitário
                </button>
                <button class="btn btn-outline-clean btn-lg" v-bind:class="{ 'active': filter.place=='HPSC' }" @click="filter.place = 'HPSC'">
                    Hospital de Pronto Socorro
                </button>
            </div>

            <hr>
            <div class="types">
                <button class="btn btn-outline-primary btn-lg" v-bind:class="{ 'active': filter.type=='Técnico-Específico' }" @click="filteringType('Técnico-Específico')">
                    Técnico-Específico
                </button>
                <button class="btn btn-outline-info btn-lg" v-bind:class="{ 'active': filter.type=='Institucional' }" @click="filteringType('Institucional')">
                    Institucional
                </button>
            </div>

         </div>

         <table v-if="trainings" class="table table-striped mt-3">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Local</th>
                    <th scope="col">Horário Treinamento</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Carga Horária</th>
                    <th scope="col">Realizado</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(training, index) of searchList" :key="index">
                    <td scope="row">{{ training.id }}</td>
                    <td>{{ training.name }}</td>
                    <td>{{ training.enterprise.substr(0, 11) }}</td>
                    <td>
                        <span> {{ moment(training.beginTime.date).format('DD/MM/YYYY') }} - 
                            <span><b>{{ moment(training.beginTime.date).format('HH:mm') }}</b></span>
                            até <span><b>{{ moment(training.endTime.date).format('HH:mm') }}</b></span>
                        </span>
                    </td>
                    <td>{{ training.type }}</td>
                    <td>{{ getWorkload(training.workload) }} horas</td>
                    <td> <input type="checkbox" @change="isDone(training)" :disabled="training.done ? true : false" v-model="training.done"/></td>
                    <td>
                        <router-link :to='`hora-homem-treinamento/lista-participantes/${training.id}`'>
                            <icon v-tooltip.top="'Lista de Participantes'" class="text-success" icon="user-plus"/>
                        </router-link>
                        <router-link :to='`hora-homem-treinamento/edit/${training.id}`' v-if="!training.done">
                            <icon v-tooltip.top="'Editar'" icon="edit"/> 
                        </router-link>
                        <router-link @click.native="remove(training.id, index)" to="" v-if="!training.done">
                            <icon v-tooltip.top="'Remover'" class="text-danger" icon="trash-alt"/>
                        </router-link>
                    </td>
                </tr>
            </tbody>
        </table>
        
    </div>   
</template>

<script>
import model, { getter } from "@/model/training-model";
import moment from 'moment'
import Alert from "@/components/shared/Alert";

export default {
    data() {
        return {
            title: "Lista de Treinamentos",
            trainings: [],
            filter: {
                text: '',
                place: 'HU',
                type: '',
            },
            moment: moment,
            alert: {
                done: { title: "Terminando o treinamento?", message: "Você está marcando este treinamento como realizado."+
                    "<BR>Ao fazer isso, você será incapaz de <b>editar</b> o treinamento e <b>Adicionar outros participantes</b>"+
                    "<BR>Tem certeza que deseja realizar essa ação?" },
                remove: { title: "Tem certeza que deseja excluir?", message: "Você está excluindo um treinamento. <BR>Tem certeza que deseja continuar?" }
            },
        }
    },
    methods: { 
        remove(id, index){
            Alert.Confirm(this.alert.remove.message).then(res => {
                if(res){
                    model.doDelete(id)
                    this.trainings.splice(index, 1)
                }
            })
        },
        isDone(training) {
            Alert.YesNo(this.alert.done.title, this.alert.done.message).then(res => {
                if(res){
                    model.isDone(training.id)
                } else {
                    training.done = false
                }}
            )
        },
        getWorkload(workload) {
            workload = model.getWorkloadObject(workload)
            return `${workload.hour}:${workload.minute}`
        },
        filteringType(type) {
            if( this.filter.type == type ) {
                this.filter.type = false
            } else {
                this.filter.type = type
            }
        },
    },
    computed: {
        searchList() {
            if(this.filter.text) {
                let exp = new RegExp(this.filter.text.trim(), 'i')
                
                let list = ''
                return this.secondFiler.filter(training => {
                    training = training.row
                    if( exp.test(training.id)) {
                        return exp
                    } else if( exp.test(training.training.name)) {
                        return exp
                    } else if( exp.test(training.group.name)) {
                        return exp
                    } else if( exp.test(training.bed)) {
                        return exp
                    } else if( exp.test(training.type)) {
                        return exp
                    } else if( exp.test( this.$options.filters.humanizeDate(training.registerTime.date) ) ) {
                        return exp
                    }
                })
            } else {
                return this.secondFiler
            }
        },
        secondFiler() {
            if(this.trainings.length == 0) {
                return []
            }
            
            let trainings = this.trainings
            if(this.filter.place) {
                trainings = trainings.filter(tr => {
                    if(tr.enterprise == this.filter.place) {
                        return tr
                    }
                })
            }
            
            if(this.filter.type) {
                trainings = trainings.filter(tr => {
                    if(tr.type.toUpperCase() == this.filter.type.toUpperCase()) {
                        return tr
                    }
                })
            }
            return trainings
        },
    },
    mounted() {
        getter.getTrainings().then(res => { this.trainings = res })
    },
}
</script>

<style lang="scss" scoped>
    .training-filters {
        margin-top: 1rem !important;
        
        display: flex;
        flex-wrap: wrap;
        flex-direction: column-reverse;
        
        width: 50%;
        
        hr { 
            width: 100%;
        }
        
        .places, .types {
            width: 100%;
            display: flex;
            justify-content: center;
        }
        
        button {
            margin-left: 10px;
        }
    }

    .icon-chart {
        position: absolute;
        right: 0;
        top: 0;
        transform: translate(-2em, 5.5251em);
    }
</style>

