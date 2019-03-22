<template>
    <vue-google-chart ref="chart" class="chart" type="LineChart" :data="chartData" :options="options"/>    
</template>

<script>
import Root from "./RootChart.vue";
export default {
    extends: Root,
    data() {
        return {
            chartData: null,
            options: {
                title: this.title,
                vAxes: { 0: {title: this.vIndex }, },
                hAxes: { 0: {title: this.hIndex }, },
                curveType: this.curveType ? 'function':'none'
            },
        }
    },
    props: {
        data: { type: Array, default: [ ] },
        title: { type: String, default: 'Titulo' },
        vIndex: { type: String, default: '' },
        hIndex: { type: String, default: 'H-Index' },
        curveType: { type: Boolean, default: false },
        congigurations: { type: Object },
        showExemple: { type: Boolean, default: false },
    },
    methods: {
        getChartOnHtml() {
            return this.$refs.chart.$el.innerHTML
        }
    },
    created() {
        Object.assign(this.options, this.congigurations)
        
        if(this.showExemple) {
            alert( "['Year', 'Sales', 'Expenses']\n ['2004',  1000,      400]\n ['2005',  1170,      460]\n ['2006',  660,       1120]\n ['2007',  1030,      540]" )
            this.chartData = [['Year', 'Sales', 'Expenses'], ['2004', 1000, 400], ['2005', 1170, 460], ['2006', 660,  1120], ['2007', 1030, 540]]
        } else {
            this.chartData = this.data
        }

    }
}
</script>
