<template>
    <vue-google-chart ref="chart" class="google-chart-component" type="PieChart" :data="chartData" :options="options"/>
</template>

<script>
import Root from "./RootChart.vue";
export default {
    extends: Root,
    data() {
        return {
            options: {
                title: this.title, 
                is3D: this.is3d,
                pieHole: this.hole,
            },
        }
    },
    props: {
        is3d: { type: Boolean, default: false },
        hole: { default: 0 },
    },
    computed: {
        dataExemple() {
            return [ 
                ['Year', 'Sales'],
                ['2014', 1000,],
                ['2015', 1170,],
                ['2016', 660, ],
                ['2017', 1030,] 
            ]
        },
    },
}
</script>
