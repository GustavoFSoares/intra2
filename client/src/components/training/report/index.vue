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
                            
                            <div class="chart">
                                <ChartContent>
                                    <GooglePieChart :exempleMode="true" />
                                </ChartContent>
                            </div>

                            <div class="chart">
                                <ChartContent>
                                    <GoogleLineChart :alertData="true" :data="data"/>                                    
                                </ChartContent>
                            </div>

                            <div class="chart">
                                <ChartContent>
                                    <GoogleBarChart :exempleMode="true" :data="[]"/>
                                </ChartContent>
                            </div>

                            <div class="chart">
                                <ChartContent>
                                    <GoogleHistogramChart :exempleMode="true"/>
                                </ChartContent>
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
import ChartContent from "@/components/shared/charts/ChartContent.vue";

export default {
    data() {
        return {
            title: 'Relatório Treinamentos',
            show: 0,
            data: [ ],
        }
    },
    methods: {
        getRandomInt() {
            let min = 1, max = 100
            min = Math.ceil(min);
            max = Math.floor(max);
            let ran = Math.floor(Math.random() * (max - min)) + min;
            return ran
        }
    },
    components: {
        GooglePieChart,
        GoogleHistogramChart,
        GoogleBarChart,
        GoogleLineChart,
        ChartContent,
    },
    mounted() {
        const service = require("@/services/client.js").default
        
        
        service.get('training/get-trainings/').then(res => {
            console.log(res);
            
            this.data = res.data
        }).catch(err => {
            console.log(err);
        })
    }
}
</script>

 <style lang="scss">
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
            width: 50%;
            height: 100%;
            padding: 7px;
        }
    }
</style>