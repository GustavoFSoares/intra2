<template>
    <div class="chart-content" v-bind:class="{ 'show': display }" >
        <box class="chart-box">
            <div class="icon" @click.prevent="dismissChart()">
                <icon icon="times"/>
            </div>
            
            <div class="slot" @click="showChart()">
                <slot v-if="show">

                </slot>
            </div>
        </box>
    </div>
</template>

<script>
export default {
    data() {
        return { 
            show: true,
            display: false,
        }
    },
    props: {
        chartInContent: { type: Object },
        value: false,
    },
    watch: {
        display(val) {
            this.doResize()
        }
    },
    methods: {
        doResize() {
            this.show = false
            setTimeout(() => {
                this.show = true
            }, 0.1);
        },
        showChart() {
            if(this.display == false) {
                this.display = !this.display
                this.doResize()
            }
        },
        dismissChart() {
            this.display = false
            this.doResize()
        }
    },
}
</script>

<style lang="scss" scoped>
    .chart-content {
        &:not(.show) {
            .icon {
                display: none;
            }
            
            .slot {
                cursor: pointer;
            }
        }
        
        &.show {
            background-color: #ffffffe8;

            display: block;
        
            min-height: 30em;
            width: 100%;
            height: 100%;
            
            z-index: 2;
            top: 0;
            left: 0;

            position: absolute;
            padding: 0;
        }
        
        .icon {
            display: flex;
            justify-content: flex-end;
            opacity: 0.6;

            &:hover {
                cursor: pointer;
                opacity: 1;
            }
        }

        .chart-box {
            width: 100%;
            height: 100%;

            .slot {
                width: 100%;
                height: 100%;
            }
        }
    }
</style>

