<template>
    <div class="container-fluid">
        
        <h1>{{ title }}</h1>

        <div class="mb-3">
            <div class='row filters'>
                
            </div>
        </div>
        
        <div class="form-group form-row col">
            <input type="search" class="filter form-control" :disabled="!reports" @input="search.filter = $event.target.value" placeholder="Nome:"/>
        </div>

        <h2>{{ typeExibited }}</h2>

        <table v-if="reports" class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Evento</th>
                    <th scope="col">Setor do Relato</th>
                    <th scope="col">Setor da Falha</th>
                    <th scope="col">Horário da Falha</th>
                    <th scope="col">Paciente Envolvido</th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(report) of searchList" :key="report.id" v-bind:class="{'table-disabled': report.closed}">
                    <th>{{ report.id }}</th>
                    <td>{{ report.event.substr(0, 40) }}</td>
                    <td>{{ report.reportPlace }}</td>
                    <td>{{ report.failedPlace }}</td>
                    <td>{{ moment(report.failedTime.date).format('DD/MM/YYYY HH:mm') }}</td>
                    <td>
                        <icon class="text-success" icon="check" v-if="report.patientInvolved"/>
                        <icon class="text-danger" icon="times" v-else/>
                    </td>
                    <td>
                        <router-link :to='`notificacao-de-incidentes/edit/${report.id}`' v-if="gotPermission && report.closed != true">
                            <icon v-tooltip.top="'Editar'" icon="edit"/>
                        </router-link>
                        <router-link :to='`notificacao-de-incidentes/detalhe/${report.id}`' v-if="report.filtered || report.closed">
                            <icon v-tooltip.top="'Detalhe'" class="text-warning" icon="search"/>
                        </router-link>
                    </td>
                    <td> <span v-if="report.count" class="badge badge-danger"> {{ report.count }} </span> </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import model, { getter } from '@/model/incident-reporting-model'
import moment from 'moment'
import { Checkbox, FormRws } from '@/components/shared/Form'
import Socket from "@/model/chat-model";

export default {
    data() {
        return {
            title: 'Lista de Incidentes',
            socket: null,
            enterpriseTypes: [{id: 'hu', name:"HU"}, {id: 'hpsc', name: "HPSC"}],
            typeExibited: '',
            reports: [],   
            moment: moment,
            user: this.$session.get('user').name,
            group: this.$session.get('user').group,
            search: {
                filter: '',
                type: '',
                dbFilters: { }
            },
            types: '',
            permission: 'undefined',
        }
    },
    methods: {
        loadValues() {
            getter.getIncidents(this.group).then(res => { this.reports = res })
        },
        loadCloseds() {
            this.search.dbFilters.closed = !this.search.dbFilters.closed
            getter.getIncidentsWithFilters(this.group, this.search.dbFilters).then(res => { this.reports = res; })
        },
        loadFiltereds() {
            if(this.search.dbFilters.filtered==undefined) {
                this.search.dbFilters.filtered = false                
            } else {
                this.search.dbFilters.filtered = !this.search.dbFilters.filtered
            }
            getter.getIncidentsWithFilters(this.group, this.search.dbFilters).then(res => { this.reports = res; })
        },
        cleanFilters() {
            this.search.dbFilters = {  }
            getter.getIncidentsWithFilters(this.group, this.search.dbFilters).then(res => { this.reports = res; })
        },
    },
    created: function () {
        this.socket = new Socket(`ir`, this.user)
        model.socketInit(this.socket)
    },
    mounted() {
        this.loadValues()

        this.socket.io.on(`ir`, (message) => {
            if( !this.socket.isYou(message.user) ) {
                model.getSocketId(message.id).then( id => {
                    this.reports.find( (element, index, array) => (element.id == id) ? element.count++ : "" )
                })
            }
        })
    },
    computed: {
        searchList() {

            if(this.search.type) {
                this.typeExibited = this.search.type.name
                let type = new RegExp(this.search.type.id.trim(), 'i')
                
                return this.reports.filter(reports => {
                    if( type.test(reports.failedPlace.enterprise)) {
                        return type
                    }
                })

            }

            if(this.search.filter) {
                let exp = new RegExp(this.search.filter.trim(), 'i')
                
                return this.reports.filter(reports => {
                    
                    if( exp.test(reports.id)) {
                        return exp
                    } else if( exp.test(reports.event)) {
                        return exp
                    } else if( exp.test(reports.failedPlace)) {
                        return exp
                    } else if( exp.test(reports.reportPlace)) {
                        return exp
                    } else if( exp.test(moment(reports.failedTime.date).format('DD/MM/YYYY HH:mm'))) {
                        return exp
                    }
                })
            } else {
                return this.reports
            }
        },
        gotPermission() {
            if(this.permission == 'undefined') {
                model.gotPermission().then(permission => { this.permission = permission } )
            } else {
                return (this.permission != 'USER' && this.permission) ? true : false
            }
        }
    },
    components: {
        'checkbox': Checkbox,
        'rows': FormRws,
    }
}
</script>

<style scoped>
    .table-disabled {
        cursor: default;
        text-decoration: none;
        color: #8a8a8a9c;
    }

    .filters {
        margin-left: 20%;
        margin-right: 20%;
    }

    .filter-not-selected {
        color: #8a8a8a9c;
    }

</style>
