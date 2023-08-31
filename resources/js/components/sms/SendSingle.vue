<template>
    <div>
        <div class="row">
            <div class="col-lg-8 col-xl-8 col-md-8 col-sm-8 mx-auto">
                <div class="header">
                    <h1 class="header-title text-center">
                        Send Single SMS
                    </h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label for="msisdn"><strong>Msisdn/ Phone</strong></label>
                                    <input v-model="sendSmsForm.msisdn" id="msisdn" type="text" class="form-control">
                                </div>

                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <label for="message"><strong>Message</strong></label>
                                    <textarea v-model="sendSmsForm.message" class="form-control" id="message" rows="7"></textarea>
                                </div>
                            </div>
                            <button v-if="sendSmsForm.processing" class="btn btn-warning btn-block" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Processing...
                            </button>
                            <button v-else @click.prevent="sendSms()" type="button" class="btn btn-primary btn-block">Send SMS</button>
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
    name: "SendSingleSms",
    components : {Bootstrap4Pagination},
    data() {
        return {
            sendSmsForm: {
                msisdn: undefined,
                message: undefined,
                processing: false
            }
        }
    },
    methods: {
        sendSms(){
            const payLoad = {
                msisdn : this.sendSmsForm.msisdn,
                message : this.sendSmsForm.message
            };
            this.sendSmsForm.processing = true;
            axios.post(`/owner/messages/send-single`, payLoad).then((response) => {
                if (response.data.status){
                    this.sendSmsForm.msisdn = undefined;
                    this.sendSmsForm.message = undefined;
                    Swal.fire('Success!', response.data.message, 'success');
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
                this.sendSmsForm.processing = false;
            });
        }
    }
}

</script>
