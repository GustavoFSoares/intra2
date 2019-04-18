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
                curveType: this.curveType ? 'function':'none',
                animation: {
                    duration: 1000,
                    easing: 'in',
                    startup: true,
                },
            },
        }
    },
    props: {
        curveType: { type: Boolean, default: false },
        area: { type: Boolean, default: false },
    },
    computed: {
        dataExemple() {
            return [ 
                ['Year', 'Sales', 'Expenses'], 
                ["2018", 11, 1064],
                ["2019", 1, 1290],
                // ['2014', 6, 5], 
                // // ['2005', 1170, 460], 
                // // ['2006', 660,  1120], 
                // // ['2007', 1030, 540]
                // ["2018",7,2],["2019",7,14],["2019",7,3]
            ] 
        }
    },
    created() {
        this.chartType = this.area ? 'AreaChart' : 'LineChart'
    }
}
</script>
