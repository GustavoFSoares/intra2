<template>
    <div class="container-fluid">
        <h1>{{ title }}</h1>

        <div class="page-container">
            <div class="navigation" >
                    
                <div class="slider-menu">
                    <div class="menu-content">
                        <h5>Navegação</h5>
                        <ul class="list-group" v-scroll-spy-active="{class: 'watched'}" v-scroll-spy-link>
                            <router-link class="list-group-item" to="" v-for="section in sections" :key="section.id">
                                {{ section.label }}
                            </router-link>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="content">
                <div class="row">
                    <import-file :id="filePlace" :file_name="ombudsman.id" :post_function="uploadFile"/>
                </div>

                <div data-target=".slider-menu" v-scroll-spy>
                                    
                    <section :id="sections.code.id">
                        <row :label="sections.code.label">
                            <div v-if="!id">
                                <v-select v-model="ombudsmanId" name="Ombudsman-Id" :data-vv-as="sections.code.label" v-validate data-vv-rules="required" label="id" :options="values.ombudsmans" @input="loadOmbudsman()" :disabled="values.ombudsmans.length == 0"/>
                                <require-text :error="errors.has('Ombudsman-Id')" :text="errors.first('Ombudsman-Id')"/>
                            </div>
                            <div v-else>
                                <h3>Nº {{ombudsman.id}}</h3>
                            </div>
                        </row>
                    </section>

                    <section :id="sections.userInformations.id" class="mb-3">
                        <div id="user-informations-title">
                            <h4 class="title">{{ sections.userInformations.label }}</h4>
                        </div>
                        
                        <div id="user-informations-person" class="card border-secondary mb-2">
                            <div class="card-body">
                                
                                <div>
                                    <h5 class="subtitle">{{subtitles.personData.title}}:</h5>
                                </div>
                                <hr>

                                <div class='row'>
                                    <rows :label="subtitles.personData.name" class="col-md-5">
                                        <input type="text" name="Ombudsman-ombudsmanUser-patientName" v-validate data-vv-rules="required" :data-vv-as="subtitles.personData.name" class="form-control" v-model="ombudsman.ombudsmanUser.patientName">
                                        <require-text :error="errors.has('Ombudsman-ombudsmanUser-patientName')" :text="errors.first('Ombudsman-ombudsmanUser-patientName')"/>
                                    </rows>

                                    <rows :label="subtitles.personData.birthday">
                                        <input type="text" name="Ombudsman-ombudsmanUser-birthday" v-validate data-vv-rules="required" :data-vv-as="subtitles.personData.birthday" class="form-control" v-mask="'##/##/####'" v-model="ombudsman.ombudsmanUser.birthday">
                                        <require-text :error="errors.has('Ombudsman-ombudsmanUser-birthday')" :text="errors.first('Ombudsman-ombudsmanUser-birthday')"/>
                                    </rows>
                                    
                                    <rows :label="subtitles.personData.phoneNumber">
                                        <input type="text" name="Ombudsman-ombudsmanUser-phoneNumber" v-validate data-vv-rules="required" :data-vv-as="subtitles.personData.phoneNumber" v-mask="['(##) #####-####', '(##) ####-####']"  class="form-control" v-model="ombudsman.ombudsmanUser.phoneNumber">
                                        <require-text :error="errors.has('Ombudsman-ombudsmanUser-phoneNumber')" :text="errors.first('Ombudsman-ombudsmanUser-phoneNumber')"/>
                                    </rows>
                                </div>

                                <div class='row'>
                                    <rows :label="subtitles.personData.reporterName">
                                        <input type="text" name="Ombudsman-ombudsmanUser-reporterName" :data-vv-as="subtitles.personData.reporterName" class="form-control" v-model="ombudsman.ombudsmanUser.declarantName">
                                    </rows>

                                    <rows :label="subtitles.personData.reporterEmail">
                                        <input type="text" name="Ombudsman-ombudsmanUser-reporterEmail" v-validate data-vv-rules="email" :data-vv-as="subtitles.personData.reporterEmail" class="form-control" v-model="ombudsman.ombudsmanUser.email">
                                        <require-text :error="errors.has('Ombudsman-ombudsmanUser-reporterEmail')" :text="errors.first('Ombudsman-ombudsmanUser-reporterEmail')"/>
                                    </rows>
                                </div>

                            </div>
                        </div>

                        <div id="user-information-address" class="card border-secondary">
                            <div class="card-body">

                                <div>
                                    <h5 class="subtitle">{{subtitles.address.title}}</h5>
                                </div>
                                <hr>

                                <div class='row'>
                                    <rows :label="subtitles.address.city" class="col-md-3">
                                        <input type="text" name="Ombudsman-address-city" v-validate data-vv-rules="required" :data-vv-as="subtitles.address.city" class="form-control" v-model="address.city">
                                        <require-text :error="errors.has('Ombudsman-address-city')" :text="errors.first('Ombudsman-address-city')"/>
                                    </rows>

                                    <rows :label="subtitles.address.neighborhood" class="col-md-3">
                                        <input type="text" name="Ombudsman-address-neighborhood" v-validate data-vv-rules="required" :data-vv-as="subtitles.address.neighborhood" class="form-control" v-model="address.neighborhood">
                                        <require-text :error="errors.has('Ombudsman-address-neighborhood')" :text="errors.first('Ombudsman-address-neighborhood')"/>
                                    </rows>


                                    <rows :label="subtitles.address.street" class="col-md-2">
                                        <input type="text" name="Ombudsman-address-street" class="form-control" v-model="address.street">
                                    </rows>
                                    
                                    <rows :label="subtitles.address.number" class="col-md-2">
                                        <input type="text" name="Ombudsman-address-number" v-validate data-vv-rules="numeric" :data-vv-as="subtitles.address.number" class="form-control" v-model="address.number">
                                        <require-text :error="errors.has('Ombudsman-address-number')" :text="errors.first('Ombudsman-address-number')"/>
                                    </rows>

                                    <rows :label="subtitles.address.postCode">
                                        <input type="text" v-mask="'#####-###'" class="form-control" v-model="address.postCode">
                                    </rows>
                                </div>

                            </div>
                        </div>

                        <div class="row mt-3">
                            <div v-for="(type, index) in types" :key="index" class="col-md type">
                                <input type="radio" v-validate data-vv-rules="required" :data-vv-as="'Tipos de demanda'" :value="type.name" v-model="ombudsman.type" name="Ombudsman-type">
                                <label :for="index">{{ type.name }}</label>
                            </div>
                        </div>
                        <require-text :error="errors.has('Ombudsman-type')" :text="errors.first('Ombudsman-type')"/>
                    </section>
                    
                    <section :id="sections.ombudsmanPlace.id" >
                        <div id="user-informations-title">
                            <h4 class="title">{{ sections.ombudsmanPlace.label }}</h4>
                        </div>
                        
                        <div id="user-informations-person" class="card border-secondary mb-2" >
                                
                            <div class="card-body" v-if="JSON.stringify(ombudsman.origin) != '{}'">
                                
                                <row label='Leito' v-if="ombudsman.origin.id == 'INT'">
                                    <input type="text" name="Ombudsman-bed" v-validate data-vv-rules="required" data-vv-as="Leito" class="form-control" v-model="ombudsman.bed">
                                    <require-text :error="errors.has('Ombudsman-bed')" :text="errors.first('Ombudsman-bed')"/>
                                </row>

                                <row label='Grupo' v-else-if="ombudsman.origin.id == 'AMB'">
                                    <v-select v-model="ombudsman.group" name="Ombudsman-group" label="name" :options="values.groups"/>
                                </row>

                            </div>

                            <div class="card-body" v-else>
                                <p class="text-danger">
                                    Selecione o <b>Código</b> da Ouvidoria
                                </p>
                            </div>
                        </div>
                    </section>

                    <section :id="sections.demands.id" class="mb-3">
                        <div>
                            <h4 class="title">{{ sections.demands.label }}</h4>
                        </div>
                        <hr>

                        <row :label="sections.demands.label">
                            <v-select v-model="demandSelected" name="Ombudsman-demands" label="name" :options="values.demands" @input="insertDemands()"/>
                        </row>

                        <div class="card border-secondary mb-2">
                            <div class="card-body">
                                <div class="row">
                                    <div v-for="(demand, index) in ombudsman.demands" class="col-md-4" :key="demand.id">
                                        <p>
                                            <span>{{demand.name}}</span>
                                            <router-link to="" @click.native="removeDemand(demand, index)">
                                                <icon class="text-danger" icon="minus-circle"/>
                                            </router-link>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section :id="sections.report.id" class="mb-3">
                        <div>
                            <h4 class="title">{{ sections.report.label }}</h4>
                        </div>
                        <hr>

                        <div class="row mt-3">
                            <div v-for="(reporterOption, index) in reporterOptions" :key="index" class="col-md type">
                                <input type="radio" v-validate data-vv-rules="required" :data-vv-as="'Canal Recebimento'" :value="reporterOption.name" v-model="ombudsman.reportedBy" name="Ombudsman-reportedBy">
                                <label :for="index">{{ reporterOption.name }}</label>
                            </div>
                            <require-text :error="errors.has('Ombudsman-reportedBy')" :text="errors.first('Ombudsman-reportedBy')"/>
                        </div>
                        <div class="card border-secondary mb-2">
                            <div class="card-body">

                                <div id="report-description" class="mb-4">
                                    
                                    <div>
                                        <h5 class="subtitle">{{ subtitles.description.report }}:</h5>
                                    </div>
                                    <hr>

                                    <row>
                                        <textarea name="Ombudsman-ombudsmanUser-description" v-validate data-vv-rules="required" :data-vv-as="'Descrição'" class="form-control" cols="30" rows="4" v-model="ombudsman.ombudsmanUserDescription" placeholder="Descrição: "></textarea>
                                        <require-text :error="errors.has('Ombudsman-ombudsmanUser-description')" :text="errors.first('Ombudsman-ombudsmanUser-description')"/>
                                    </row>

                                </div>

                                <div id="report-sugestion">

                                    <div>
                                        <h5 class="subtitle">{{ subtitles.description.sugestion }}:</h5>
                                    </div>
                                    <hr>

                                    <row>
                                        <textarea name="Ombudsman-ombudsmanUser-sugestion" class="form-control" cols="30" rows="4" v-model="ombudsman.ombudsmanUserSugestion" placeholder="Sugestão: "></textarea>
                                    </row>

                                </div>

                            </div>
                        </div>
                    </section>

                    <section :id="sections.ombudsmanReport.id" class="mb-3">
                        <div>
                            <h4 class="title">{{ sections.ombudsmanReport.label }}</h4>
                        </div>
                        
                        <div class="card border-secondary">
                            <div class="card-body">

                                <div>
                                    <h5 class="subtitle">{{ subtitles.ombudsman.report }}:</h5>
                                </div>
                                <hr>

                                <row>
                                    <textarea name="Ombudsman-ombudsman-description" v-validate data-vv-rules="required" :data-vv-as="subtitles.ombudsman.report" class="form-control" cols="30" rows="4" v-model="ombudsman.ombudsmanDescription" placeholder="Informações do Ouvidor: "></textarea>
                                    <require-text :error="errors.has('Ombudsman-ombudsman-description')" :text="errors.first('Ombudsman-ombudsman-description')"/>
                                </row>                        
                                
                            </div>
                            
                            <signature label="Você está logado como" :username="ombudsman.ombudsman.name"/>
                        </div>

                        <div class="row mt-3">
                            <div v-for="(relevance, index) in relevances" :key="index" class="col-md type">
                                <input type="radio" v-validate data-vv-rules="required" :data-vv-as="'Relevância'" :value="relevance.name" v-model="ombudsman.relevance" name="Ombudsman-relevance">
                                <label :for="index">{{ relevance.name }}</label>
                            </div>
                            <require-text :error="errors.has('Ombudsman-relevance')" :text="errors.first('Ombudsman-relevance')"/>
                        </div>
                    </section>
                </div>

            </div>

        </div>

        <div class="navigation-bottom mt-3">
            <row>
                <button class="btn btn-outline-secondary btn-lg" id="submit-button" type="button" @click="isValidForm" :disabled="sending" v-if="ombudsman.id">
                    Registrar Ouvidoria
                </button>
                <router-link class="btn btn-outline-primary btn-lg" :to="{name: 'ouvidoria'}" tag="button" :disabled="sending">
                    Voltar
                </router-link>
            </row>
        </div>
    </div>
