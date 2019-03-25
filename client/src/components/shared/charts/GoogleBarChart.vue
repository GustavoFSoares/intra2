<template>
    <vue-google-chart ref="chart" class="chart" :type="chartType" :data="chartData" :options="options"/>
</template>

<script>
import Root from "./RootChart.vue";
export default {
    extends: Root,
    data() {
        return {
            chartType: '',
            chartData: null,
            options: {
                title: this.title,
                vAxes: { 0: {title: this.vIndex }, },
                hAxes: { 0: {title: this.hIndex }, },
                isStacked: this.stacked,
            },
        }
    },
    props: {
        data: { type: Array, default: [ ] },
        title: { type: String, default: 'Titulo' },
        vIndex: { type: String, default: '' },
        hIndex: { type: String, default: 'H-Index' },
        stacked: { type: Boolean, default: false },
        congigurations: { type: Object },
        showExemple: { type: Boolean, default: false },
        collumn: { type: Boolean, default: false },
    },
    methods: {
        getChartOnHtml() {
            return this.$refs.chart.$el.innerHTML
        }
    },
    created() {
        this.chartType = this.collumn ? 'ColumnChart' : 'BarChart'
        Object.assign(this.options, this.congigurations)
        
        if(this.showExemple || this.data.length == false) {
            alert( "[['City', '2010 Population', '2000 Population']\n ['New York City, NY', 8175000, 8008000]\n ['Los Angeles, CA', 3792000, 3694000]\n ['Chicago, IL', 2695000, 2896000]\n ['Houston, TX', 2099000, 1953000]\n ['Philadelphia, PA', 1526000, 1517000]]" )
            this.chartData = [['City', '2010 Population', '2000 Population'], ['New York City, NY', 8175000, 8008000], ['Los Angeles, CA', 3792000, 3694000], ['Chicago, IL', 2695000, 2896000], ['Houston, TX', 2099000, 1953000], ['Philadelphia, PA', 1526000, 1517000]]
        } else {
            this.chartData = this.data
        }

    }
}
</script>
