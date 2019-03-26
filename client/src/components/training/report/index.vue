<template>
        <div>
            <h1>{{ title }}</h1>

            <div class="page-container">

                <div class="reports-available">
                    <box class="box">

                        <ul class="list-group">
                            <li class="list-group-item list-group-item-action">Estatístico</li>
                            <li class="list-group-item list-group-item-action">Quantitativo</li>
                        </ul>

                    </box>
                </div>

                <div class="report-content">
                    <box class="box">

                        <div class="charts">

                            <div class="chart" v-bind:class="{'show': show==1}" @click="show=1">
                                <box>
                                    <GooglePieChart :data="[]"/>
                                    
                                </box>
                            </div>

                            <div class="chart" v-bind:class="{'show': show==2}" @click="show=2">
                                <box>
                                    <GoogleHistogramChart :data="[]"/>
                                    
                                </box>
                            </div>

                            <div class="chart show" v-bind:class="{'show': show==3}" @click="show=3">
                                <box>
                                    <GoogleBarChart :data="[]"/>
                                    
                                </box>
                            </div>

                            <div class="chart" v-bind:class="{'show': show==4}" @click="show=4">
                                <box>
                                    <GoogleLineChart :data="[]"/>
                                    
                                </box>
                            </div>


                        </div>

                    </box>
                </div>

            </div>

         </div>
</template>

<script>
import GooglePieChart from "@/components/shared/charts/GooglePieChart.vue";
import GoogleHistogramChart from "@/components/shared/charts/GoogleHistogramChart.vue";
import GoogleBarChart from "@/components/shared/charts/GoogleBarChart.vue";
import GoogleLineChart from "@/components/shared/charts/GoogleLineChart.vue";

export default {
    data() {
        return {
            title: 'Relatório Treinamentos',
            show: 0,
        }
    },
    components: {
        GooglePieChart,
        GoogleHistogramChart,
        GoogleBarChart,
        GoogleLineChart,
    }
}
</script>

 <style lang="scss" scoped>
    .page-container {
        display: flex;
        justify-content: flex-start;

        .reports-available {
            
            flex-grow: 0;

            display: flex;
            justify-content: flex-start;
            
            widows: 100%;
            height: 100%;

            .box .card-body {
                padding-left: 0;
            }

            li {
                left: 0;

                font-size: 1.2rem;
            }
            
        }
        
        .report-content {
            margin-left: 15px;
            margin-right: 15px;
            
            flex-grow: 2;

            display: flex;
            justify-content: center;

            .box {
                width: 100%;
                height: 100%;

                background-color: #dedede2e;
            }
        }
    }

    .charts {
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;

        .chart {
            &:nth-child(even):has(.card) {
                padding-left: 7px;
            }
            &:nth-child(odd):has(.card) {
                padding-right: 7px;
            }
            width: 50%;
            
            // &.show {
            //     position: absolute;
            //     // padding: unset;

            //     z-index: 1;
            //     width: 100%;
            //     height: 100%;
                
            //     left: 0;
            //     margin-left: 20px;
            // }
        }

    }
</style>