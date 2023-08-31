<template>
    <div>
        <div class="row">
            <div class="col-lg-8 col-xl-8 col-md-8 col-sm-8 mx-auto">
                <div class="header">
                    <h1 class="header-title text-center">
                        Create Units For {{ property.name }}
                    </h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="unitType"><strong>Unit Type</strong></label>
                                    <select v-model="createForm.unitTypeId" id="unitType" class="form-control">
                                        <option :value="unitType.id" v-for="unitType in unitTypes.data" :key="unitType.uuid">{{ unitType.type }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="name"><strong>Door Label (e.g. A01)</strong></label>
                                    <input v-model="createForm.label" id="name" type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="status"><strong>Unit/ Room Status</strong></label>
                                    <select v-model="createForm.status" id="status" class="form-control">
                                        <option :value="false">VACANT</option>
                                        <option :value="true">OCCUPIED</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="deposit"><strong>Deposit (KES)</strong></label>
                                    <input v-model="createForm.deposit" id="deposit" type="text" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="rent"><strong>Monthly Rent (KES)</strong></label>
                                    <input v-model="createForm.rent" id="rent" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="isWaterPostpaid"><strong>Is Water Postpaid?</strong></label>
                                    <select v-model="createForm.isWaterPostpaid" id="isWaterPostpaid" class="form-control">
                                        <option :value="true">YES</option>
                                        <option :value="false">NO</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="isElectricityPostpaid"><strong>Is Electricity Postpaid?</strong></label>
                                    <select v-model="createForm.isElectricityPostpaid" id="isElectricityPostpaid" class="form-control">
                                        <option :value="true">YES</option>
                                        <option :value="false">NO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="features"><strong>Unit Features</strong></label>
                                <textarea v-model="createForm.features" id="features" class="form-control" rows="7"></textarea>
                            </div>
                            <button v-if="createForm.processing" class="btn btn-warning btn-block" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Processing...
                            </button>
                            <button v-else @click.prevent="createUnit()" type="button" class="btn btn-primary btn-block">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
                <div class="header">
                    <h1>
                        {{ property.name }} Units
                    </h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-responsive table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Unit Type</th>
                                    <th>Account No (For Rental Payments)</th>
                                    <th>Door Label/ Room Number</th>
                                    <th>Unit/ Room Status</th>
                                    <th>Deposit (KES)</th>
                                    <th>Monthly Rent (KES)</th>
                                    <th>Is Water Postpaid?</th>
                                    <th>Is Electricity Postpaid?</th>
                                    <th>Features</th>
                                    <th colspan="4">Action (Update Unit, Add Photos)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="unit in units.data" :key="unit.uuid">
                                    <td>
                                        <p>{{ unit.type.type }}</p>
                                    </td>
                                    <td>
                                        <p>{{ unit.account_no }}</p>
                                    </td>
                                    <td>
                                        <p>{{ unit.label }}</p>
                                    </td>
                                    <td>
                                        <p class="text-danger" v-if="unit.status">Occupied</p>
                                        <p class="text-success" v-else>Vacant</p>
                                    </td>
                                    <td>
                                        <p>{{ unit.deposit }}</p>
                                    </td>
                                    <td>
                                        <p>{{ unit.rent }}</p>
                                    </td>
                                    <td>
                                        <p class="text-success" v-if="unit.is_water_postpaid">YES</p>
                                        <p class="text-danger" v-else>NO</p>
                                    </td>
                                    <td>
                                        <p class="text-success" v-if="unit.is_electricity_postpaid">YES</p>
                                        <p class="text-danger" v-else>NO</p>
                                    </td>
                                    <td>
                                        <p>{{ unit.features }}</p>
                                    </td>
                                    <td>
                                        <button @click.prevent="showUpdateUnitModal(unit)" class="btn btn-success"><i class="fa fa-edit"></i> Update Unit</button>
                                    </td>
                                    <td>
                                        <button @click.prevent="showCreateUnitPhotosModal(unit)" class="btn btn-success"><i class="fa fa-photo-video"></i> Add/ Delete Photos</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <Bootstrap4Pagination @limit=5 :data="units" @pagination-change-page="loadUnits"/>
                    </div>
                </div>
            </div>
        </div>
        <!-- Update Unit Modal -->
        <div class="modal fade" id="updateUnitModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Update Unit</h4>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <form>
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="unitType"><strong>Unit Type</strong></label>
                                            <select v-model="updateForm.unitTypeId" id="unitType" class="form-control">
                                                <option :value="unitType.id" v-for="unitType in unitTypes.data" :key="unitType.uuid">{{ unitType.type }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="name"><strong>Door Label (e.g. A01)</strong></label>
                                            <input v-model="updateForm.label" id="name" type="text" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="status"><strong>Unit/ Room Status</strong></label>
                                            <select v-model="updateForm.status" id="status" class="form-control">
                                                <option :value="false">VACANT</option>
                                                <option :value="true">OCCUPIED</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label for="deposit"><strong>Deposit (KES)</strong></label>
                                            <input v-model="updateForm.deposit" id="deposit" type="text" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="rent"><strong>Monthly Rent (KES)</strong></label>
                                            <input v-model="updateForm.rent" id="rent" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label for="isWaterPostpaid"><strong>Is Water Postpaid?</strong></label>
                                            <select v-model="updateForm.isWaterPostpaid" id="isWaterPostpaid" class="form-control">
                                                <option :value="true">YES</option>
                                                <option :value="false">NO</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="isElectricityPostpaid"><strong>Is Electricity Postpaid?</strong></label>
                                            <select v-model="updateForm.isElectricityPostpaid" id="isElectricityPostpaid" class="form-control">
                                                <option :value="true">YES</option>
                                                <option :value="false">NO</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="features"><strong>Unit Features</strong></label>
                                        <textarea v-model="updateForm.features" id="features" class="form-control" rows="7"></textarea>
                                    </div>
                                    <button v-if="updateForm.processing" class="btn btn-warning btn-block" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Processing...
                                    </button>
                                    <button v-else @click.prevent="updateUnit()" type="button" class="btn btn-primary btn-block">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Create Unit Photos Modal -->
        <div class="modal fade" id="createUnitPhotosModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Create/ Delete Unit Photo(s)</h4>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4 col-xl-4 col-md-4 col-sm-4" v-for="photo in createUnitPhotosForm.photos" :key="photo.uuid">
                                        <div class="card">
                                            <div class="card-body">
                                                <img class="rounded mx-auto d-block" alt="unit-photo" :src="photo.photo_url" :height="100" :width="100">
                                            </div>
                                            <div class="card-footer">
                                                <button @click.prevent="deleteUnitPhoto(photo)" type="button" class="btn btn-danger btn-block">Delete</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
                                        <form enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label for="image"><strong>Unit Photos ***(can upload multiple photos - maximum of 4 photos at a time)***</strong></label>
                                                <input type="file" ref="file" multiple="multiple" class="form-control">
                                            </div>
                                            <button v-if="createUnitPhotosForm.processing" class="btn btn-warning btn-block" type="button" disabled>
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                Processing...
                                            </button>
                                            <button v-else @click.prevent="createUnitPhotos()" type="button" class="btn btn-primary btn-block">Upload Photo(s)</button>
                                        </form>
                                    </div>
                                </div>
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
    name: "ManageUnits",
    props: ['property'],
    components : {Bootstrap4Pagination},
    data() {
        return {
            unitTypes: {},
            units: {},
            createForm: {
                unitTypeId: undefined,
                label: undefined,
                deposit: undefined,
                rent: undefined,
                isWaterPostpaid: false,
                isElectricityPostpaid: false,
                features: undefined,
                status: false,
                processing: false
            },
            updateForm: {
                unitUuid: undefined,
                unitTypeId: undefined,
                label: undefined,
                deposit: undefined,
                rent: undefined,
                isWaterPostpaid: false,
                isElectricityPostpaid: false,
                features: undefined,
                status: false,
                processing: false
            },
            createUnitPhotosForm: {
                unitUuid: undefined,
                photos: {},
                processing: false
            },
            deleteUnitPhotosForm: {
                unitUuid: undefined,
                processing: false
            }
        }
    },
    mounted() {
        this.loadUnitTypes();
        this.loadUnits();
    },
    methods: {
        loadUnitTypes(page = 1){
            const payLoad = {
                sort_by : 'latest'
            };
            this.createForm.processing = true;
            axios.post(`/load-unit-types?page=${page}`, payLoad).then(response => {
                this.unitTypes = response.data;
            }).catch(error => {
                //
            }).finally(() => {
                this.createForm.processing = false;
            });
        },
        loadUnits(page = 1){
            const payLoad = {
                sort_by : 'latest'
            };
            this.createForm.processing = true;
            axios.post(`/owner/units/${this.property.uuid}/load?page=${page}`, payLoad).then(response => {
                this.units = response.data;
            }).catch(error => {
                //
            }).finally(() => {
                this.createForm.processing = false;
            });
        },
        createUnit(){
            const payLoad = {
                unit_type_id : this.createForm.unitTypeId,
                label : this.createForm.label,
                deposit : this.createForm.deposit,
                rent : this.createForm.rent,
                is_water_postpaid : this.createForm.isWaterPostpaid,
                is_electricity_postpaid : this.createForm.isElectricityPostpaid,
                features : this.createForm.features,
                status : this.createForm.status
            };
            this.createForm.processing = true;
            axios.post(`/owner/units/${this.property.uuid}/create`, payLoad).then((response) => {
                if (response.data.status){
                    this.createForm.unitTypeId = undefined;
                    this.createForm.label = undefined;
                    this.createForm.deposit = undefined;
                    this.createForm.rent = undefined;
                    this.createForm.isWaterPostpaid = false;
                    this.createForm.isElectricityPostpaid = false;
                    this.createForm.features = undefined;
                    this.createForm.status = undefined;
                    Swal.fire('Success!', response.data.message, 'success');
                    this.loadUnitTypes();
                    this.loadUnits();
                } else {
                    Swal.fire('Error!', response.data.message, 'warning');
                }
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
        showUpdateUnitModal(unit){
            this.updateForm.unitUuid = unit.uuid;
            this.updateForm.unitTypeId = unit.unit_type_id;
            this.updateForm.label = unit.label;
            this.updateForm.deposit = unit.deposit;
            this.updateForm.rent = unit.rent;
            this.updateForm.isWaterPostpaid = unit.is_water_postpaid === 1;
            this.updateForm.isElectricityPostpaid = unit.is_electricity_postpaid === 1;
            this.updateForm.features = unit.features;
            this.updateForm.status = unit.status === 1;
            $('#updateUnitModal').modal('show');
        },
        updateUnit(){
            const payLoad = {
                unit_type_id : this.updateForm.unitTypeId,
                label : this.updateForm.label,
                deposit : this.updateForm.deposit,
                rent : this.updateForm.rent,
                is_water_postpaid : this.updateForm.isWaterPostpaid,
                is_electricity_postpaid : this.updateForm.isElectricityPostpaid,
                features : this.updateForm.features,
                status : this.updateForm.status
            };
            this.updateForm.processing = true;
            axios.post(`/owner/units/${this.property.uuid}/update/${this.updateForm.unitUuid}`, payLoad).then((response) => {
                if (response.data.status){
                    this.updateForm.unitTypeId = undefined;
                    this.updateForm.label = undefined;
                    this.updateForm.deposit = undefined;
                    this.updateForm.rent = undefined;
                    this.updateForm.isWaterPostpaid = false;
                    this.updateForm.isElectricityPostpaid = false;
                    this.updateForm.features = undefined;
                    this.updateForm.status = undefined;
                    Swal.fire('Success!', response.data.message, 'success');
                    $('#updateUnitModal').modal('hide');
                    this.loadUnitTypes();
                    this.loadUnits();
                } else {
                    Swal.fire('Error!', response.data.message, 'warning');
                }
            }).catch((error) => {
                if (error.response && error.response.status === 422) {
                    const errorValues = Object.values(error.response.data.errors).flatMap(arr => arr);
                    Swal.fire('Error!', JSON.stringify(errorValues.map(errorMessage => errorMessage)), 'error');
                } else {
                    Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                }
            }).finally(() => {
                this.updateForm.processing = false;
            });
        },
        showCreateUnitPhotosModal(unit){
            this.createUnitPhotosForm.unitUuid = unit.uuid;
            this.createUnitPhotosForm.photos = unit.photos;
            $('#createUnitPhotosModal').modal('show');
        },
        createUnitPhotos(){
            let payLoad = new FormData();
            for(var i = 0; i < this.$refs.file.files.length; i++ ){
                let file = this.$refs.file.files[i];
                payLoad.append('photos[' + i + ']', file);
            }

            this.createUnitPhotosForm.processing = true;
            axios.post(`/owner/units/photos/${this.createUnitPhotosForm.unitUuid}/create`, payLoad, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                }
            ).then((response) => {
                $('#createUnitPhotosModal').modal('hide');
                Swal.fire('Success!', response.data.message, 'success');
                this.createUnitPhotosForm.photos = {};
                this.loadUnitTypes();
                this.loadUnits();
            }).catch((error) => {
                if (error.response && error.response.status === 422) {
                    const errorValues = Object.values(error.response.data.errors).flatMap(arr => arr);
                    Swal.fire('Error!', JSON.stringify(errorValues.map(errorMessage => errorMessage)), 'error');
                } else {
                    Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                }
            }).finally(() => {
                this.createUnitPhotosForm.processing = false;
            });
        },
        deleteUnitPhoto(photo){
            Swal.fire({
                title: 'Delete photo?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/owner/units/photos/${photo.uuid}/delete`).then((response) => {
                        if (response.data.status === true){
                            Swal.fire('Success!', response.data.message, 'success');
                            this.loadUnitTypes();
                            this.loadUnits();
                            $('#createUnitPhotosModal').modal('hide');
                        } else {
                            Swal.fire('Error!', response.data.message, 'warning');
                        }
                    }).catch((error) => {
                        if (error.response && error.response.status === 422) {
                            const errorValues = Object.values(error.response.data.errors).flatMap(arr => arr);
                            Swal.fire('Error!', JSON.stringify(errorValues.map(errorMessage => errorMessage)), 'error');
                        } else {
                            Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                        }
                    }).finally(() => {});
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swalWithBootstrapButtons.fire('Cancelled', 'Your file is safe :)', 'error');
                }
            })
        },
    }
}

</script>
