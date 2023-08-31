/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp({});

/**
 * Dashboards
 */
import AdminDashboardComponent from './components/admin/AdminDashboard.vue'; // admin
import OwnerDashboardComponent from './components/OwnerDashboard.vue';
import TenantDashboardComponent from './components/TenantDashboard.vue';
import AgentDashboardComponent from './components/AgentDashboard.vue';

// import ExampleComponent from './components/ExampleComponent.vue';
import ManagePropertiesComponent from './components/ManageProperties.vue';
import ManageUnitsComponent from './components/ManageUnits.vue';
import ManageTenantsComponent from './components/ManageTenants.vue';
import BrowseUnitsComponent from './components/BrowseUnits.vue';
import MyResidencesComponent from './components/MyResidences.vue';
import TenantTransactionsComponent from './components/TenantTransactions.vue';
import UpdateCompanyComponent from './components/UpdateCompany.vue';
import TenantRentalPaymentsComponent from './components/TenantRentalPayments.vue';
import ManageInquiriesComponent from './components/ManageInquiries.vue';
import ManageOwnerAgentsComponent from './components/owner/ManageAgents.vue'; // owner
import OwnerRentalPaymentsComponent from './components/owner/RentalPayments.vue'; // owner
import WalletTransactionsComponent from './components/WalletTransactions.vue';

/**
 * messages/sms
 */
import SendSingleSmsComponent from './components/sms/SendSingle.vue';
import OutgoingSmsComponent from './components/sms/Outgoing.vue';

// app.component('example-component', ExampleComponent);
/**
 * Dashboard
 */
app.component('admin-dashboard', AdminDashboardComponent); // admin
app.component('owner-dashboard', OwnerDashboardComponent);
app.component('tenant-dashboard', TenantDashboardComponent);
app.component('agent-dashboard', AgentDashboardComponent);

app.component('manage-properties', ManagePropertiesComponent);
app.component('manage-units', ManageUnitsComponent);
app.component('manage-tenants', ManageTenantsComponent);
app.component('browse-units', BrowseUnitsComponent);
app.component('my-residences', MyResidencesComponent);
app.component('tenant-transactions', TenantTransactionsComponent);
app.component('update-company', UpdateCompanyComponent);
app.component('tenant-rental-payments', TenantRentalPaymentsComponent);
app.component('manage-inquiries', ManageInquiriesComponent);
app.component('manage-owner-agents', ManageOwnerAgentsComponent); // owner
app.component('owner-rental-payments', OwnerRentalPaymentsComponent); // owner/ agent
app.component('wallet-transactions', WalletTransactionsComponent);

/**
 * messages/sms
 */
app.component('send-single-sms', SendSingleSmsComponent);
app.component('outgoing-sms', OutgoingSmsComponent);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

app.mount('#app');
