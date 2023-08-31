<template>
    <div>
        <div class="row">
            <div class="col-lg-8 col-xl-8 col-md-8 col-sm-8 mx-auto">
                <div class="header">
                    <h1 class="header-title text-center">
                        Filter My Residences
                    </h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="label"><strong>Unit Label/ Room Number</strong></label>
                                    <input v-model="filterForm.label" id="label" type="text" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="account_no"><strong>Account Number</strong></label>
                                    <input v-model="filterForm.accountNo" id="account_no" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <button v-if="filterForm.processing" class="btn btn-warning btn-block" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    </button>
                                    <button v-else @click.prevent="loadTenants()" type="button" class="btn btn-primary btn-block"><i class="fa fa-search"></i></button>
                                </div>
                                <div class="col-md-6">
                                    <button @click.prevent="clearFilter()" type="button" class="btn btn-danger btn-block">Clear</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
                <div class="header">
                    <h1>
                        My Residences
                    </h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-responsive table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Tenant Name</th>
                                    <th>Msisdn/ phone</th>
                                    <th>Email</th>
                                    <th>Unit/ Room Number</th>
                                    <th>Account No</th>
                                    <th>No Of Occupants</th>
                                    <th>Check In</th>
                                    <th>State on Checking In</th>
                                    <th>Tenant Agreement Document</th>
                                    <th colspan="1">Action (Confirm Tenant Reservation, Pay Rent, Issue Vacation Notice)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="tenant in tenants.data" :key="tenant.uuid">
                                    <td>
                                        <p>{{ tenant.name }}</p>
                                    </td>
                                    <td>
                                        <p>{{ tenant.msisdn }}</p>
                                    </td>
                                    <td>
                                        <p>{{ tenant.email }}</p>
                                    </td>
                                    <td>
                                        <p>{{ tenant.unit.label }}</p>
                                    </td>
                                    <td>
                                        <p>{{ tenant.unit.account_no }}</p>
                                    </td>
                                    <td>
                                        <p>{{ tenant.no_of_occupants }}</p>
                                    </td>
                                    <td>
                                        <p>{{ tenant.check_in }}</p>
                                    </td>
                                    <td>
                                        <p>{{ tenant.state_on_check_in }}</p>
                                    </td>
                                    <td>
                                        <a target="_blank" :href="tenant.tenant_agreement_doc_url">view file</a>
                                    </td>
                                    <td v-if="tenant.check_in_confirmation === 1">
                                        <td v-if="tenant.status === 1">
                                            <div v-if="tenant.check_out_status === 1 || tenant.check_out_status === 2 || tenant.check_out_status === 3">
                                                <p class="text-warning" v-if="tenant.check_out_status === 1">Vacation request submitted</p>
                                                <p class="text-success" v-else-if="tenant.check_out_status === 2">Vacation request approved</p>
                                                <p class="text-success" v-else>Checked out</p>
                                            </div>
                                            <div v-else>
                                                <button @click.prevent="showPayRentViaMpesaModal(tenant)" class="btn btn-success"><i class="fa fa-credit-card"></i> Pay Rent (M-PESA)</button>
                                                <button @click.prevent="showIssueVacationNotice(tenant)" class="btn btn-success mt-3"><i class="fa fa-shipping-fast"></i> Issue Vacation Notice</button>
                                            </div>
                                        </td>
                                        <td v-else>
                                            <p class="text-warning">Checked Out</p>
                                        </td>
                                    </td>
                                    <td v-else>
                                        <div v-if="tenant.check_out_status === 1 || tenant.check_out_status === 2 || tenant.check_out_status === 3">

                                        </div>
                                        <div v-else>
                                            <button @click.prevent="confirmCheckInRequest(tenant)" class="btn btn-success"><i class="fa fa-check-circle"></i> Confirm Check In Request</button>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <Bootstrap4Pagination @limit=5 :data="tenants" @pagination-change-page="loadTenants"/>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pay Rent Via Mpesa Modal -->
        <div class="modal fade" id="payRentViaMpesaModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Pay Rent Via M-pesa</h4>
                        <img height="120" width="200" :src="'/ui-kit/img/brands/mpesa.png'" alt="Mpesa logo">
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <form>
                                    <div class="form-group row">
                                        <div class="col-md-12">
                                            <label for="account_no"><strong>Account Number</strong></label>
                                            <input v-model="payRentForm.accountNo" id="account_no" disabled type="text" class="form-control">
                                        </div>
                                        <div class="col-md-12">
                                            <label for="amount"><strong>Amount (KES)</strong></label>
                                            <input v-model="payRentForm.amount" id="amount" disabled type="text" class="form-control">
                                        </div>
                                        <div class="col-md-12">
                                            <label for="msisdn"><strong>Msisdn/ Phone</strong></label>
                                            <input v-model="payRentForm.msisdn" id="msisdn" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <button v-if="payRentForm.processing" class="btn btn-warning btn-block" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Processing...
                                    </button>
                                    <button v-else @click.prevent="payRentViaMpesa()" type="button" class="btn btn-success btn-block">Pay Now</button>
                                </form>
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
    name: "MyResidences",
    components : {Bootstrap4Pagination},
    data() {
        return {
            tenants: {},
            filterForm: {
                label: undefined,
                accountNo: undefined,
                processing: false
            },
            payRentForm: {
                tenantUuid: undefined,
                accountNo: undefined,
                msisdn: undefined,
                amount: undefined,
                processing: false
            }
        }
    },
    mounted() {
        this.loadTenants();
    },
    methods: {
        loadTenants(page = 1){
            const payLoad = {
                label : this.filterForm.label,
                account_no : this.filterForm.accountNo,
                sort_by : 'latest'
            };
            this.filterForm.processing = true;
            axios.post(`/tenant/units/my-residences/load?page=${page}`, payLoad).then(response => {
                this.tenants = response.data;
            }).catch(error => {
                //
            }).finally(() => {
                this.filterForm.processing = false;
            });
        },
        showPayRentViaMpesaModal(tenant){
            this.payRentForm.tenantUuid = tenant.uuid;
            this.payRentForm.accountNo = tenant.unit.account_no;
            this.payRentForm.msisdn = tenant.msisdn;
            this.payRentForm.amount = tenant.unit.rent;
            $('#payRentViaMpesaModal').modal('show');
        },
        payRentViaMpesa(){
            const payLoad = {
                msisdn: this.payRentForm.msisdn
            };
            this.payRentForm.processing = true;
            axios.post(`/tenant/units/pay-rent-via-mpesa/${this.payRentForm.tenantUuid}`, payLoad).then((response) => {
                if (response.data.status){
                    $('#payRentViaMpesaModal').modal('hide');
                    Swal.fire('Success', response.data.message, 'success');
                } else {
                    Swal.fire('Error', response.data.message, 'warning');
                }
                this.loadTenants();
            }).catch((error) => {
                if (error.response && error.response.status === 422) {
                    const errorValues = Object.values(error.response.data.errors).flatMap(arr => arr);
                    Swal.fire('Error!', JSON.stringify(errorValues.map(errorMessage => errorMessage)), 'error');
                } else {
                    Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                }
            }).finally(() => {
                this.payRentForm.processing = false;
            });
        },
        showIssueVacationNotice(tenant){
            Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, issue vacation notice'
            }).then((result) => {
                if (result.value) {
                    this.filterForm.processing = true;
                    axios.post(`/tenant/units/issue-vacation-notice/${tenant.uuid}`).then((response) => {
                        if (response.data.status){
                            Swal.fire('Success', response.data.message, 'success');
                        } else {
                            Swal.fire('Error', response.data.message, 'warning');
                        }
                        this.loadTenants();
                    }).catch((error) => {
                        if (error.response && error.response.status === 422) {
                            const errorValues = Object.values(error.response.data.errors).flatMap(arr => arr);
                            Swal.fire('Error!', JSON.stringify(errorValues.map(errorMessage => errorMessage)), 'error');
                        } else {
                            Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                        }
                    }).finally(() => {
                        this.filterForm.processing = false;
                    });
                } else if (result.dismiss === 'cancel') {
                    Swal.fire('Cancelled', 'Your tenant vacation notice request was cancelled :)', 'warning');
                }
            });
        },
        confirmCheckInRequest(tenant){
            Swal.fire({
                title: 'Are you sure?',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, confirm'
            }).then((result) => {
                if (result.value) {
                    this.filterForm.processing = true;
                    axios.post(`/tenant/units/confirm-checkin-request/${tenant.uuid}`).then((response) => {
                        if (response.data.status){
                            Swal.fire('Success', response.data.message, 'success');
                        } else {
                            Swal.fire('Error', response.data.message, 'warning');
                        }
                        this.loadTenants();
                    }).catch((error) => {
                        if (error.response && error.response.status === 422) {
                            const errorValues = Object.values(error.response.data.errors).flatMap(arr => arr);
                            Swal.fire('Error!', JSON.stringify(errorValues.map(errorMessage => errorMessage)), 'error');
                        } else {
                            Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                        }
                    }).finally(() => {
                        this.filterForm.processing = false;
                    });
                } else if (result.dismiss === 'cancel') {
                    Swal.fire('Cancelled', 'Your tenant reservation confirmation is still pending :)', 'warning');
                }
            });
        },
        clearFilter(){
            this.filterForm.label = undefined;
            this.filterForm.accountNo = undefined;
            this.loadTenants();
        }
    }
}

</script>
