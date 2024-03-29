<template>
    <div class="container-fluid">

        <div id="revoked" v-if="document.isLoaded() && this.document.isBlocked()">
            <hr>
            <h1>{{ document.status.name }}</h1>
            <hr>
        </div>

        <h1>{{ title }}</h1>

        <actual-status :actualStatusId="document.status" :documentId="id"/>
        <section>
            <text-exibitor :document="document"/>            

            <box class="border-secondary">
                <div class="attachment">
                    <h4 class="text">Documentos Anexos:</h4>
                    <v-multifile-pdf class="file" :only_exibition="true" :id="document.id" origin='eletronic-documents'/>
                </div>
            </box>
        </section>

        <section id="signature-area">
            <sign-document @signed="setNewStatus" :id="id" v-model="document" :disabled="!document.status || document.isBlocked()" title="Assinar Documento" ref="sign_document" :show="canShowSignature"/>
        </section>

        <section v-if="document.isLoaded() && document.user.id == $session.get('user').id">
            <div class='text-right'>
                <button class="btn btn-outline-danger btn-lg" @click="cancelDocument()" :disabled="loading.cancelingDocument" v-if="document.isInProssessing()">
                    Cancelar Documento 
                    <sending-icon v-model="loading.cancelingDocument"/>
                </button>
                <button class="btn btn-outline-success btn-lg" id="submit-button" type="button" v-if="document.finished" @click="printDocument()">
                    <icon icon="print"/>
                    Imprimir Documento
                    <sending-icon v-model="loading.printingDocument"/>
                </button>
            </div>
        </section>

        <section id="buttons">
            <row>
                <router-link class="btn btn-outline-primary btn-lg" :to="{name: 'documentos-eletronicos'}" tag="button">
                    Voltar
                </router-link>
            </row>
        </section>
    </div>
</template>

<script>
import ActualStatus from "./complements/ActualStatus"
import SignDocument from './complements/SignDocument.vue'
import SignatureForm from './complements/SignatureForm.vue'
import TextExibitor from "./complements/TextExibitor";

import Alert from "@/components/shared/Alert"
import VmfilePdf from '@/components/shared/VFile/V-multifile-pdf.vue'

import EletronicDocument from "@/entity/EletronicDocuments";
import model, { getter } from "@/model/eletronic-documents-model"
export default {
    data() {
        return {
            title: "Assinar Documento",
            id: this.$route.params.id,
            document: new EletronicDocument(),
            canShowSignature: false,
            loading: {
                cancelingDocument: false,
                filingDocument: false,
                printingDocument: false
            },
        }
    },
    methods: {
        loadValues() {
            if(this.id) {
                getter.getEletronicDocumentById(this.id).then( res => { 
                    
                    if(res == null) {
                        this.$alert.info("Você não ter permissão para ver este documento ou ele não existe")
                    } else {
                        this.document = new EletronicDocument(res); 
                        this.title = this.document.type.name
                        this.canShowSignature = true
                        
                        if(this.document.status.id == 'sending') {
                            this.setLikeWaitingSignature()
                        }
                    }
                    
                })
            }
        },
        updateSignature(data) {
            let signature = this.document.signatureList.find(signature => {
                return signature.user.id == data.id
            })
            signature.signed = data.signed;
            signature.status = data.status;
        },
        setLikeWaitingSignature() {
            //Se próximo Usuário == Usuário da Sessão
            getter.getNextUserToSign(this.id).then(res => {
                if( res != null && res.id == this.$session.get('user').id) {
                    //Setar Documento como "Aguardando Assinatura"
                    model.setLikeWaitingSignature(this.id).then(res => {
                        this.setNewStatus(res)
                    })
                }
                
            })
        },
        cancelDocument() {
            Alert.YesNo("Tem certeza?", "Você está invalidando este documento e ele será bloqueado permanentemente!").then(res => {
                if(res) {
            
                    this.loading.cancelingDocument = true
                    model.setLikeCanceled(this.document.id).then(res => {
                        this.document.status = res
                        this.loading.cancelingDocument = false
                    }).catch(err => {
                        this.loading.cancelingDocument = false
                    })

                }
            })
        },
        printDocument() {
            model.printDocument(this.document.id).then(res => {
                this.$alert.success('Imprimindo Documento...')
                this.loading.printingDocument = false
            }).catch(err => {
                this.loading.printingDocument = false                
            })
        },
        setNewStatus(status) {
            this.document.status = status
        }
    },
    components: {
        'v-multifile-pdf': VmfilePdf,
        'signature-form': SignatureForm,
        'sign-document': SignDocument,
        'actual-status': ActualStatus,
        'text-exibitor': TextExibitor,
    },
    mounted() {
        this.loadValues()
    },
}
</script>

<style lang="scss" scoped>
    #content {
        margin-right: 3%;
        margin-left: 3%;
    }

    .date {
        text-align: right;
        margin-right: 20px;
        margin-top: -15px;
    }

    #revoked {
        display: block;
        position: absolute;
        color: var(--danger);
        transform: translate(-2em, 17.5251em) rotate(-45deg);
        transform-origin: 0 0;
        width: 30em;

        line-height: 1;
        padding: 1em 0;
        z-index: -1;
        
        left: 0;
        top: 0;
        
        h1 {
            font-size: 60px;
            text-align: left;
            margin-left: 1.0em;
        }

        hr {
            border-top: 2px solid var(--danger);
        }

    }
    
    .attachment {
        display: flex;
        justify-content: space-between;

        .file {
            display: flex;
            justify-content: flex-end;
        }
    }

</style>

