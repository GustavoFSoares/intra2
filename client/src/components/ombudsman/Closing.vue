<template>
    <div class="closing-ombudsman">
        <row label='Fechamento do Ouvidor' v-if="(status == 'waiting-manager' || status == 'registered') && gotAdminPermission">
            <textarea v-model="text" ref="textarea" class="form-control" cols="30" rows="4" @input="uploadValue()"/>
            <signature label="Ouvidor assinando" :username="ombudsmanClosingName"/>
        </row>
        <row v-else-if="value">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Relato do Ouvidor:</h6>
                    <p class="card-text">
                        {{ value }}
                    </p>
                </div>
            </div>
            <signature label="Assinado por" :username="ombudsmanClosingName"/>
        </row>
    </div>
</template>

<script>
import { FormRw, FormRws, Require, VueSelect } from "@/components/shared/Form";

export default {
    data() {
        return {
            text: null,
            ombudsman: this.$session.get('user')
        }
    },
    watch: {
        value(val) {
            this.text = val
        }
    },
    props: {
        value: { default: "" },
        status: { default: "" },
        ombudsmanClosingName: { default: "" },
        gotAdminPermission: { default: false },
    },
    methods: {
        uploadValue() {
            this.$emit('input', this.text)
        },
    },
    components: {
        'row': FormRw,
        'signature': require('@/components/shared/Signature.vue').default,
    }
}
</script>

<style lang="scss" scoped>
    .closing-ombudsman {
        position: relative;
    }
</style>

