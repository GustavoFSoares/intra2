<template>
    <div class="container">
        <div class='row'>
            <rows class="col-md-9" v-if="!only_exibition">
                <row>
                    <li v-for="(file) in files" :key="file.id">
                        <span>{{file.name}}</span> -
                        <span>{{file.size | formatSize}}</span>
                        <span v-if="file.error" class="text-danger">
                            <icon icon="times" size="lg"/>
                            <b>{{file.error}}</b>
                        </span>
                        <span v-else-if="file.success">
                            <icon class="text-success" icon="check" size="lg"/>
                        </span>
                        <span v-else-if="file.active">active</span>
                        <span v-else></span>
                    </li>
                    <div v-if="files.length == 0 && !filePath">
                        <span class="text-danger"> Sem documento </span>
                    </div>
                </row>
                
                <row>
                    <div>
                        <upload-component id="upload-component" class="btn btn-outline-primary" 
                            v-bind:class="{'disabled': !this.id || sending}"
                            @input-filter="inputFilter"
                            :custom-action="submitFile"
                            :chunk-enabled="true"
                            :multiple="false"
                            ref="upload" 
                            accept="application/pdf" 
                            v-model="files"> 
                            <icon icon="file"/> Selecionar Documento
                        </upload-component>
                        
                        <button class="btn btn-outline-success disable-pointer"
                            v-bind:class="{'disabled': !this.id}"
                            v-show="!$refs.upload || !$refs.upload.active" 
                            @click.prevent="$refs.upload.active = true" 
                            :disabled="sending"
                            type="button">
                            <icon icon="upload"/> Enviar 
                        </button>
                        <button  class="btn btn-outline-danger disable-pointer"
                            v-bind:class="{'disabled': !this.id}"
                            v-show="$refs.upload && $refs.upload.active" 
                            @click.prevent="$refs.upload.active = false; sending = false" 
                            type="button">
                            <icon icon="hand-paper"/> Parar Envio 
                        </button>
                        <icon v-if="sending" class="text-primary" icon="spinner" size="lg" spin/>
                    </div>       
                    <div v-if="!id" id="id-message">
                        <span class="text-danger"> Aguardando ID </span>
                    </div>
                </row>
            </rows>

            <rows>
                <a class="text-default" v-if="filePath && loadingFile == false" href="" @click.prevent="showExemple(filePath)">
                    <icon class="text-danger" icon="file-pdf" size="2x"/> {{ fileName }}
                </a>
                <div v-if="loadingFile">
                    <icon icon="spinner" size="2x" spin/>
                    <div class="text-disabled bold">Procurando Documento...</div>
                </div>
            </rows>
        </div>
  </div>
</template>

<script>
import service from '@/services/file'
import { FormRw, FormRws } from "@/components/shared/Form";

export default {
    data() {
        return {
            files: [],
            filePath: false,
            sending: false,
            loadingFile: false,
        }
    },
    props: {
        id: { default: false },
        file_name: { default: false },
        post_function: '',
        only_exibition: { default: false },
    },
    methods: {
        async submitFile(file, component) {
            this.sending = true
            let formData = new FormData()
            formData.append('file', file.file);
            
            this.post_function(formData, this.fileName).then(res => {
                this.sending = false
                
                this.filePath = res
                this.showExemple(this.filePath)
            }).catch(err => {
                this.$refs.upload.update(this.files[0], {error: 'Não Salvo!'})
                this.sending = false
            }) 
        },

        inputFilter(newFile, oldFile, prevent) {
            if (newFile && !oldFile) {
                if (!/\.(pdf)$/i.test(newFile.name)) {
                    newFile.error = "Formato não compatível"
                }
            }

            let URL = window.URL || window.webkitURL
            if (URL && URL.createObjectURL) {
                newFile.blob = URL.createObjectURL(newFile.file)
            }
        },
        showExemple(path) {
            let route = this.$router.resolve({ path: '/file/', query: { onScreen: true, filePath: path }});
            window.open(`${this.$globals.API_SERVER}/${route.href}`, '_blank')
        },
        findFile(id) {
            if(id) {
                this.loadingFile = true
                service.checkFileExist(id+".pdf").then( res => {
                    this.filePath = res
                    this.loadingFile = false    
                }).catch(err => {
                    this.loadingFile = false
                })
            }
        },
    },
    computed: { 
        fileName() {
            if(this.file_name) {
                return this.file_name
            } else if(this.$route.params.id) {
                return this.$route.params.id
            } else if(this.files[0].name) {
                return this.files[0].name
            } else {
                return 'File'
            }
        }
    },
    watch: {
        id() {
            (this.id) ? this.id+".pdf":false
            this.findFile(this.id) 
        },
    },
    components: {
        'upload-component': require('vue-upload-component'),
        'row': FormRw,
        'rows': FormRws,
    },
}
</script>

<style lang="scss" scoped>
    .disable-pointer:hover {
        cursor: context-menu;
    }

    #upload-component:disabled {
        pointer-events: none;
    }

    #id-message {
        text-align: left;
        margin-left: 100px;
    }
</style>
