<template>
    <div>
        <div class="row">
            <div class="col-lg-8 col-xl-8 col-md-8 col-sm-8 mx-auto">
                <div class="header">
                    <h1 class="header-title text-center">
                        Browse Properties/ Units
                    </h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="location"><strong>Location</strong></label>
                                    <input v-model="filterForm.location" id="location" type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="type"><strong>Select Type</strong></label>
                                    <select v-model="filterForm.type" id="type" class="form-control">
                                        <option :value="1">RESIDENTIAL</option>
                                        <option :value="2">COMMERCIAL</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="county_uuid"><strong>Select County</strong></label>
                                    <select v-model="filterForm.countyId" id="county_uuid" class="form-control">
                                        <option :value="county.id" v-for="county in counties.data" :key="county.uuid">{{ county.county }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="min_price"><strong>Min Price</strong></label>
                                    <input v-model="filterForm.min_price" id="min_price" type="number" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="max_price"><strong>Max Price</strong></label>
                                    <input v-model="filterForm.max_price" id="max_price" type="number" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="unit_type_id"><strong>Select Category</strong></label>
                                    <select v-model="filterForm.unitTypeId" id="unit_type_id" class="form-control">
                                        <option :value="unitType.id" v-for="unitType in unitTypes.data" :key="unitType.uuid">{{ unitType.type }}</option>
                                    </select>
                                </div>
                            </div>
                            <button v-if="filterForm.processing" class="btn btn-warning btn-block" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </button>
                            <button v-else @click.prevent="filterUnits()" type="button" class="btn btn-primary btn-block"><i class="fa fa-search"></i></button>
                            <button @click.prevent="clearFilter()" type="button" class="btn btn-danger btn-block">Clear</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
                <div class="header">
                    <h1>
                        Found ({{ units.total }}) Result(s) : Showing Page {{ units.current_page }} of {{ units.last_page }}
                    </h1>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4 col-xl-4 col-md-4 col-sm-4" v-for="unit in units.data" :key="unit.uuid">
                                        <div class="card">
                                            <img :src="unit.property.photo_url" class="card-img-top" alt="Image">
                                            <div class="card-body">
                                                <h5 class="card-title">Property Name : {{ unit.property.name }}</h5>
                                                <p class="card-text">{{ unit.property.location }}</p>
                                                <p class="card-text">County : {{ unit.property.county.county }}</p>
                                                <p class="card-text">Type : {{ unit.property.type === 1 ? 'Residential' : 'Commercial' }}</p>
                                                <p class="card-text">Unit Label/ Room Number {{ unit.label }}</p>
                                                <p class="card-text">Deposit (KES) : {{ unit.deposit }}</p>
                                                <p class="card-text">Rent (KES) : {{ unit.rent }}</p>
                                                <p class="card-text">Category : {{ unit.type.type }}</p>
                                                <a href="" class="btn btn-primary">Read More</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <Bootstrap4Pagination @limit=20 :data="units" @pagination-change-page="filterUnits"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Swal from "sweetalert2";
import swalWithBootstrapButtons from "sweetalert2";
import { Bootstrap4Pagination } from 'laravel-vue-pagination';

export default {
    name: "BrowseUnits",
    components : {Bootstrap4Pagination},
    data() {
        return {
            units: {},
            counties: {},
            unitTypes: {},
            filterForm: {
                location: undefined,
                type: undefined,
                countyId: undefined,
                min_price: undefined,
                max_price: undefined,
                unitTypeId: undefined,
                processing: false
            }
        }
    },
    mounted() {
        this.loadCounties();
        this.loadUnitTypes();
        this.filterUnits();
    },
    methods: {
        loadCounties(page = 1){
            const payLoad = {
                sort_by : 'latest'
            };
            this.filterForm.processing = true;
            axios.post(`/load-counties?page=${page}`, payLoad).then(response => {
                this.counties = response.data;
            }).catch(error => {
                //
            }).finally(() => {
                this.filterForm.processing = false;
            });
        },
        loadUnitTypes(page = 1){
            const payLoad = {
                sort_by : 'latest'
            };
            this.filterForm.processing = true;
            axios.post(`/load-unit-types?page=${page}`, payLoad).then(response => {
                this.unitTypes = response.data;
            }).catch(error => {
                //
            }).finally(() => {
                this.filterForm.processing = false;
            });
        },
        filterUnits(page = 1){
            const payLoad = {
                location : this.filterForm.location,
                type : this.filterForm.type,
                county_id : this.filterForm.countyId,
                min_price : this.filterForm.min_price,
                max_price : this.filterForm.max_price,
                unit_type_id : this.filterForm.unitTypeId,
                sort_by : 'latest'
            };
            this.filterForm.processing = true;
            axios.post(`/tenant/units/browse?page=${page}`, payLoad).then(response => {
                this.units = response.data;
            }).catch(error => {
                //
            }).finally(() => {
                this.filterForm.processing = false;
            });
        },
        clearFilter(){
            this.filterForm.location = undefined;
            this.filterForm.type = undefined;
            this.filterForm.countyId = undefined;
            this.filterForm.min_price = undefined;
            this.filterForm.max_price = undefined;
            this.filterForm.unitTypeId = undefined;
            this.loadCounties();
            this.loadUnitTypes();
            this.filterUnits();
        }
    }
}

</script>
