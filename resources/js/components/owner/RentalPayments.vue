<template>
    <div>
        <div class="row">
            <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
                <div class="header">
                    <h1 class="text-center text-white">
                        Filter Rental Payments
                    </h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="property_uuid"><strong>Select Property</strong></label>
                                    <select v-model="filterForm.propertyUuid" @change="loadUnits(filterForm.propertyUuid)" id="property_uuid" class="form-control">
                                        <option :value="property.uuid" v-for="property in properties.data" :key="property.uuid">{{ property.name }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="unit_uuid"><strong>Select Unit</strong></label>
                                    <select v-model="filterForm.unitUuid" id="unit_uuid" class="form-control">
                                        <option :value="unit.uuid" v-for="unit in units.data" :key="unit.uuid">{{ unit.label }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="msisdn"><strong>Msisdn/ Phone</strong></label>
                                    <input v-model="filterForm.msisdn" id="msisdn" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <button v-if="filterForm.processing" class="btn btn-warning btn-block" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    </button>
                                    <button v-else @click.prevent="loadPayments()" type="button" class="btn btn-primary btn-block"><i class="fa fa-search"></i></button>
                                </div>
                                <div class="col-md-6">
                                    <button @click.prevent="clearFilter()" type="button" class="btn btn-danger btn-block">Clear</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-responsive table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Payer Name</th>
                                    <th>Msisdn/ phone</th>
                                    <th>Unit/ Room Number</th>
                                    <th>Rental Month</th>
                                    <th>Amount Payable (KES)</th>
                                    <th>Amount Paid (KES)</th>
                                    <th>Balance (KES)</th>
                                    <th>Description</th>
                                    <th>Payment Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="payment in payments.data" :key="payment.uuid">
                                    <td>
                                        <p>{{ payment.name }}</p>
                                    </td>
                                    <td>
                                        <p>{{ payment.msisdn }}</p>
                                    </td>
                                    <td>
                                        <p>{{ payment.unit.label }}</p>
                                    </td>
                                    <td>
                                        <p>{{ payment.rental_month }}</p>
                                    </td>
                                    <td>
                                        <p>{{ payment.amount_payable }}</p>
                                    </td>
                                    <td>
                                        <p>{{ payment.amount_paid }}</p>
                                    </td>
                                    <td>
                                        <p class="text-success" v-if="payment.balance < 0">{{ payment.balance }}</p>
                                        <p class="text-danger" v-else>{{ payment.balance }}</p>
                                    </td>
                                    <td>
                                        <p>{{ payment.description }}</p>
                                    </td>
                                    <td>
                                        <p class="text-success" v-if="payment.payment_status">PAID</p>
                                        <p class="text-danger" v-else>PENDING</p>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <Bootstrap4Pagination @limit=5 :data="payments" @pagination-change-page="loadPayments"/>
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
    name: "OwnerRentalPayments",
    components : {Bootstrap4Pagination},
    data() {
        return {
            properties: {},
            units: {},
            payments: {},
            filterForm: {
                propertyUuid: undefined,
                unitUuid: undefined,
                msisdn: undefined,
                processing: false
            }
        }
    },
    mounted() {
        this.loadPayments();
        this.loadProperties();
    },
    methods: {
        loadPayments(page = 1){
            const payLoad = {
                unit_uuid : this.filterForm.unitUuid,
                msisdn : this.filterForm.msisdn,
                sort_by : 'latest'
            };
            this.filterForm.processing = true;
            axios.post(`/owner/rental-payments/load?page=${page}`, payLoad).then(response => {
                this.payments = response.data;
            }).catch(error => {
                //
            }).finally(() => {
                this.filterForm.processing = false;
            });
        },
        loadProperties(page = 1){
            const payLoad = {
                sort_by : 'latest'
            };
            this.filterForm.processing = true;
            axios.post(`/owner/properties/load?page=${page}`, payLoad).then(response => {
                this.properties = response.data;
            }).catch(error => {
                //
            }).finally(() => {
                this.filterForm.processing = false;
            });
        },
        loadUnits(propertyUuid, page = 1){
            const payLoad = {
                sort_by : 'latest'
            };
            this.filterForm.processing = true;
            axios.post(`/owner/units/${propertyUuid}/load?page=${page}`, payLoad).then(response => {
                this.units = response.data;
            }).catch(error => {
                //
            }).finally(() => {
                this.filterForm.processing = false;
            });
        },
        clearFilter(){
            this.filterForm.propertyUuid = undefined;
            this.filterForm.unitUuid = undefined;
            this.filterForm.msisdn = undefined;
            this.loadPayments();
            this.loadProperties();
        }
    }
}

</script>
