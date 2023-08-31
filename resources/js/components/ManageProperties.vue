<template>
    <div>
        <div class="row">
            <div class="col-lg-8 col-xl-8 col-md-8 col-sm-8 mx-auto">
                <div class="header">
                    <h1 class="header-title text-center">
                        Create Properties
                    </h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form enctype="multipart/form-data">
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="name"><strong>Property Name</strong></label>
                                    <input v-model="createForm.name" id="name" type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="no_of_units"><strong>No Of Units</strong></label>
                                    <input v-model="createForm.noOfUnits" id="no_of_units" type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="type"><strong>Type</strong></label>
                                    <select v-model="createForm.type" id="type" class="form-control">
                                        <option :value="1">Residential</option>
                                        <option :value="2">Commercial</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="location"><strong>Property Location</strong></label>
                                    <input v-model="createForm.location" id="location" type="text" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="county_uuid"><strong>Select County</strong></label>
                                    <select v-model="createForm.countyId" id="county_uuid" class="form-control">
                                        <option :value="county.id" v-for="county in counties.data" :key="county.uuid">{{ county.county }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="kyc_docs_url"><strong>Property KYC Document</strong></label>
                                    <input id="kyc_docs_url" type="file" v-on:change="onFileChangeKyc($event,'create')" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="photo_url"><strong>Cover Photo</strong></label>
                                    <input id="photo_url" type="file" v-on:change="onFileChangeCoverPhoto($event,'create')" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description"><strong>Property Description</strong></label>
                                <textarea v-model="createForm.description" id="description" class="form-control" rows="7"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="features"><strong>Property Features</strong></label>
                                <textarea v-model="createForm.features" id="features" class="form-control" rows="7"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="youtube_embed_code"><strong>Google Map Embed Code *optional (Copy paste embed code here) <a href="https://www.google.com/maps" target="_blank">Generate Map Location Embed Code</a> | <a href="https://youtu.be/KIC0OK9nKXY" target="_blank">How to generate embed code?</a></strong></label>
                                <textarea v-model="createForm.googleMapEmbedCode" id="youtube_embed_code" class="form-control" rows="4">Paste here...</textarea>
                            </div>
                            <div class="form-group">
                                <label for="youtube_embed_code"><strong>Youtube Embed Code *optional (Copy paste youtube embed code here) | <a href="https://www.youtube.com/watch?v=lJIrF4YjHfQ" target="_blank">How to generate video embed code?</a></strong></label>
                                <textarea v-model="createForm.youtubeEmbedCode" id="youtube_embed_code" class="form-control" rows="4">Paste here...</textarea>
                            </div>
                            <button v-if="createForm.processing" class="btn btn-warning btn-block" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Processing...
                            </button>
                            <button v-else @click.prevent="createProperty()" type="button" class="btn btn-primary btn-block">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
                <div class="header">
                    <h1>
                        My Properties
                    </h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-responsive table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Cover Photo</th>
                                    <th>Property Name</th>
                                    <th>No Of Units</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Features</th>
                                    <th>Location</th>
                                    <th>County</th>
                                    <th>KYC Docs Url</th>
                                    <th>Status</th>
                                    <th colspan="4">Action (Update Property, Create Units)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="property in properties.data" :key="property.uuid">
                                    <td>
                                        <img :src="property.photo_url" width="100" height="100" class="my-n1" :alt="property.name">
                                    </td>
                                    <td>
                                        <p>{{ property.name }}</p>
                                    </td>
                                    <td>
                                        <p>{{ property.no_of_units }}</p>
                                    </td>
                                    <td>
                                        <p v-if="property.type === 1">Residential</p>
                                        <p v-else>Commercial</p>
                                    </td>
                                    <td>
                                        <p>{{ property.description }}</p>
                                    </td>
                                    <td>
                                        <p>{{ property.features }}</p>
                                    </td>
                                    <td>
                                        <p>{{ property.location }}</p>
                                    </td>
                                    <td>
                                        <p>{{ property.county.county }}</p>
                                    </td>
                                    <td>
                                        <a :href='property.kyc_docs_url' target="_blank">Open File</a>
                                    </td>
                                    <td>
                                        <p class="text-success" v-if="property.status">Approved</p>
                                        <p class="text-danger" v-else>Pending</p>
                                    </td>
                                    <td>
                                        <button @click.prevent="showUpdatePropertyModal(property)" class="btn btn-success"><i class="fa fa-edit"></i> Update Property</button>
                                    </td>
                                    <td>
                                        <a :href="'/owner/units/'+property.uuid+'/manage-units'" class="btn btn-success"><i class="fa fa-plus-square"></i> Create Units</a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <Bootstrap4Pagination @limit=5 :data="properties" @pagination-change-page="loadProperties"/>
                    </div>
                </div>
            </div>
        </div>
        <!-- Update Property Modal -->
        <div class="modal fade" id="updatePropertyModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Update Property</h4>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <form enctype="multipart/form-data">
                                    <div class="form-group row">
                                        <div class="col-md-4">
                                            <label for="name"><strong>Property Name</strong></label>
                                            <input v-model="updateForm.name" id="name" type="text" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="no_of_units"><strong>No Of Units</strong></label>
                                            <input v-model="updateForm.noOfUnits" id="no_of_units" type="text" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="type"><strong>Type</strong></label>
                                            <select v-model="updateForm.type" id="type" class="form-control">
                                                <option :value="1">Residential</option>
                                                <option :value="2">Commercial</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label for="location"><strong>Property Location</strong></label>
                                            <input v-model="updateForm.location" id="location" type="text" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="county_uuid"><strong>Select County</strong></label>
                                            <select v-model="updateForm.countyId" id="county_uuid" class="form-control">
                                                <option :value="county.id" v-for="county in counties.data" :key="county.uuid">{{ county.county }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label for="kyc_docs_url"><strong>Property KYC Document</strong></label>
                                            <input id="kyc_docs_url" type="file" v-on:change="onFileChangeKyc($event, 'update')" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="photo_url"><strong>Cover Photo</strong></label>
                                            <input id="photo_url" type="file" v-on:change="onFileChangeCoverPhoto($event,'update')" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="description"><strong>Property Description</strong></label>
                                        <textarea v-model="updateForm.description" id="description" class="form-control" rows="7"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="features"><strong>Property Features</strong></label>
                                        <textarea v-model="updateForm.features" id="features" class="form-control" rows="7"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="youtube_embed_code"><strong>Google Map Embed Code *optional (Copy paste embed code here) <a href="https://www.google.com/maps" target="_blank">Generate Map Location Embed Code</a> | <a href="https://youtu.be/KIC0OK9nKXY" target="_blank">How to generate embed code?</a></strong></label>
                                        <textarea v-model="updateForm.googleMapEmbedCode" id="youtube_embed_code" class="form-control" rows="4">Paste here...</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="youtube_embed_code"><strong>Youtube Embed Code *optional (Copy paste youtube embed code here) | <a href="https://www.youtube.com/watch?v=lJIrF4YjHfQ" target="_blank">How to generate video embed code?</a></strong></label>
                                        <textarea v-model="updateForm.youtubeEmbedCode" id="youtube_embed_code" class="form-control" rows="4">Paste here...</textarea>
                                    </div>
                                    <button v-if="updateForm.processing" class="btn btn-warning btn-block" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Processing...
                                    </button>
                                    <button v-else @click.prevent="updateProperty()" type="button" class="btn btn-primary btn-block">Save</button>
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
import { Bootstrap4Pagination } from 'laravel-vue-pagination';

export default {
    name: "ManageProperties",
    components : {Bootstrap4Pagination},
    data() {
        return {
            counties: {},
            properties: {},
            createForm: {
                name: undefined,
                noOfUnits: undefined,
                type: 1,
                description: undefined,
                features: undefined,
                location: undefined,
                countyId: undefined,
                kycDocsUrl: undefined,
                googleMapEmbedCode: undefined,
                photoUrl: undefined,
                youtubeEmbedCode: undefined,
                processing: false
            },
            updateForm: {
                propertyUuid: undefined,
                name: undefined,
                noOfUnits: undefined,
                type: 1,
                description: undefined,
                features: undefined,
                location: undefined,
                countyId: undefined,
                kycDocsUrl: undefined,
                googleMapEmbedCode: undefined,
                photoUrl: undefined,
                youtubeEmbedCode: undefined,
                processing: false
            }
        }
    },
    mounted() {
        this.loadCounties();
        this.loadProperties();
    },
    methods: {
        loadCounties(page = 1){
            const payLoad = {
                sort_by : 'latest'
            };
            this.createForm.processing = true;
            axios.post(`/load-counties?page=${page}`, payLoad).then(response => {
                this.counties = response.data;
            }).catch(error => {
                //
            }).finally(() => {
                this.createForm.processing = false;
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
        onFileChangeCoverPhoto(event, type){
            if (type === 'create'){
                this.createForm.photoUrl = event.target.files[0];
            } else {
                this.updateForm.photoUrl = event.target.files[0];
            }
        },
        onFileChangeKyc(event, type){
            if (type === 'create'){
                this.createForm.kycDocsUrl = event.target.files[0];
            } else {
                this.updateForm.kycDocsUrl = event.target.files[0];
            }
        },
        createProperty(){
            let payLoad = new FormData();
            payLoad.append("name", this.createForm.name || "");
            payLoad.append("no_of_units", this.createForm.noOfUnits || "");
            payLoad.append("type", this.createForm.type);
            payLoad.append("description", this.createForm.description || "");
            payLoad.append("features", this.createForm.features || "");
            payLoad.append("location", this.createForm.location || "");
            payLoad.append("county_id", this.createForm.countyId || "");
            payLoad.append("kyc_docs_url", this.createForm.kycDocsUrl || "");
            payLoad.append("google_map_embed_code", this.createForm.googleMapEmbedCode || "");
            payLoad.append("photo_url", this.createForm.photoUrl || "");
            payLoad.append("youtube_embed_code", this.createForm.youtubeEmbedCode || "");

            this.createForm.processing = true;
            axios.post('/owner/properties/create', payLoad).then((response) => {
                this.createForm.name = undefined;
                this.createForm.noOfUnits = undefined;
                this.createForm.type = 1;
                this.createForm.description = undefined;
                this.createForm.features = undefined;
                this.createForm.location = undefined;
                this.createForm.countyId = undefined;
                this.createForm.kycDocsUrl = undefined;
                this.createForm.googleMapEmbedCode = undefined;
                this.createForm.photoUrl = undefined;
                this.createForm.youtubeEmbedCode = undefined;
                Swal.fire('Success!', response.data.message, 'success');
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
        showUpdatePropertyModal(property){
            this.updateForm.propertyUuid = property.uuid;
            this.updateForm.name = property.name;
            this.updateForm.noOfUnits = property.no_of_units;
            this.updateForm.type = property.type;
            this.updateForm.description = property.description;
            this.updateForm.features = property.features;
            this.updateForm.location = property.location;
            this.updateForm.countyId = property.county_id;
            this.updateForm.googleMapEmbedCode = property.google_map_embed_code;
            this.updateForm.youtubeEmbedCode = property.youtube_embed_code;
            $('#updatePropertyModal').modal('show');
        },
        updateProperty(){
            let payLoad = new FormData();
            payLoad.append("name", this.updateForm.name || "");
            payLoad.append("no_of_units", this.updateForm.noOfUnits || "");
            payLoad.append("type", this.updateForm.type);
            payLoad.append("description", this.updateForm.description || "");
            payLoad.append("features", this.updateForm.features || "");
            payLoad.append("location", this.updateForm.location || "");
            payLoad.append("county_id", this.updateForm.countyId || "");
            payLoad.append("kyc_docs_url", this.updateForm.kycDocsUrl ? this.updateForm.kycDocsUrl : '');
            payLoad.append("google_map_embed_code", this.updateForm.googleMapEmbedCode || "");
            payLoad.append("photo_url", this.updateForm.photoUrl ? this.updateForm.photoUrl : '');
            payLoad.append("youtube_embed_code", this.updateForm.youtubeEmbedCode || "");

            this.updateForm.processing = true;
            axios.post(`/owner/properties/${this.updateForm.propertyUuid}/update`, payLoad).then((response) => {
                if (response.data.status){
                    this.updateForm.name = undefined;
                    this.updateForm.noOfUnits = undefined;
                    this.updateForm.type = 1;
                    this.updateForm.description = undefined;
                    this.updateForm.features = undefined;
                    this.updateForm.location = undefined;
                    this.updateForm.countyId = undefined;
                    this.updateForm.kycDocsUrl = undefined;
                    this.updateForm.googleMapEmbedCode = undefined;
                    this.updateForm.photoUrl = undefined;
                    this.updateForm.youtubeEmbedCode = undefined;
                    Swal.fire('Success!', response.data.message, 'success');
                    $('#updatePropertyModal').modal('hide');
                } else {
                    Swal.fire('Error!', response.data.message, 'warning');
                }
                this.loadProperties();
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
        }
    }
}

</script>
