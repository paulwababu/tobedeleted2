<template>
    <div>
        <div class="row">
            <div class="col-lg-8 col-xl-8 col-md-8 col-sm-8 mx-auto">
                <div class="header">
                    <h1 class="header-title text-center">
                        Create Agent
                    </h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="name"><strong>Name</strong></label>
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
                                    <label for="location"><strong>Location</strong></label>
                                    <input v-model="createForm.location" id="location" type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="national_id_no"><strong>National ID Number/ Passport Number</strong></label>
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
                            </div>
                            <button v-if="createForm.processing" class="btn btn-warning btn-block" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </button>
                            <button v-else @click.prevent="createAgent()" type="button" class="btn btn-primary btn-block">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
                <div class="header">
                    <h1>
                        My Agents
                    </h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="name"><strong>Full Name</strong></label>
                                    <input v-model="filterForm.name" id="name" type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="msisdn"><strong>Msisdn/ Phone</strong></label>
                                    <input v-model="filterForm.msisdn" id="msisdn" type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="email"><strong>Email</strong></label>
                                    <input v-model="filterForm.email" id="email" type="email" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <button v-if="filterForm.processing" class="btn btn-warning btn-block" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    </button>
                                    <button v-else @click.prevent="loadAgents()" type="button" class="btn btn-primary btn-block"><i class="fa fa-search"></i></button>
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
                                    <th>Full Name</th>
                                    <th>Msisdn/ phone</th>
                                    <th>Email</th>
                                    <th>Location</th>
                                    <th>Account Status</th>
                                    <th>National ID/ Passport Number</th>
                                    <th>Profile Pic</th>
                                    <th>Gender</th>
                                    <th colspan="2">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="agent in agents.data" :key="agent.uuid">
                                    <td>
                                        <p>{{ agent.name }}</p>
                                    </td>
                                    <td>
                                        <p>{{ agent.msisdn }}</p>
                                    </td>
                                    <td>
                                        <p>{{ agent.email }}</p>
                                    </td>
                                    <td>
                                        <p>{{ agent.location }}</p>
                                    </td>
                                    <td>
                                        <p class="text-danger" v-if="agent.status === 0">Pending</p>
                                        <p class="text-success" v-else-if="agent.status === 1">Approved</p>
                                        <p class="text-warning" v-else>Suspended</p>
                                    </td>
                                    <td>
                                        <p>{{ agent.national_id_no }}</p>
                                    </td>
                                    <td>
                                        <p><a target="_blank" :href="agent.profile_photo_url">open file</a></p>
                                    </td>
                                    <td>
                                        <p v-if="agent.gender === 1">Male</p>
                                        <p v-else-if="agent.gender === 2">Female</p>
                                        <p v-else>Anonymous</p>
                                    </td>
                                    <td>
                                        <button @click.prevent="showUpdateAgentModal(agent)" class="btn btn-success"><i class="fa fa-user-edit"></i> Update Agent</button>
                                    </td>
                                    <td v-if="agent.status === 0 || agent.status === 2">
                                        <button @click.prevent="approveAgent(agent)" class="btn btn-success"><i class="fa fa-check-circle"></i> Approve Agent</button>
                                    </td>
                                    <td v-else>
                                        <button @click.prevent="suspendAgent(agent)" class="btn btn-danger"><i class="fa fa-ban"></i> Suspend Agent</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <Bootstrap4Pagination @limit=5 :data="agents" @pagination-change-page="loadAgents"/>
                    </div>
                </div>
            </div>
        </div>
        <!-- Update Agent Modal -->
        <div class="modal fade" id="updateAgentModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Update Agent</h4>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <form>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label for="name"><strong>Name</strong></label>
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
                                            <input v-model="updateForm.location" id="location" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label for="national_id_no"><strong>National ID Number/ Passport Number</strong></label>
                                            <input v-model="updateForm.nationalIdNo" id="national_id_no" type="text" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="gender"><strong>Gender</strong></label>
                                            <select v-model="updateForm.gender" id="gender" class="form-control">
                                                <option :value="1">MALE</option>
                                                <option :value="2">FEMALE</option>
                                                <option :value="3">ANONYMOUS</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button v-if="updateForm.processing" class="btn btn-warning btn-block" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    </button>
                                    <button v-else @click.prevent="updateAgent()" type="button" class="btn btn-primary btn-block">Save</button>
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
    name: "ManageAgents",
    components : {Bootstrap4Pagination},
    data() {
        return {
            agents: {},
            createForm: {
                name: undefined,
                msisdn: undefined,
                email: undefined,
                location: undefined,
                nationalIdNo: undefined,
                gender: undefined,
                processing: false
            },
            updateForm: {
                agentUuid: undefined,
                name: undefined,
                msisdn: undefined,
                email: undefined,
                location: undefined,
                nationalIdNo: undefined,
                gender: undefined,
                processing: false
            },
            filterForm: {
                name: undefined,
                msisdn: undefined,
                email: undefined,
                processing: false
            }
        }
    },
    mounted() {
        this.loadAgents();
    },
    methods: {
        loadAgents(page = 1){
            const payLoad = {
                sort_by : 'latest',
                name: this.filterForm.name,
                msisdn: this.filterForm.msisdn,
                email: this.filterForm.email,
            };
            this.filterForm.processing = true;
            axios.post(`/owner/agents/load?page=${page}`, payLoad).then(response => {
                this.agents = response.data;
            }).catch(error => {
                //
            }).finally(() => {
                this.filterForm.processing = false;
            });
        },
        createAgent(){
            const payLoad = {
                name: this.createForm.name,
                msisdn: this.createForm.msisdn,
                email: this.createForm.email,
                location: this.createForm.location,
                national_id_no: this.createForm.nationalIdNo,
                gender: this.createForm.gender
            };

            this.createForm.processing = true;
            axios.post(`/owner/agents/create`, payLoad).then((response) => {
                if (response.data.status){
                    this.createForm.name = undefined;
                    this.createForm.msisdn = undefined;
                    this.createForm.email = undefined;
                    this.createForm.location = undefined;
                    this.createForm.nationalIdNo = undefined;
                    this.createForm.gender = undefined;
                    Swal.fire('Success!', response.data.message, 'success');
                } else {
                    Swal.fire('Error!', response.data.message, 'warning');
                }
                this.loadAgents();
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
        showUpdateAgentModal(agent){
            this.updateForm.agentUuid = agent.uuid;
            this.updateForm.name = agent.name;
            this.updateForm.msisdn = agent.msisdn;
            this.updateForm.email = agent.email;
            this.updateForm.location = agent.location;
            this.updateForm.nationalIdNo = agent.national_id_no;
            this.updateForm.gender = agent.gender;
            $('#updateAgentModal').modal('show');
        },
        updateAgent(){
            const payLoad = {
                name: this.updateForm.name,
                msisdn: this.updateForm.msisdn,
                email: this.updateForm.email,
                location: this.updateForm.location,
                national_id_no: this.updateForm.nationalIdNo,
                gender: this.updateForm.gender
            };

            this.updateForm.processing = true;
            axios.post(`/owner/agents/${this.updateForm.agentUuid}/update`, payLoad).then((response) => {
                if (response.data.status){
                    this.updateForm.name = undefined;
                    this.updateForm.msisdn = undefined;
                    this.updateForm.email = undefined;
                    this.updateForm.location = undefined;
                    this.updateForm.nationalIdNo = undefined;
                    this.updateForm.gender = undefined;
                    Swal.fire('Success!', response.data.message, 'success');
                } else {
                    Swal.fire('Error!', response.data.message, 'warning');
                }
                $('#updateAgentModal').modal('hide');
                this.loadAgents();
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
        approveAgent(agent){
            Swal.fire({
                title: 'Approve agent?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(`/owner/agents/${agent.uuid}/approve`).then((response) => {
                        if (response.data.status === true){
                            Swal.fire('Success!', response.data.message, 'success');
                            this.loadAgents();
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
                    swalWithBootstrapButtons.fire('Cancelled', 'Your agent was not approved :)', 'error');
                }
            });
        },
        suspendAgent(agent){
            Swal.fire({
                title: 'Suspend agent?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, suspend!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(`/owner/agents/${agent.uuid}/suspend`).then((response) => {
                        if (response.data.status === true){
                            Swal.fire('Success!', response.data.message, 'success');
                            this.loadAgents();
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
                    swalWithBootstrapButtons.fire('Cancelled', 'Your was not suspended :)', 'error');
                }
            })
        },
        clearFilter(){
            this.filterForm.name = undefined;
            this.filterForm.msisdn = undefined;
            this.filterForm.email = undefined;
            this.loadAgents();
        }
    }
}

</script>
