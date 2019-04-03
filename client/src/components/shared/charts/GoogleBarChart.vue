<template>
    <vue-google-chart ref="chart" class="google-chart-component" :type="chartType" :data="chartData" :options="options"/>
</template>

<script>
import Root from "./RootChart.vue";
export default {
    extends: Root,
    data() {
        return {
            chartType: '',
            options: {
                title: this.title,
                vAxes: { 0: {title: this.vIndex }, },
                hAxes: { 0: {title: this.hIndex }, },
                isStacked: this.stacked,
                animation: {
                    duration: 1000,
                    easing: 'inAndOut',
                    startup: true,
                },
            },
        }
    },
    props: {
        stacked: { type: Boolean, default: false },
        collumn: { type: Boolean, default: false },
    },
    computed: {
        dataExemple() {
            return [
                ['City', '2010 Population', '2000 Population'], 
                ['New York City, NY', 8175000, 8008000],
                ['Los Angeles, CA', 3792000, 3694000],
                ['Chicago, IL', 2695000, 2896000],
                ['Houston, TX', 2099000, 1953000],
                ['Philadelphia, PA', 1526000, 1517000]
            ]
        }
    },
    created() {
        this.chartType = this.collumn ? 'ColumnChart' : 'BarChart'
    }
}
</script>
