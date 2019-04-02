<template>
    <div v-show="error">
        <span class="text-danger" v-show="attribute === '' || show">
            {{text}}
        </span>
    </div>
</template>

<script>
export default {
    data() {
        return {

        }
    },
    props: {
        text: String,
        attribute: '',
        error: Boolean,
        show: { Boolean, default: true }
    }
}
</script>