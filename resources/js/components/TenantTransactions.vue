<template>
    <div>
        <div class="row">
            <div class="col-lg-8 col-xl-8 col-md-8 col-sm-8 mx-auto">
                <div class="header">
                    <h1 class="header-title text-center">
                        Filter Transactions
                    </h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="trans_id"><strong>Trans ID</strong></label>
                                    <input v-model="filterForm.transId" id="trans_id" type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="account_no"><strong>Account Number</strong></label>
                                    <input v-model="filterForm.accountNo" id="account_no" type="text" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="msisdn"><strong>Msisdn/ Phone</strong></label>
                                    <input v-model="filterForm.msisdn" id="msisdn" type="text" class="form-control">
                                </div>
                            </div>
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
                                <div class="col-md-6">
                                    <button v-if="filterForm.processing" class="btn btn-warning btn-block" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                    </button>
                                    <button v-else @click.prevent="loadTransactions()" type="button" class="btn btn-primary btn-block"><i class="fa fa-search"></i></button>
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
            <div class="col-lg-10 col-xl-10 col-md-10 col-sm-10 mx-auto">
                <div class="header">
                    <h1>
                        Transactions History
                    </h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-responsive table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Channel</th>
                                    <th>Payer Name</th>
                                    <th>Msisdn/ phone</th>
                                    <th>Unit/ Room Number</th>
                                    <th>Transaction ID</th>
                                    <th>Transaction Time</th>
                                    <th>Amount (KES)</th>
                                    <th>Account Number</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="transaction in transactions.data" :key="transaction.uuid">
                                    <td>
                                        <p v-if="transaction.channel === 1">M-PESA</p>
                                        <p v-else-if="transaction.channel === 2">CARD</p>
                                        <p v-else-if="transaction.channel === 3">INVOICE</p>
                                        <p v-else>PAYPAL</p>
                                    </td>
                                    <td>
                                        <p>{{ transaction.name }}</p>
                                    </td>
                                    <td>
                                        <p>{{ transaction.msisdn }}</p>
                                    </td>
                                    <td>
                                        <p>{{ transaction.unit.label }}</p>
                                    </td>
                                    <td>
                                        <p>{{ transaction.trans_id }}</p>
                                    </td>
                                    <td>
                                        <p>{{ transaction.trans_time }}</p>
                                    </td>
                                    <td>
                                        <p>{{ transaction.amount }}</p>
                                    </td>
                                    <td>
                                        <p>{{ transaction.account_no }}</p>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <Bootstrap4Pagination @limit=5 :data="transactions" @pagination-change-page="loadTransactions"/>
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
    name: "TenantTransactions",
    components : {Bootstrap4Pagination},
    data() {
        return {
            transactions: {},
            filterForm: {
                transId: undefined,
                accountNo: undefined,
                msisdn: undefined,
                start: undefined,
                end: undefined,
                processing: false
            }
        }
    },
    mounted() {
        this.loadTransactions();
    },
    methods: {
        loadTransactions(page = 1){
            const payLoad = {
                trans_id : this.filterForm.transId,
                account_no : this.filterForm.accountNo,
                msisdn : this.filterForm.msisdn,
                start : this.filterForm.start,
                end : this.filterForm.end,
                sort_by : 'latest'
            };
            this.filterForm.processing = true;
            axios.post(`/tenant/units/tenant-transactions/load?page=${page}`, payLoad).then(response => {
                this.transactions = response.data;
            }).catch(error => {
                //
            }).finally(() => {
                this.filterForm.processing = false;
            });
        },
        clearFilter(){
            this.filterForm.transId = undefined;
            this.filterForm.accountNo = undefined;
            this.filterForm.msisdn = undefined;
            this.filterForm.start = undefined;
            this.filterForm.end = undefined;
            this.loadTransactions();
        }
    }
}

</script>