</template>

<script>
import model, { getter, DemandModel } from '@/model/ombudsman-model'
const GetterGroup = require('@/model/group-model').getter
import Ombudsman from "@/entity/Ombudsman";
import { FormRw, FormRws, Require, VueSelect } from "@/components/shared/Form";
import Alert from "@/components/shared/Alert"
import VFile from "@/components/shared/VFile/V-file-pdf"

export default {
    data() {
        return {
            id: this.$route.params.id,
            title: "Registro de Ouvidorias",
            ombudsmanId: '',
            ombudsman: new Ombudsman( ),
            demandSelected: '',
            sending: false,
            show: false,
            address: {},
            types: [
                { name: 'Denúncia', },
                { name: 'Solicitação', },
                { name: 'Reclamação', },
                { name: 'Sugestão', },
                { name: 'Elogio', },
                { name: 'Outros', },
            ],
            relevances: [
                { name: 'Baixa', },
                { name: 'Média', },
                { name: 'Alta', },
                { name: 'Urgente', },
            ],
            reporterOptions:[
                { name: 'Presencial' },
                { name: 'Telefone' },
                { name: 'E-Mail' },
            ],
            sections: {
                code: { id: "code", label: "Código"},
                userInformations: { id: "user-informations", label: "Dados do Relator"},
                ombudsmanPlace: { id: "ombudsman-place", label: "Local da Ouvidoria"},
                demands: { id: "demands", label: "Demandas"},
                report: { id: "report", label: "Relato"},
                ombudsmanReport: { id: "ombudsman-report", label: "Ouvidor"},
            },
            subtitles: {
                personData: { title: "Dados Pessoais", name: "Nome Paciente", birthday: "Data de Nascimento", phoneNumber: "Telefone", reporterName: "Nome Relatante", reporterEmail: "Email", },
                address: { title: "Endereço:", street: "Rua", number: "Nº", neighborhood: "Bairro", city: "Cidade", postCode: "CEP", },
                description: { report: "Descrição: (Relatar o ocorrido)", sugestion: "Sugestão/Solicitação", },
                ombudsman: { report: "Informação complementar do Ouvidor", },
            },
            values: {
                groups: [],
                ombudsmans: [],
                demands: [],
            },
        }
    },
    methods: {
        loadValues() {
            getter.getOmbudsmansWaiting().then(res => {
                if(res.length === 0) {
                    this.$alert.danger('Você não tem ouvidorias cadastradas')
                } else {
                    this.values.ombudsmans = res;

                }
            })
            getter.getDemands().then(res => { this.values.demands = res; })
            GetterGroup.getGroups().then(res => { this.values.groups = res; })
            if(this.isEdit()) {
                getter.getOmbudsmanById(this.id).then(res => {
                    this.ombudsman = new Ombudsman(res); 
                })
            } else {
                this.ombudsman.ombudsman = this.$session.get('user')
            }
        },
        loadOmbudsman() {
            if(this.ombudsmanId) {
                this.ombudsman.id = this.ombudsmanId.id
                this.ombudsman.origin = { id: this.ombudsmanId.origin }
            }
        },
        insertDemands() {
            if(this.demandSelected) {
                if(!this.demandExist) {
                    this.ombudsman.demands.push(this.demandSelected)
                } else {
                    Alert.Confirm(`Demanda "<i>${this.demandSelected.name}</i>" já cadastrada`)
                }
                
                setTimeout(() => { this.demandSelected = '' }, 100);
            }
        },
        uploadFile: model.uploadFile,
        removeDemand(demand, index) {
            this.ombudsman.demands.splice(index, '1')
        },
        isEdit() {
            return model.isEdit(this.id)
        },
        isValidForm() { this.$validator.validateAll().then(success => success? this.submit():"") },
        submit() {
            this.sending = true
            this.ombudsman.ombudsmanUser.address = this.userAddress

            model.doUpdate(this.ombudsman).then(res => { 
                this.sending = false
                this.$router.push({ name: "ouvidoria" })
            }).catch(err => {
                this.sending = false
            }) 
        },
    },
    computed: {
        gotPermission() {
            if(this.permission == 'undefined') {
                model.gotPermission(this.id).then(permission => { this.permission = permission } )
            } else {
                return (this.permission != 'USER' && this.report.id) ? true : false
            }
        },
        demandExist() {
            return DemandModel.demandExist(this.ombudsman.demands, this.demandSelected)
        },
        userAddress() {
            let address = `${this.address.city} - B. ${this.address.neighborhood}`
            if(this.address.street) {
                address += ` - ${this.address.street}`
            }
            if(this.address.number) {
                address += `, Nº ${this.address.number}`
            }
            if(this.address.postCode) {
                address += ` - CEP: ${this.address.postCode}`
            }
            
            return address
        },
        filePlace() {
            let id = false
            if(this.ombudsman.id) {
                id = `Ombudsman/${this.ombudsman.id}`
            }
            return id
        },
    },
    mounted() {
        this.loadValues()
    },
    components: {
        'row': FormRw,
        'rows': FormRws,
        'v-select': VueSelect,
        'require-text': Require,
        'import-file': VFile,
        'signature': require('@/components/shared/Signature.vue').default,
    },
}
</script>

<style lang="scss" scoped>
    .subtitle {
        text-align: left;
        font-size: 17px;
    }

    .watched {
        pointer-events:none;
        color:grey;
    }

    .navigation-bottom {
        position: absolute;
    }

    .page-container {
        display: flex;
        justify-content: center;

        .navigation {
            position: relative;
            width: 22rem;

            .slider-menu {
                width: 18rem;
                display: flex;
                justify-content: center;

                .menu-content {
                    position: fixed;
                }
            }
        }

    }
</style>
