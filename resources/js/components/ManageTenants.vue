<template>
    <div>
        <div class="row">
            <div class="col-lg-8 col-xl-8 col-md-8 col-sm-8 mx-auto">
                <div class="header">
                    <h1 class="header-title text-center">
                        Create Tenant
                    </h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form enctype="multipart/form-data">
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <p class="text-warning">Please note that;</p>
                                    <ol>
                                        <li class="text-success">Tenant MUST have paid either deposit or both rent and deposit for first month before assigning them a unit/ house.</li>
                                        <li class="text-success">Tenants should provide a phone number that is constant to all units/ houses he/she has ever resided so as to easily manage all their residences from a central point in their portal dashboard.</li>
                                        <li class="text-success">After assigning a unit/ house to a tenant, an SMS containing the login credentials will be sent to the newly created tenant.</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="property_uuid"><strong>Select Property</strong></label>
                                    <select v-model="createForm.propertyUuid" @change="loadUnits(createForm.propertyUuid)" id="property_uuid" class="form-control">
                                        <option :value="property.uuid" v-for="property in properties.data" :key="property.uuid">{{ property.name }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="unit_uuid"><strong>Select Unit</strong></label>
                                    <select v-model="createForm.unitUuid" id="unit_uuid" class="form-control">
                                        <option :value="unit.uuid" v-for="unit in units.data" :key="unit.uuid">{{ unit.label }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="no_of_occupants"><strong>Number Of Occupants</strong></label>
                                    <input v-model="createForm.noOfOccupants" id="no_of_occupants" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="name"><strong>Full Name</strong></label>
                                    <input v-model="createForm.name" id="name" type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="msisdn"><strong>Msisdn/ Phone</strong></label>
                                    <input v-model="createForm.msisdn" id="msisdn" type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="email"><strong>Email</strong></label>
                                    <input v-model="createForm.email" id="email" type="email" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="national_id_no"><strong>National ID/ Passport No</strong></label>
                                    <input v-model="createForm.nationalIdNo" id="national_id_no" type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="gender"><strong>Gender</strong></label>
                                    <select v-model="createForm.gender" id="gender" class="form-control">
                                        <option :value="1">MALE</option>
                                        <option :value="2">FEMALE</option>
                                        <option :value="3">ANONYMOUS</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="rental_payment_channel"><strong>First Rental Payment Channel</strong></label>
                                    <select v-model="createForm.rentalPaymentChannel" id="rental_payment_channel" class="form-control">
                                        <option :value="1">DEPOSIT</option>
                                        <option :value="2">DEPOSIT & RENT</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label for="state_on_check_in"><strong>Unit/ House Status On Checking In</strong></label>
                                    <textarea v-model="createForm.stateOnCheckIn" id="state_on_check_in" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label for="tenant_agreement_doc_url"><strong>Tenant Agreement Document</strong></label>
                                    <input id="tenant_agreement_doc_url" type="file" v-on:change="onFileChange($event)" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <p class="text-warning">Note that these details can't be updated after being saved. To do so you will have to contact the system admin so kindly double check before saving record</p>
                                </div>
                            </div>
                            <button v-if="createForm.processing" class="btn btn-warning btn-block" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </button>
                            <button v-else @click.prevent="createTenant()" type="button" class="btn btn-primary btn-block">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
                <div class="header">
                    <h1>
                        My Tenants
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
                                    <label for="name"><strong>Full Name</strong></label>
                                    <input v-model="filterForm.name" id="name" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="msisdn"><strong>Msisdn/ Phone</strong></label>
                                    <input v-model="filterForm.msisdn" id="msisdn" type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="email"><strong>Email</strong></label>
                                    <input v-model="filterForm.email" id="email" type="email" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="status"><strong>Residency Status</strong></label>
                                    <select v-model="filterForm.status" id="status" class="form-control">
                                        <option :value="1">CHECKED IN</option>
                                        <option :value="2">CHECKED OUT</option>
                                    </select>
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
                                    <th colspan="4">Action (Manage Residency, Create Bills, View Bills)</th>
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
                                    <td v-if="tenant.check_out_status === 1">
                                        <p class="text-danger">Tenant Checked In</p>                                    </td>
                                    <td v-else-if="tenant.check_out_status === 2">
                                        <button @click.prevent="approveTenantCheckOutRequest(tenant)" class="btn btn-success"><i class="fa fa-check-circle"></i> Approve Check Out</button>
                                    </td>
                                    <td v-else-if="tenant.check_out_status === 3">
                                        <button @click.prevent="showTenantCheckOutModal(tenant)" class="btn btn-success"><i class="fa fa-check-circle"></i> Check Out Tenant</button>
                                    </td>
                                    <td v-else>
                                        <p class="text-success">Tenant Checked Out</p>
                                    </td>
                                    <td>
                                        <button v-if="tenant.unit.is_water_postpaid" @click.prevent="showCreateWaterBillModal(tenant)" class="btn btn-success"><i class="fa fa-hand-holding-water"></i> Create Water Bill</button>
                                    </td>
                                    <td>
                                        <button v-if="tenant.unit.is_electricity_postpaid" @click.prevent="showCreateElectricBillModal(tenant)" class="btn btn-success"><i class="fa fa-lightbulb"></i> Create Electric Bill</button>
                                    </td>
                                    <td>
                                        <a :href="'/owner/tenants/bills/'+tenant.uuid+'/history'">
                                            <button v-if="tenant.unit.is_electricity_postpaid || tenant.unit.is_water_postpaid" class="btn btn-success"><i class="fa fa-clipboard-list"></i> View Bills History</button>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <Bootstrap4Pagination @limit=5 :data="units" @pagination-change-page="loadTenants"/>
                    </div>
                </div>
            </div>
        </div>
        <!-- Create Water Bill Modal -->
        <div class="modal fade" id="createWaterBillModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Create Water Bill</h4>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <form>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label for="billMonth"><strong>Bill Month</strong></label>
                                            <input v-model="createWaterBillForm.billMonth" id="billMonth" type="date" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="unitsConsumed"><strong>Units Consumed</strong></label>
                                            <input v-model="createWaterBillForm.unitsConsumed" id="unitsConsumed" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <p class="text-warning">Note that total amount payable will be (Units Consumed * Cost Per Unit)</p>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label for="costPerUnit"><strong>Cost Per Unit (KES)</strong></label>
                                            <input v-model="createWaterBillForm.costPerUnit" id="costPerUnit" type="text" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="amountPaid"><strong>Amount Paid (KES)</strong></label>
                                            <input v-model="createWaterBillForm.amountPaid" id="amountPaid" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <button v-if="createWaterBillForm.processing" class="btn btn-warning btn-block" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Processing...
                                    </button>
                                    <button v-else @click.prevent="createWaterBill()" type="button" class="btn btn-primary btn-block">Save <i class="fa fa-hand-holding-water"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Create Electric Bill Modal -->
        <div class="modal fade" id="createElectricBillModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Create Electric Bill</h4>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <form>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label for="billMonth"><strong>Bill Month</strong></label>
                                            <input v-model="createElectricBillForm.billMonth" id="billMonth" type="date" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="unitsConsumed"><strong>Units Consumed</strong></label>
                                            <input v-model="createElectricBillForm.unitsConsumed" id="unitsConsumed" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <p class="text-warning">Note that total amount payable will be (Units Consumed * Cost Per Unit)</p>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label for="costPerUnit"><strong>Cost Per Unit (KES)</strong></label>
                                            <input v-model="createElectricBillForm.costPerUnit" id="costPerUnit" type="text" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="amountPaid"><strong>Amount Paid (KES)</strong></label>
                                            <input v-model="createElectricBillForm.amountPaid" id="amountPaid" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <button v-if="createElectricBillForm.processing" class="btn btn-warning btn-block" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Processing...
                                    </button>
                                    <button v-else @click.prevent="createElectricBill()" type="button" class="btn btn-primary btn-block">Save <i class="fa fa-lightbulb"></i></button>
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
    name: "ManageTenants",
    components : {Bootstrap4Pagination},
    data() {
        return {
            properties: {},
            units: {},
            tenants: {},
            createForm: {
                propertyUuid: undefined,
                unitUuid: undefined,
                noOfOccupants: 1,
                name: undefined,
                msisdn: undefined,
                email: undefined,
                nationalIdNo: undefined,
                gender: undefined,
                rentalPaymentChannel: 1,
                stateOnCheckIn: "The unit/ house is brand new",
                tenantAgreementDocUrl: undefined,
                processing: false
            },
            filterForm: {
                propertyUuid: undefined,
                unitUuid: undefined,
                name: undefined,
                msisdn: undefined,
                email: undefined,
                status: 1,
                processing: false
            },
            createWaterBillForm: {
                tenantUuid: undefined,
                type: 1,
                billMonth: undefined,
                unitsConsumed: undefined,
                costPerUnit: undefined,
                amountPayable: undefined,
                amountPaid: 0,
                processing: false
            },
            createElectricBillForm: {
                tenantUuid: undefined,
                type: 2,
                billMonth: undefined,
                unitsConsumed: undefined,
                costPerUnit: undefined,
                amountPayable: undefined,
                amountPaid: 0,
                processing: false
            },
        }
    },
    mounted() {
        this.loadTenants();
        this.loadProperties();
    },
    methods: {
        loadTenants(page = 1){
            const payLoad = {
                unit_uuid : this.filterForm.unitUuid,
                name : this.filterForm.name,
                msisdn : this.filterForm.msisdn,
                email : this.filterForm.email,
                status : this.filterForm.status,
                sort_by : 'latest'
            };
            this.createForm.processing = true;
            this.filterForm.processing = true;
            axios.post(`/owner/tenants/load?page=${page}`, payLoad).then(response => {
                this.tenants = response.data;
            }).catch(error => {
                //
            }).finally(() => {
                this.createForm.processing = false;
                this.filterForm.processing = false;
            });
        },
        loadProperties(page = 1){
            const payLoad = {
                sort_by : 'latest'
            };
            this.createForm.processing = true;
            axios.post(`/owner/properties/load?page=${page}`, payLoad).then(response => {
                this.properties = response.data;
            }).catch(error => {
                //
            }).finally(() => {
                this.createForm.processing = false;
            });
        },
        loadUnits(propertyUuid, page = 1){
            const payLoad = {
                sort_by : 'latest',
                status : false
            };
            this.createForm.processing = true;
            this.filterForm.processing = true;
            axios.post(`/owner/units/${propertyUuid}/load?page=${page}`, payLoad).then(response => {
                this.units = response.data;
            }).catch(error => {
                //
            }).finally(() => {
                this.createForm.processing = false;
                this.filterForm.processing = false;
            });
        },
        onFileChange(event){
            this.createForm.tenantAgreementDocUrl = event.target.files[0];
        },
        createTenant(){
            if (!this.createForm.unitUuid){
                Swal.fire('Error!', 'Unit is required', 'warning');
                return;
            }
            let payLoad = new FormData();
            payLoad.append("no_of_units", this.createForm.noOfUnits || "");
            payLoad.append("no_of_occupants", this.createForm.noOfOccupants);
            payLoad.append("name", this.createForm.name || "");
            payLoad.append("msisdn", this.createForm.msisdn || "");
            payLoad.append("email", this.createForm.email || "");
            payLoad.append("national_id_no", this.createForm.nationalIdNo || "");
            payLoad.append("gender", this.createForm.gender || "");
            payLoad.append("rental_payment_channel", this.createForm.rentalPaymentChannel || "");
            payLoad.append("state_on_check_in", this.createForm.stateOnCheckIn || "");
            payLoad.append("tenant_agreement_doc_url", this.createForm.tenantAgreementDocUrl || "");

            this.createForm.processing = true;
            axios.post(`/owner/tenants/${this.createForm.unitUuid}/create`, payLoad).then((response) => {
                if (response.data.status){
                    this.createForm.unitUuid = undefined;
                    this.createForm.noOfOccupants = 1;
                    this.createForm.name = undefined;
                    this.createForm.msisdn = undefined;
                    this.createForm.email = undefined;
                    this.createForm.nationalIdNo = undefined;
                    this.createForm.gender = undefined;
                    this.createForm.rentalPaymentChannel = 1;
                    this.createForm.stateOnCheckIn = "The unit/ house is brand new";
                    this.createForm.tenantAgreementDocUrl = undefined;
                    Swal.fire('Success!', response.data.message, 'success');
                } else {
                    Swal.fire('Error!', response.data.message, 'warning');
                }
                this.loadTenants();
                this.loadProperties();
            }).catch((error) => {
                if (error.response && error.response.status === 422) {
                    const errorValues = Object.values(error.response.data.errors).flatMap(arr => arr);
                    Swal.fire('Error!', JSON.stringify(errorValues.map(errorMessage => errorMessage)), 'error');
                } else {
                    Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                }
            }).finally(() => {
                this.createForm.processing = false;
            });
        },
        approveTenantCheckOutRequest(tenant){
            //
        },
        showTenantCheckOutModal(tenant){

        },
        showCreateWaterBillModal(tenant){
            this.createWaterBillForm.tenantUuid = tenant.uuid;
            $('#createWaterBillModal').modal('show');
        },
        createWaterBill(){
           const payLoad = {
               type: this.createWaterBillForm.type,
               bill_month: this.createWaterBillForm.billMonth,
               units_consumed: this.createWaterBillForm.unitsConsumed,
               cost_per_unit: this.createWaterBillForm.costPerUnit,
               amount_paid: this.createWaterBillForm.amountPaid
           };

            this.createWaterBillForm.processing = true;
            axios.post(`/owner/tenants/bills/${this.createWaterBillForm.tenantUuid}/create`, payLoad).then((response) => {
                if (response.data.status){
                    this.createWaterBillForm.type = 1;
                    this.createWaterBillForm.billMonth = undefined;
                    this.createWaterBillForm.unitsConsumed = undefined;
                    this.createWaterBillForm.costPerUnit = undefined;
                    this.createWaterBillForm.amountPaid = 0;
                    Swal.fire('Success!', response.data.message, 'success');
                } else {
                    Swal.fire('Error!', response.data.message, 'warning');
                }
                this.loadTenants();
                this.loadProperties();
            }).catch((error) => {
                if (error.response && error.response.status === 422) {
                    const errorValues = Object.values(error.response.data.errors).flatMap(arr => arr);
                    Swal.fire('Error!', JSON.stringify(errorValues.map(errorMessage => errorMessage)), 'error');
                } else {
                    Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                }
            }).finally(() => {
                this.createWaterBillForm.processing = false;
            });
        },
        createElectricBill(){
            const payLoad = {
                type: this.createElectricBillForm.type,
                bill_month: this.createElectricBillForm.billMonth,
                units_consumed: this.createElectricBillForm.unitsConsumed,
                cost_per_unit: this.createElectricBillForm.costPerUnit,
                amount_paid: this.createElectricBillForm.amountPaid
            };

            this.createElectricBillForm.processing = true;
            axios.post(`/owner/tenants/bills/${this.createElectricBillForm.tenantUuid}/create`, payLoad).then((response) => {
                if (response.data.status){
                    this.createElectricBillForm.type = 2;
                    this.createElectricBillForm.billMonth = undefined;
                    this.createElectricBillForm.unitsConsumed = undefined;
                    this.createElectricBillForm.costPerUnit = undefined;
                    this.createElectricBillForm.amountPaid = 0;
                    Swal.fire('Success!', response.data.message, 'success');
                } else {
                    Swal.fire('Error!', response.data.message, 'warning');
                }
                this.loadTenants();
                this.loadProperties();
            }).catch((error) => {
                if (error.response && error.response.status === 422) {
                    const errorValues = Object.values(error.response.data.errors).flatMap(arr => arr);
                    Swal.fire('Error!', JSON.stringify(errorValues.map(errorMessage => errorMessage)), 'error');
                } else {
                    Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                }
            }).finally(() => {
                this.createElectricBillForm.processing = false;
            });
        },
        showCreateElectricBillModal(tenant){
            this.createElectricBillForm.tenantUuid = tenant.uuid;
            $('#createElectricBillModal').modal('show');
        },
        clearFilter(){
            this.filterForm.propertyUuid = undefined;
            this.filterForm.unitUuid = undefined;
            this.filterForm.name = undefined;
            this.filterForm.msisdn = undefined;
            this.filterForm.email = undefined;
            this.loadTenants();
            this.loadProperties();
        }
    }
}

</script>
