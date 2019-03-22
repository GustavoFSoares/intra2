<template>
    <vue-google-chart ref="chart" class="chart" type="PieChart" :data="chartData" :options="options"/>    
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
                is3D: this.is3d,
                pieHole: this.hole,
            }
        }
    },
    props: {
        data: { type: Array, default: [ ] },
        title: { type: String, default: 'Titulo' },
        is3d: { type: Boolean, default: false },
        hole: { type: Number, default: 0 },
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
        if(this.showExemple || this.data.length == false) {
            alert( " ['Year', 'Sales'],\n ['2014', 1000,],\n ['2015', 1170,],\n ['2016', 660, ],\n ['2017', 1030,],\n" )
            this.chartData = [['Year', 'Sales'], ['2014', 1000,], ['2015', 1170,], ['2016', 660, ], ['2017', 1030,]]
        } else {
            this.chartData = this.data
        }

    }
}
</script>
