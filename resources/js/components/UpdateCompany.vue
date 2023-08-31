<template>
    <div>
        <div class="row">
            <div class="col-lg-8 col-xl-8 col-md-8 col-sm-8 mx-auto">
                <div class="header">
                    <h1 class="header-title text-center">
                        Update "{{ updateForm.name }}" Company Details
                    </h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="name"><strong>Company Name</strong></label>
                                    <input v-model="updateForm.name" id="name" type="text" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="msisdn"><strong>Msisdn/ Phone</strong></label>
                                    <input v-model="updateForm.msisdn" id="msisdn" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="email"><strong>Email</strong></label>
                                    <input v-model="updateForm.email" id="email" type="email" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="location"><strong>Location</strong></label>
                                    <input v-model="updateForm.location" id="email" type="text" class="form-control">
                                </div>
                            </div>
                            <button v-if="updateForm.processing" class="btn btn-warning btn-block" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Processing...
                            </button>
                            <button v-else @click.prevent="updateCompany()" type="button" class="btn btn-primary btn-block">Update</button>
                        </form>
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
    name: "UpdateCompany",
    props: ['company_uuid'],
    components : {Bootstrap4Pagination},
    data() {
        return {
            units: {},
            updateForm: {
                companyUuid: undefined,
                name: undefined,
                msisdn: undefined,
                email: undefined,
                location: undefined,
                processing: false
            }
        }
    },
    mounted() {
        this.loadCompany(this.company_uuid);
    },
    methods: {
        loadCompany(uuid){
            const payLoad = {
                sort_by : 'latest'
            };
            this.updateForm.processing = true;
            axios.post(`/company/${uuid}/load`, payLoad).then(response => {
                this.updateForm.companyUuid = response.data.uuid;
                this.updateForm.name = response.data.name;
                this.updateForm.msisdn = response.data.msisdn;
                this.updateForm.email = response.data.email;
                this.updateForm.location = response.data.location;
            }).catch(error => {
                //
            }).finally(() => {
                this.updateForm.processing = false;
            });
        },
        updateCompany(){
            const payLoad = {
                name : this.updateForm.name,
                msisdn : this.updateForm.msisdn,
                email : this.updateForm.email,
                location : this.updateForm.location
            };
            this.updateForm.processing = true;
            axios.post(`/company/${this.company_uuid}/update`, payLoad).then((response) => {
                if (response.data.status){
                    this.updateForm.companyUuid = undefined;
                    this.updateForm.name = undefined;
                    this.updateForm.msisdn = undefined;
                    this.updateForm.email = undefined;
                    this.updateForm.location = undefined;
                    Swal.fire('Success!', response.data.message, 'success');
                    this.loadCompany(this.company_uuid);
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
        }
    }
}

</script>
