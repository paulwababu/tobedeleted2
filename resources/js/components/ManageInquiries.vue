<template>
    <div>
        <div class="row">
            <div class="col-lg-8 col-xl-8 col-md-8 col-sm-8 mx-auto">
                <div class="header">
                    <h1 class="header-title text-center">
                        Filter Inquiries
                    </h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="start"><strong>Start</strong></label>
                                    <input v-model="filterForm.start" id="start" type="date" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="end"><strong>End</strong></label>
                                    <input v-model="filterForm.end" id="end" type="date" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="name"><strong>Name</strong></label>
                                    <input v-model="filterForm.name" id="name" type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="msisdn"><strong>Msisdn</strong></label>
                                    <input v-model="filterForm.msisdn" id="msisdn" type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="email"><strong>Email</strong></label>
                                    <input v-model="filterForm.email" id="email" type="email" class="form-control">
                                </div>
                            </div>
                            <button v-if="filterForm.processing" class="btn btn-warning btn-block" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </button>
                            <button v-else @click.prevent="loadInquiries()" type="button" class="btn btn-primary btn-block"><i class="fa fa-search"></i></button>
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
                        Property Inquiries
                    </h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-responsive table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Property</th>
                                    <th>Name</th>
                                    <th>Msisdn</th>
                                    <th>Email</th>
                                    <th>Inquiry/ Question</th>
                                    <th>Status</th>
                                    <th>Inquiry Response</th>
                                    <th>Response Date</th>
                                    <th>Response By</th>
                                    <th colspan="4">Action (Respond To Inquiry)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="inquiry in inquiries.data" :key="inquiry.uuid">
                                    <td>
                                        <p>{{ inquiry.property.name }}</p>
                                    </td>
                                    <td>
                                        <p>{{ inquiry.name }}</p>
                                    </td>
                                    <td>
                                        <p>{{ inquiry.msisdn }}</p>
                                    </td>
                                    <td>
                                        <p>{{ inquiry.email }}</p>
                                    </td>
                                    <td>
                                        <p>{{ inquiry.inquiry }}</p>
                                    </td>
                                    <td>
                                        <p class="text-success" v-if="inquiry.status">Responded To</p>
                                        <p class="text-danger" v-else>Pending</p>
                                    </td>
                                    <td>
                                        <p>{{ inquiry.response }}</p>
                                    </td>
                                    <td>
                                        <p>{{ inquiry.response_date }}</p>
                                    </td>
                                    <td>
                                        <p>{{ inquiry.user ? inquiry.user.name : '' }}</p>
                                    </td>
                                    <td>
                                        <button v-if="!inquiry.status" @click.prevent="showRespondToInquiryModal(inquiry)" class="btn btn-success"><i class="fa fa-comment-alt"></i> Respond</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <Bootstrap4Pagination @limit=5 :data="inquiries" @pagination-change-page="loadInquiries"/>
                    </div>
                </div>
            </div>
        </div>
        <!-- Respond To Inquiry Modal -->
        <div class="modal fade" id="respondToInquiryModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Respond To Inquiry</h4>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <form>
                                    <div class="form-group">
                                        <label for="features"><strong>Response</strong></label>
                                        <textarea v-model="respondToInquiryForm.inquiryResponse" id="features" class="form-control" rows="7"></textarea>
                                    </div>
                                    <button v-if="respondToInquiryForm.processing" class="btn btn-warning btn-block" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Processing...
                                    </button>
                                    <button v-else @click.prevent="respondToInquiry()" type="button" class="btn btn-primary btn-block">Save & Send Response As SMS To Client</button>
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
    name: "ManageInquiries",
    components : {Bootstrap4Pagination},
    data() {
        return {
            inquiries: {},
            filterForm: {
                name: undefined,
                msisdn: undefined,
                email: undefined,
                processing: false
            },
            respondToInquiryForm: {
                inquiryUuid: undefined,
                propertyUuid: undefined,
                inquiryResponse: undefined,
                processing: false
            }
        }
    },
    mounted() {
        this.loadInquiries();
    },
    methods: {
        loadInquiries(page = 1){
            const payLoad = {
                sort_by : 'latest',
                start: this.filterForm.start,
                end: this.filterForm.end,
                name: this.filterForm.name,
                msisdn: this.filterForm.msisdn,
                email: this.filterForm.email
            };
            this.filterForm.processing = true;
            axios.post(`/owner/inquiries/load?page=${page}`, payLoad).then(response => {
                this.inquiries = response.data;
            }).catch(error => {
                //
            }).finally(() => {
                this.filterForm.processing = false;
            });
        },
        showRespondToInquiryModal(inquiry){
            this.respondToInquiryForm.inquiryUuid = inquiry.uuid;
            this.respondToInquiryForm.propertyUuid = inquiry.property.uuid;
            $('#respondToInquiryModal').modal('show');
        },
        respondToInquiry(){
            const payLoad = {
                inquiry_response: this.respondToInquiryForm.inquiryResponse
            };

            this.respondToInquiryForm.processing = true;
            axios.post(`/owner/inquiries/${this.respondToInquiryForm.propertyUuid}/${this.respondToInquiryForm.inquiryUuid}/respond`, payLoad).then((response) => {
                this.respondToInquiryForm.inquiryResponse = undefined;
                Swal.fire('Success!', response.data.message, 'success');
                $('#respondToInquiryModal').modal('hide');
                this.loadInquiries();
            }).catch((error) => {
                if (error.response && error.response.status === 422) {
                    const errorValues = Object.values(error.response.data.errors).flatMap(arr => arr);
                    Swal.fire('Error!', JSON.stringify(errorValues.map(errorMessage => errorMessage)), 'error');
                } else {
                    Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                }
            }).finally(() => {
                this.respondToInquiryForm.processing = false;
            });
        }
    }
}

</script>
