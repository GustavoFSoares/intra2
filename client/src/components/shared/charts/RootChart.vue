<script>
import { GChart } from 'vue-google-charts'

export default {
    components: {
        'vue-google-chart': GChart,
    },
    props: {
        value: { type: Array, default: () => [ ] },
        title: { type: String, default: 'Titulo' },
        vIndex: { type: String, default: '' },
        hIndex: { type: String, default: 'H-Index' },
        congigurations: { type: Object },
        exempleMode: { type: Boolean, default: false },
        alertData: { type: Boolean, default: false },
    },
    methods: {
        getChartOnHtml() {
            return this.$refs.chart.$el.innerHTML
        },
    },
    computed: {
        chartData() {
            if(this.exempleMode == true) {
                return this.dataExemple
            } else {
                console.log(this.value, 'aqui');
                
                if(this.value.length == 0) {
                    return [['', ''],[0,0]]
                }
                return this.value
            }
        }
    },
    created() {
        if(this.alertData == true) {
            alert("Acesse 'window.chartData'")
            
            window.chartData = this.chartData
        }
        Object.assign(this.options, this.congigurations)        
    }
}
</script>

<style lang="scss" scoped>
    .google-chart-component {
        width: 100%;
        height: 100%;
    }
</style>
