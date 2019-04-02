<template>
    <div class="container-fluid">
        <!-- <div id="universal" v-bind:class="{ 'show':showUniversal && show.completList }">
            <div class="universal-container">
                <div class="img">
                    <img src="https://pngimage.net/wp-content/uploads/2018/06/logo-da-iurd-png-5.png">
                </div>
            </div>
        </div> -->
        <h1>{{ title }}</h1>

        <row :text_left="false">
            <router-link class="button btn btn-outline-primary btn-lg" :to="{name: 'documentos-eletronicos/add'}" tag="button">
                Criar Documento
            </router-link>            
        </row>

        <div class='row' v-show="loaded">
            <rows label=''>
                <div class="buttons">
                    <div class="state-buttons row mb-3">
                        <button class="state-button button btn btn-outline-clean btn-lg" v-bind:class="{ 'active': filter.inbox }" @click="filter.inbox = true">
                            Caixa de Entrada <icon icon="file-archive"/>
                        </button>
                        <button class="state-button button btn btn-outline-clean btn-lg" v-bind:class="{ 'active': !filter.inbox }" @click="filter.inbox = false">
                            Enviados <icon icon="file-archive"/>
                        </button>
                    </div>
                        
                    <hr>
                    
                    <div class="status-buttons row">
                        <button class="status-button button btn btn-outline-success btn-lg mb-3" v-bind:class="{ 'active': filter.status == 'finished' }" @click="filteringStatus('finished')">
                            Finalizados <icon icon="file-archive"/>
                        </button>
                        <button class="status-button button btn btn-outline-danger btn-lg mb-3" v-bind:class="{ 'active': filter.status == 'canceled' }" @click="filteringStatus('canceled')">
                            Revogados <icon icon="file-archive"/>
                        </button>
                        <button class="status-button button btn btn-outline-secondary btn-lg mb-3" v-bind:class="{ 'active': filter.status == 'archived' }" @click="filteringStatus('archived')">
                            Arquivados <icon icon="file-archive"/>
                        </button>
                    </div>
                </div>
            </rows>
            
            <rows label='' v-show="loaded">
                <button class="button btn btn-outline-dark btn-lg" v-bind:class="{ 'active': show.completList }" @click="show.completList = !show.completList; showUniversal = true" v-if="user.admin">
                    Lista Universal <icon icon="globe"/>
                </button>
            </rows>
        </div>
        
        <div class="form-group form-row col mt-3">
            <input type="search" class="filter form-control" :disabled="!documents" @input="filter.search = $event.target.value" placeholder="Pesquisa:"/>
        </div>

        <big-table field_length="180" class="table-hover">

            <thead>
                <tr>
                    <th scope="col">Protocolo</th>
                    <th scope="col">Assunto</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Criador</th>
                    <th scope="col">Status</th>
                    <th scope="col">Acompanhando</th>
                    <th scope="col">Data Criação</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <tr @click="showMore(document)" v-for="(document, index) in searchList" :key="document.id" class="row-list" :class="getColorStatus(document)">
                    <th>{{ document.id }}</th>
                    <td>{{ document.subject }}</td>
                    <td>{{ document.type.code }}</td>
                    <td>{{ document.user.name.substr(0, 10) }}...</td>
                    <td>{{ document.status.name }}</td>
                    <td>
                        <div v-for="signature in document.signatureList" :key="signature.id" v-if="signature.bc">
                            <div class="text-left"><icon icon="angle-double-right"/>
                                <i>{{ signature.user.name.substr(0, 10) }}...</i> - <b>A/C</b>
                            </div>
                        </div>
                    </td>
                    <td>{{ document.createdAt.date | humanizeDate }}</td>
                    <td>
                        <a href="" @click.stop.prevent="$router.push(`documentos-eletronicos/edit/${document.id}`)" v-if="showEditButton(document.status, document.user)">
                            <icon v-tooltip.top="'Editar'" icon="edit"/>
                        </a>
                        <a href="" @click.stop.prevent="$router.push(`documentos-eletronicos/detalhe/${document.id}`)" v-else>
                            <icon v-tooltip.top="'Detalhe'" class="text-warning" icon="search"/>
                        </a>
                        <a @click.stop.prevent="remove(document.id, index)" to='' v-if="showDeleteButton(document.status, document.user)">
                            <icon v-tooltip.top="'Excluir'" class="text-danger" icon="trash-alt"/>
                        </a>
                        <a @click.stop.prevent="archive(document, index)" to='' v-if="showArchiveButton(document)">
                            <icon v-tooltip.top="'Arquivar'" class="text-secondary" icon="archive"/>
                        </a>
                    </td>

                </tr>
                <tr class="row-list" v-if="searchList.length == 0 && loaded">
                    <td class="bold text-disabled" colspan="50"> Nenhum registro encontrado associado a você </td>
                </tr>
                
            </tbody>

        </big-table>
        
        <modal ref="modal" :title="`<b>Protocolo:</b> ${documentSelected.id}`">
            <show-more :document="documentSelected"/>
        </modal>
    </div>
</template>

<script>
import Alert from "@/components/shared/Alert";

import BigTable from "@/components/shared/BigTable";
import Modal from "@/components/shared/Modal";
import ShowMore from "./complements/ShowMore";
import model, { getter } from "@/model/eletronic-documents-model";

