<template>
    <vue-google-chart ref="chart" class="google-chart-component" type="Histogram" :data="chartData" :options="options"/>    
</template>

<script>
import Root from "./RootChart.vue";
export default {
    extends: Root,
    data() {
        return {
            options: {
                histogram: { lastBucketPercentile: this.rage },
            },
        }
    },
    props: {
        rage: { type: Number, default: 0 },
    },
    
    computed: {
        dataExemple() {
            return [ 
                ['Dinosaur', 'Tamanho'], 
                ['Acrocanthosaurus (top-spined lizard)', 12.2, ], 
                ['Albertosaurus (Alberta lizard)', 9.1, ], 
                ['Allosaurus (other lizard)', 12.2, ], 
                ['Apatosaurus (deceptive lizard)', 22.9, ], 
                ['Archaeopteryx (ancient wing)', 0.9, ], 
                ['Argentinosaurus (Argentina lizard)', 36.6, ], 
                ['Baryonyx (heavy claws)', 9.1, ], 
                ['Brachiosaurus (arm lizard)', 30.5, ], 
                ['Ceratosaurus (horned lizard)', 6.1, ], 
                ['Coelophysis (hollow form)', 2.7, ], 
                ['Compsognathus (elegant jaw)', 0.9, ], 
                ['Deinonychus (terrible claw)', 2.7, ], 
                ['Diplodocus (double beam)', 27.1, ], 
                ['Dromicelomimus (emu mimic)', 3.4, ], 
                ['Gallimimus (fowl mimic)', 5.5, ], 
                ['Mamenchisaurus (Mamenchi lizard)', 21.0, ], 
                ['Megalosaurus (big lizard)', 7.9, ], 
                ['Microvenator (small hunter)', 1.2, ], 
                ['Ornithomimus (bird mimic)', 4.6, ], 
                ['Oviraptor (egg robber)', 1.5, ],
            ]
        }
    },
}
</script>
