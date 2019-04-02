<template>
    <div>
        <div class='list-printers'>
            <div class="printer">
                <label><b>Imprimir Ouvidoria:</b></label>
                <div>
                    <router-link to="" @click.native="open('newFolder')">
                        <icon icon="print" size="3x"/>
                    </router-link>
                </div>
            </div>
            <div class="printer">
                <label><b>Re-imprimir:</b></label>
                <div>
                    <router-link to="" @click.native="open('olderOmbudsman')">
                        <icon icon="print" class="text-danger" size="3x"/>
                    </router-link>
                </div>
            </div>
        </div>
        <modal title="Impressão de Ouvidorias" ref="newFolder" @return="exportFile()">
            <div class='row pull-center'>
                <rows label='Origem de Ouvidoria' class="col-md-8">
                    <v-select :options="origin" label="name" v-model="doc.origin"/>
                </rows>

                <rows label="Quantidade de Páginas">
                    <div>
                        <router-link to="" id="icon" v-if="doc.page > 1" @click.native="doc.page--"> 
                            <icon  icon="minus"/>
                        </router-link>                        
                        
                        <span id="level">{{ doc.page }}</span>
                        
                        <router-link to="" id="icon" v-if="doc.page < 20" @click.native="doc.page++">
                            <icon icon="plus"/> 
                        </router-link>
                    </div>
                </rows>
            </div>
        </modal>
        <modal title="Ouvidorias Perdidas" ref="olderOmbudsman" @return="doPrint()">
            <row label='Ouvidoria Paradas'>
                <v-select :options="stopeds" label="id" v-model="ombudsman"/>
            </row>
        </modal>
    </div>
</template>

<script>
import { getter } from "@/model/ombudsman-model";
import { VueSelect, FormRws, FormRw } from "@/components/shared/Form";
export default {
    data() {
        return {
            origin: [],
            stopeds: [],
            ombudsman: '',
            doc: {
                page: '1',
                origin: '',
            }
        }
    },
    methods: {
        open(type) {
            if(type == 'newFolder') {
                this.$refs.newFolder.show()
            } else {
                this.$refs.olderOmbudsman.show()
            }
        },
        doPrint() {
            window.open(`${window.globals.API_SERVER}/ombudsman/doc/reprint/?id=${this.ombudsman.id}&user_id=${this.$session.get('user').id}`, '_target')
        },
        exportFile() {
            window.open(`${window.globals.API_SERVER}/ombudsman/doc/?page=${this.doc.page}&origin=${this.doc.origin.id}`, '_target')
        }
    },
    mounted() {
        getter.getOrigins().then(res => { this.origin = res })
        getter.getOmbudsmansWaiting().then(res => { this.stopeds = res })
    },
    components: {
        'modal': require("@/components/shared/Modal.vue").default,
        'v-select': VueSelect,
        'rows': FormRws,
        'row': FormRw,
    }
}
</script>

<style lang="scss" scoped>
    .list-printers {
        display: flex;
        justify-content: center;

        .printer {
            padding: 15px;
        }
    }
</style>

