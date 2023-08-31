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
                    <div class="card-header">
                        <button @click.prevent="showTopUpWalletModal()" class="btn btn-success float-left">Top-Up Wallet Via M-pesa</button>
                        <button class="btn btn-success ml-5 float-left">Withdraw To M-pesa</button>
                        <h3 class="float-right">Balance : KES {{ companyMetadata.walletBalance }}</h3>
                    </div>
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
                                <div class="col-md-12">
                                    <label for="trans_id"><strong>Transaction ID</strong></label>
                                    <input v-model="filterForm.transId" id="trans_id" type="text" class="form-control">
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
            <div class="col-lg-12 col-xl-12 col-md-12 col-sm-12">
                <div class="header">
                    <h1>
                        Wallet Transactions History
                    </h1>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-responsive table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Details</th>
                                    <th>Transaction ID</th>
                                    <th>Amount (KES)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="transaction in transactions.data" :key="transaction.uuid">
                                    <td>
                                        <p>{{ transaction.date }}</p>
                                    </td>
                                    <td>
                                        <p>{{ transaction.details }}</p>
                                    </td>
                                    <td>
                                        <p>{{ transaction.trans_id }}</p>
                                    </td>
                                    <td>
                                        <p>{{ transaction.amount }}</p>
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
        <!-- Top Up Wallet Modal -->
        <div class="modal fade" id="topUpWalletModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Top-Up Wallet Via M-pesa</h4>
                        <img height="120" width="200" :src="'/ui-kit/img/brands/mpesa.png'" alt="Mpesa logo">
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <form>
                                    <div class="form-group row">
                                        <div class="col-md-12">
                                            <label for="account_no"><strong>Account Number</strong></label>
                                            <input v-model="topUpWalletForm.companyWalletAccountNo" id="account_no" disabled type="text" class="form-control">
                                        </div>
                                        <div class="col-md-12">
                                            <label for="msisdn"><strong>Msisdn/ Phone</strong></label>
                                            <input v-model="topUpWalletForm.msisdn" id="msisdn" type="text" class="form-control">
                                        </div>
                                        <div class="col-md-12">
                                            <label for="amount"><strong>Amount (KES)</strong></label>
                                            <input v-model="topUpWalletForm.amount" id="amount" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <button v-if="topUpWalletForm.processing" class="btn btn-warning btn-block" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Processing...
                                    </button>
                                    <button v-else @click.prevent="topUpWallet()" type="button" class="btn btn-success btn-block">Top-Up Now</button>
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
    name: "WalletTransactions",
    props: ['company_uuid'],
    components : {Bootstrap4Pagination},
    data() {
        return {
            transactions: {},
            filterForm: {
                start: undefined,
                end: undefined,
                transId: undefined,
                processing: false
            },
            companyMetadata: {
                companyUuid: undefined,
                walletBalance: undefined,
                walletAccountNo: undefined
            },
            topUpWalletForm: {
                companyUuid: undefined,
                companyWalletAccountNo: undefined,
                amount: undefined,
                msisdn: undefined,
                processing: false
            }
        }
    },
    mounted() {
        this.loadCompany(this.company_uuid);
        this.loadTransactions();
    },
    methods: {
        loadCompany(uuid){
            const payLoad = {
                sort_by : 'latest'
            };
            this.filterForm.processing = true;
            axios.post(`/company/${uuid}/load`, payLoad).then(response => {
                this.companyMetadata.companyUuid = response.data.uuid;
                this.companyMetadata.walletBalance = response.data.wallet_balance;
                this.companyMetadata.walletAccountNo = response.data.wallet_account_no;
            }).catch(error => {
                //
            }).finally(() => {
                this.filterForm.processing = false;
            });
        },
        loadTransactions(page = 1){
            const payLoad = {
                start : this.filterForm.start,
                end : this.filterForm.end,
                trans_id : this.filterForm.transId,
                sort_by : 'latest'
            };
            this.filterForm.processing = true;
            axios.post(`/wallet-transactions/load-for-company/load?page=${page}`, payLoad).then(response => {
                this.transactions = response.data;
            }).catch(error => {
                //
            }).finally(() => {
                this.filterForm.processing = false;
            });
        },
        clearFilter(){
            this.filterForm.start = undefined;
            this.filterForm.end = undefined;
            this.filterForm.transId = undefined;
            this.loadTransactions();
        },
        showTopUpWalletModal(){
            this.topUpWalletForm.companyUuid = this.companyMetadata.companyUuid;
            this.topUpWalletForm.companyWalletAccountNo = this.companyMetadata.walletAccountNo;
            $('#topUpWalletModal').modal('show');
        },
        topUpWallet(){
            const payLoad = {
                msisdn: this.topUpWalletForm.msisdn,
                amount: this.topUpWalletForm.amount
            };
            this.topUpWalletForm.processing = true;
            axios.post(`/wallet-transactions/top-up/wallet/${this.topUpWalletForm.companyUuid}`, payLoad).then((response) => {
                if (response.data.status){
                    $('#topUpWalletModal').modal('hide');
                    Swal.fire('Success', response.data.message, 'success');
                } else {
                    Swal.fire('Error', response.data.message, 'warning');
                }
                this.loadTransactions();
            }).catch((error) => {
                if (error.response && error.response.status === 422) {
                    const errorValues = Object.values(error.response.data.errors).flatMap(arr => arr);
                    Swal.fire('Error!', JSON.stringify(errorValues.map(errorMessage => errorMessage)), 'error');
                } else {
                    Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                }
            }).finally(() => {
                this.topUpWalletForm.processing = false;
            });
        }
    }
}

</script>