export default {
    data: () => ({
        title: "Documentos Eletrônicos",
        documents: [],
        documentsCompletList: [],
        documentSelected: false,
        filter: {
            search: '',
            status: '',
            inbox: true,
        },
        user: $session.get('user'),
        show: {
            archived: false,
            completList: false,
        },
        showUniversal: false,
        loaded: false,
    }),
    methods: {
        filteringStatus(status) {
            if( this.filter.status == status ) {
                this.filter.status = false
            } else {
                this.filter.status = status
            }
        },
        showMore(document) {
            this.documentSelected = document
            this.$refs.modal.show()
        },
        getColorStatus(document) {
            let colorClass = ''
            
            if(document.archived == true) {
                return 'table-disabled'
            }

            switch (document.status.id) {
                case 'draft':
                    colorClass = 'table-primary'
                    break;

                case 'finished':
                    colorClass = 'table-success'
                    break;
            
                case 'revoked': 
                case 'canceled': 
                    colorClass = 'table-danger'
                    break;

                default:
                    break;
            }

            return colorClass
        },
        showEditButton(status, user) {
            if(user.id != this.user.id) {
                return false
            } else if(status.id == 'draft' || status == '') {
                return true
            } else {
                return false
            }
        },
        showDeleteButton(status, user) {
            if(user.id != this.user.id) {
                return false
            } else if(status.id == 'draft' || status == '') {
                return true
            } else {
                return false
            }
        },
        showArchiveButton(document) {
            if(document.user.id != this.user.id || document.archived) {
                return false
            } else if(document.status.id == 'finished' 
                        || document.status.id == 'canceled'  
                        || document.status.id == 'revoked'
                    ) {
                return true
            } else {
                return false
            }
        },
        showDocumentIfArchived(document) {
            if( this.filter.status == 'archived' ) {
                if( document.archived == true ) {
                    return document
                }
            } else {
                if( document.archived == false ) {
                    return document
                }
            }
        },
        archive(document, index) {
            Alert.YesNo(`O Documento ${document.id} será Arquivado`, 'Tem certeza que deseja arquivar?').then( res => {
                if(res) {
                    model.setLikeArchived(document).catch(err => {
                        setTimeout(() => {
                            this.$router.go()
                        }, 5000);
                    })
                }
            })
        },
        remove(id, index) {
            Alert.YesNo(`O Documento ${id} será Excluído`, 'Tem certeza que deseja excluir?').then( res => {
                if(res) {
                    this.documents.splice( model.indexOf(id, this.documents), 1 )
                    model.doDelete(id).catch(err => {
                        setTimeout(() => {
                            this.$router.go()
                        }, 5000);
                    })
                }
            })
        }
    },
    computed: {
        secondFilter() {
            let documents = []
            if(this.show.completList) {
                documents = this.documentsCompletList
            } else {
                documents = this.documents
            }

            documents = documents.filter(document => {
                if( this.filter.inbox ) {
                    // Se inbox = true | eu não sou criador
                    if(document.user.id != this.user.id) {
                        return document
                    }
                } else {
                    // Se não, eu não sou criador
                    if(document.user.id == this.user.id) {
                        return document
                    }
                }
            })

            if( this.filter.status && this.filter.status != 'archived') {
                documents = documents.filter(document => {
                    switch (this.filter.status) {
                        case 'finished':
                            if(document.status.id == 'finished') {
                                return document
                            }
                            break;

                        case 'canceled':
                            if(document.status.id == 'canceled' || document.status.id == 'revoked') {
                                return document
                            }
                            break;
                    }
                })
            }

            if(this.filter.search) {
                let exp = new RegExp(this.filter.search.trim(), 'i')
                
                return documents.filter(document => {
                    if( exp.test(document.subject)) {
                        return exp
                    } else if( exp.test(document.id)) {
                        return exp
                    } else if( exp.test(document.type.code)) {
                        return exp
                    } else if( exp.test(document.user.name)) {
                        return exp
                    } else if( exp.test(document.status.name)) {
                        return exp  
                    }
                })
            } else {
                return documents
            }
            return []
        },
        searchList() {
            return this.secondFilter.filter(document => {
                return this.showDocumentIfArchived(document)
            })
        },
    },
    mounted() {
        getter.getEletronicDocuments().then(res => { 
            this.loaded = true
            this.documents = res

            if(this.user.admin) {
                getter.getEletronicDocuments('admin').then(res => { 
                    this.loaded = true
                    this.documentsCompletList = res
                })
            }
        })

    },
    watch: {
        showUniversal(val) {
            setTimeout(() => {
                this.showUniversal = false
            }, 200);
        }
    },
    components: {
        'big-table': BigTable,
        'modal': Modal,
        'show-more': ShowMore,
    }
}
</script>

<style lang="scss" scoped>
    .row-list:hover {
        cursor: pointer;
    }

    .status-buttons, .state-buttons {
        button {
            margin-left: 10px;
        }

        display: flex;
        justify-content: center;
    }

    // #universal {
    //     display: none;
    //     transition: opacity 1s ease-out;
    //     opacity: 0; 
    // }

    // #universal.show {
    //     background-color: rgba(1,1,1, 0.020);
    //     position: fixed;
    //     top: 0;
    //     right: 0;
    //     bottom: 0;
    //     left: 0;
    //     z-index: 1050;
    //     outline: 0;
        
    //     transition: opacity 1s ease-out;
    //     opacity: 1;
        
    //     display: block;
        
    //     animation: fadein 0.4s;
    //     animation: fadeout 0.6s;

    //     transition-delay: 0.6s 
    // }

    // #universal .universal-container {
    //     display: flex;
    //     align-items: center;
    //     justify-content: space-between;

    //     margin-left: 20%;
    //     margin-top: 25%;
    // }

</style>