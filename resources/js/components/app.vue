<template>
    <div class="container p-5 text-center">
        <div v-if="isEmpty" class="alert alert-danger" role="alert">
            Please do not leave empty fields!
        </div>
        <div v-if="isErrorDistance" class="alert alert-danger" role="alert">
            Wrong address
        </div>
        <div class="input-group input-group-lg">
            <div class="input-group-prepend" style="width: 15%; min-width: 130px">
                <span class="input-group-text w-100 text-center" id="inputGroup-sizing-lg">City start</span>
            </div>
            <input v-if="!isLoading" v-model="cityStart" type="text" class="form-control" style="width: 85%" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg">
            <input v-else disabled v-model="cityStart" type="text" class="form-control" style="width: 85%" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg">
        </div>
        <div class="input-group input-group-lg mt-4">
            <div class="input-group-prepend" style="width: 15%; min-width: 130px">
                <span class="input-group-text w-100 text-center" id="inputGroup-sizing-lg2">City end</span>
            </div>
            <input v-if="!isLoading" v-model="cityEnd" type="text" class="form-control" style="width: 85%" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg">
            <input v-else disabled v-model="cityEnd" type="text" class="form-control" style="width: 85%" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg">
        </div>
        <div class="dropdown mt-5 text-left" style="display: grid; grid-template-columns: 1fr 6fr" v-if="!isLoading">
            <button class="btn btn-danger dropdown-toggle" style="min-width: 130px" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Car model
            </button>
            <div class="form-control font-weight-bold" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-lg">
                {{ carType }}
            </div>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <a class="dropdown-item" v-for="(item, index) in cars"
                   @click="fillCarType(item.marke, item.modelis, item.kuroId, item.kuroSanaudos)">
                    {{ item.marke }} {{ item.modelis }}</a>
            </div>
        </div>
        <vue-content-loading v-else :width="400" :height="40">
            <rect y="21" width="400" height="40" />
        </vue-content-loading>
        <button @click="calculate()" type="button" class="btn btn-danger mt-5">Calculate trip cost</button>
        <div v-if="tripCost" class="card mt-4 w-50 text-center" style="margin: auto">
            <div class="card-body font-weight-bolder" style="font-size: 1.5em">
                Total fuel price: <div style="font-size: 1.6em; color: red"> {{ this.tripCost + '€' }} </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    import VueContentLoading from 'vue-content-loading';

    export default {
        name: 'app',
        props: [],
        components: {
            VueContentLoading

        },
        data() {
            return {
                isLoading: false,
                cars: [],
                fuels: [],
                carType: '',
                fuelId: null,
                cityStart: '',
                cityEnd: '',
                fuelExpenses: null,
                isEmpty: false,
                isErrorDistance: '',
                distanceBetweenCities: null,
                tripCost: null,
            }
        },
        computed: {

        },
        methods: {
            fillCarType(marke, modelis, kuroId, kuroSanaudos) {
                this.carType = marke + ' '+ modelis;
                this.fuelId = kuroId;
                this.fuelExpenses = parseFloat(kuroSanaudos);
            },
            async calculate() {
                this.isErrorDistance = '';
                if (!this.carType || !this.cityStart || !this.cityEnd) {
                    this.isEmpty = true;
                } else {
                    this.isEmpty = false;
                    this.isLoading = true;
                    const distanceApi = await axios.get('/api/get-distance-between-points', {
                        params: {
                            cityStart: this.cityStart,
                            cityEnd: this.cityEnd
                        }
                    });
                    this.isErrorDistance = distanceApi.data.isErrorDistance;
                    if (distanceApi.data.dist) {
                        this.distanceBetweenCities = distanceApi.data.dist.distance;
                        const carFuelInfo = await axios.get('/api/get-car-fuel-info', {
                            params: {
                                fuelId: this.fuelId
                            }
                        });
                        const fuelPrice = carFuelInfo.data.price;
                        let parsedDistance = this.distanceBetweenCities.substring(0, this.distanceBetweenCities.length-3);
                        let parsedDistanceCopy = parsedDistance;
                        let parsedTotal;

                        if (parsedDistance.length > 4) {
                            let thousands = parseFloat(parsedDistance.substring(0, 2));
                            thousands = thousands * 1000;
                            console.log(thousands);
                            let parsedUnits = parseFloat(parsedDistanceCopy.substr(parsedDistanceCopy.length-3));
                            console.log(parsedUnits);
                            parsedTotal = parsedUnits+thousands;
                        } else {
                            parsedTotal = parseFloat(this.distanceBetweenCities.substring(0, this.distanceBetweenCities.length-3));
                        }
                        console.log('galutinis', parsedTotal);
                        this.tripCost = (this.fuelExpenses / 100.00 * parsedTotal *
                            parseFloat(fuelPrice)).toFixed(2);
                    } else {
                        this.tripCost = null;
                    }
                    this.isLoading = false;
                }
            }

        },
        mounted () {

        },
        async created() {
            try {
                this.isLoading = true;
                const fuels = await axios.get(`/api/fuels`, {});
                const cars = await axios.get(`/api/cars`, {});

                this.cars = cars.data.cars;
                this.fuels = fuels.data.fuels;

                this.isLoading = false;
            } catch (error) {
                console.log(error);
            }
        }
    };
</script>
