<template>
    <vue-google-chart ref="chart" class="chart" type="Histogram" :data="chartData" :options="options"/>    
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
                histogram: { lastBucketPercentile: this.rage },
            },
        }
    },
    props: {
        data: { type: Array, default: [ ] },
        title: { type: String, default: 'Titulo' },
        vIndex: { type: String, default: '' },
        hIndex: { type: String, default: 'H-Index' },
        rage: { type: Number, default: 0 },
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
            alert( "['Dinosaur', 'Tamanho']\n ['Acrocanthosaurus (top-spined lizard)', 12.2, ]\n ['Albertosaurus (Alberta lizard)', 9.1, ]\n ['Allosaurus (other lizard)', 12.2, ]\n ['Apatosaurus (deceptive lizard)', 22.9, ]\n ['Archaeopteryx (ancient wing)', 0.9, ]\n ['Argentinosaurus (Argentina lizard)', 36.6, ]\n ['Baryonyx (heavy claws)', 9.1, ]\n ['Brachiosaurus (arm lizard)', 30.5, ]\n ['Ceratosaurus (horned lizard)', 6.1, ]\n ['Coelophysis (hollow form)', 2.7, ]\n ['Compsognathus (elegant jaw)', 0.9, ]\n ['Deinonychus (terrible claw)', 2.7, ]\n ['Diplodocus (double beam)', 27.1, ]\n ['Dromicelomimus (emu mimic)', 3.4, ]\n ['Gallimimus (fowl mimic)', 5.5, ]\n ['Mamenchisaurus (Mamenchi lizard)', 21.0, ]\n ['Megalosaurus (big lizard)', 7.9, ]\n ['Microvenator (small hunter)', 1.2, ]\n ['Ornithomimus (bird mimic)', 4.6, ]\n ['Oviraptor (egg robber)', 1.5, ]\n" )
            this.chartData = [ ['Dinosaur', 'Tamanho'], ['Acrocanthosaurus (top-spined lizard)', 12.2, ], ['Albertosaurus (Alberta lizard)', 9.1, ], ['Allosaurus (other lizard)', 12.2, ], ['Apatosaurus (deceptive lizard)', 22.9, ], ['Archaeopteryx (ancient wing)', 0.9, ], ['Argentinosaurus (Argentina lizard)', 36.6, ], ['Baryonyx (heavy claws)', 9.1, ], ['Brachiosaurus (arm lizard)', 30.5, ], ['Ceratosaurus (horned lizard)', 6.1, ], ['Coelophysis (hollow form)', 2.7, ], ['Compsognathus (elegant jaw)', 0.9, ], ['Deinonychus (terrible claw)', 2.7, ], ['Diplodocus (double beam)', 27.1, ], ['Dromicelomimus (emu mimic)', 3.4, ], ['Gallimimus (fowl mimic)', 5.5, ], ['Mamenchisaurus (Mamenchi lizard)', 21.0, ], ['Megalosaurus (big lizard)', 7.9, ], ['Microvenator (small hunter)', 1.2, ], ['Ornithomimus (bird mimic)', 4.6, ], ['Oviraptor (egg robber)', 1.5, ],]
        } else {
            this.chartData = this.data
        }
    }
}
</script>
