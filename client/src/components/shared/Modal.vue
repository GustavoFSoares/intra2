<template>
    <div id="all-modal">
        <div class="modal fade" :id="id" tabindex="-1" role="dialog" :aria-labelledby="id" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle" v-html="title"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="close()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <slot>

                        </slot>
                    </div>
                    <div class="modal-footer">
                        <button @click="close()" type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" :disabled="disabled" @click="send">{{submitlabel}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>
<script>
import $ from "jquery";
export default {
    props: {
        title: String,
        submitlabel: {type: String, default: 'Ok'},
        disabled: {type: Boolean, default: false},
        submit_method: {type: Function},
        value: ''
    },
    data() {
        return {
            id: '',
        }
    },
    watch: {
        value(val) {
            if(val) {
                this.show()
            }
        }
    },
    methods: {
        send() {
            if( this.submit_method ) {
                this.submit_method().then(res => {
                    $(`#${this.id}`).modal('hide')
                    this.$emit('return')
                })
            } else {
                this.close()
            }
        },
        show() {
            $(`#${this.id}`).modal('show')
        },
        close() {
            $(`#${this.id}`).modal('hide')
            this.$emit('close')
            this.$emit('return')
            this.$emit('input', false)
        },
        destroy() {
            $(`#${this.id}`).remove()
            $('.modal-backdrop').remove()
            $('.modal-open').css('padding-right', 0).removeClass()
        }
    },
    created() {
        this.id = this._uid
    },
    mounted() {
        if(this.value) {
            this.show()
        }
    },
    beforeDestroy() {
        this.destroy()
    },
}
</script>
