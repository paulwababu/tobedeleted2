<template>
    <div>
        <div class="row">
            <div class="col-lg-8 col-xl-8 col-md-8 col-sm-8 mx-auto">
                <div class="header">
                    <h1 class="header-title text-center">
                        Filter Outgoing Messages
                    </h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="msisdn"><strong>Msisdn/ Phone</strong></label>
                                    <input v-model="filterForm.msisdn" id="msisdn" type="text" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="message"><strong>Message Keyword</strong></label>
                                    <input v-model="filterForm.message" id="message" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="tracking_id"><strong>Tracking ID</strong></label>
                                    <input v-model="filterForm.trackingId" id="tracking_id" type="text" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="status"><strong>Status</strong></label>
                                    <select v-model="filterForm.status" id="status" class="form-control">
                                        <option :value="0">FAILED</option>
                                        <option :value="1">PENDING</option>
                                        <option :value="2">DELIVERED</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <button v-if="filterForm.processing" class="btn btn-warning btn-block" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    </button>
                                    <button v-else @click.prevent="loadMessages()" type="button" class="btn btn-primary btn-block"><i class="fa fa-search"></i></button>
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
                    <h1 class="text-center">
                        Outgoing Messages History
                    </h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-responsive table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Msisdn/ phone</th>
                                    <th>Message</th>
                                    <th>Sent At</th>
                                    <th>SMS Response</th>
                                    <th>Tracking ID</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="message in messages.data" :key="message.uuid">
                                    <td>
                                        <p>{{ message.msisdn }}</p>
                                    </td>
                                    <td>
                                        <p>{{ message.message }}</p>
                                    </td>
                                    <td>
                                        <p>{{ message.created_at }}</p>
                                    </td>
                                    <td>
                                        <p>{{ message.api_response_message }}</p>
                                    </td>
                                    <td>
                                        <p>{{ message.tracking_id }}</p>
                                    </td>
                                    <td>
                                        <p class="text-danger" v-if="message.status === 0">Failed</p>
                                        <p class="text-warning" v-else-if="message.status === 1">Pending</p>
                                        <p class="text-danger" v-else>Delivered</p>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <Bootstrap4Pagination @limit=5 :data="messages" @pagination-change-page="loadMessages"/>
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
    name: "OutgoingSms",
    components : {Bootstrap4Pagination},
    data() {
        return {
            messages: {},
            filterForm: {
                msisdn: undefined,
                message: undefined,
                trackingId: undefined,
                status: undefined
            }
        }
    },
    mounted() {
        this.loadMessages();
    },
    methods: {
        loadMessages(page = 1){
            const payLoad = {
                msisdn : this.filterForm.msisdn,
                message : this.filterForm.email,
                tracking_id : this.filterForm.trackingId,
                status : this.filterForm.status,
                sort_by : 'latest'
            };
            this.filterForm.processing = true;
            axios.post(`/owner/messages/outgoing/load?page=${page}`, payLoad).then(response => {
                this.messages = response.data;
            }).catch(error => {
                //
            }).finally(() => {
                this.filterForm.processing = false;
            });
        },
        clearFilter(){
            this.filterForm.msisdn = undefined;
            this.filterForm.message = undefined;
            this.filterForm.trackingId = undefined;
            this.loadMessages();
        }
    }
}

</script>
