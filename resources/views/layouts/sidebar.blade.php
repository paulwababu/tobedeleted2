<nav id="sidebar" class="sidebar">
    <a class="sidebar-brand" href="{{ route('user.dashboard-page') }}">
        <svg>
            <use xlink:href="#ion-ios-pulse-strong"></use>
        </svg>
        Spark
    </a>
    <div class="sidebar-content">
        <div class="sidebar-user">
            <img src="{{ asset('ui-kit/img/logo.jpg') }}" class="img-fluid rounded-circle mb-2" alt="Linda Miller" />
            <div class="font-weight-bold">{{ Auth::user()->name }}</div>
            <small>{{ Auth::user()->email }}</small>
        </div>

        <ul class="sidebar-nav">
            @role('owner|agent|admin|tenant')
            <li class="sidebar-item active">
                <a href="{{ route('user.dashboard-page') }}" class="sidebar-link">
                    <i class="align-middle mr-2 fas fa-fw fa-home"></i> <span class="align-middle">Home</span>
                </a>
            </li>
            @endrole
            @role('tenant')
{{--            <li class="sidebar-item">--}}
{{--                <a href="{{ route('unit.browse-page') }}" class="sidebar-link">--}}
{{--                    <i class="align-middle mr-2 fas fa-fw fa-building"></i> <span class="align-middle">Browse Properties/ Units</span>--}}
{{--                </a>--}}
{{--            </li>--}}
            <li class="sidebar-item">
                <a href="{{ route('tenant.my-residences-page') }}" class="sidebar-link">
                    <i class="align-middle mr-2 fas fa-fw fa-house-user"></i> <span class="align-middle">Residences</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="#payments" data-toggle="collapse" class="sidebar-link collapsed">
                    <i class="align-middle mr-2 fas fa-fw fa-hand-holding-usd"></i> <span class="align-middle">Manage Payments</span>
                </a>
                <ul id="payments" class="sidebar-dropdown list-unstyled collapse" data-parent="#sidebar">
                    <li class="sidebar-item"><a class="sidebar-link" href="{{ route('tenant.manage-rental-payments-page') }}">Rental Payments </a></li>
                    <li class="sidebar-item"><a class="sidebar-link" href="{{ route('tenant.transactions-page') }}">All Transactions </a></li>
                </ul>
            </li>
            @endrole
            @role('owner|agent|admin|tenant')
            <li class="sidebar-item">
                <a href="{{ route('wallet-transaction.manage-page') }}" class="sidebar-link">
                    <i class="align-middle mr-2 fas fa-fw fa-wallet"></i> <span class="align-middle">Open/ Manage Wallet</span>
                </a>
            </li>
            @endrole
            @role('owner|agent|admin')
            <li class="sidebar-item">
                <a href="#properties" data-toggle="collapse" class="sidebar-link collapsed">
                    <i class="align-middle mr-2 fas fa-fw fa-building"></i> <span class="align-middle">Manage Properties</span>
                </a>
                <ul id="properties" class="sidebar-dropdown list-unstyled collapse" data-parent="#sidebar">
                    <li class="sidebar-item"><a class="sidebar-link" href="{{ route('property.manage-page') }}">Create Properties & Units </a></li>
                </ul>
            </li>
            <li class="sidebar-item">
                <a href="{{ route('tenant.manage-page') }}" class="sidebar-link">
                    <i class="align-middle mr-2 fas fa-fw fa-users"></i> <span class="align-middle">Tenants (Check In/ Out)</span>
                </a>
            </li>
            @endrole
            @role('owner')
            <li class="sidebar-item">
                <a href="{{ route('agent.manage-owner-agents-page') }}" class="sidebar-link">
                    <i class="align-middle mr-2 fas fa-fw fa-user"></i> <span class="align-middle">Manage Agents</span>
                </a>
            </li>
            @endrole
            @role('owner|agent')
            <li class="sidebar-item">
                <a href="{{ route('owner.rental-payments-page') }}" class="sidebar-link">
                    <i class="align-middle mr-2 fas fa-fw fa-hand-holding-usd"></i> <span class="align-middle">Rental Payments</span>
                </a>
            </li>
            @endrole
            @role('owner|agent|admin')
            <li class="sidebar-item">
                <a href="{{ route('inquiry.manage-page') }}" class="sidebar-link">
                    <i class="align-middle mr-2 fas fa-fw fa-question"></i> <span class="align-middle">Property Inquiries</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a href="#company" data-toggle="collapse" class="sidebar-link collapsed">
                    <i class="align-middle mr-2 fas fa-fw fa-edit"></i> <span class="align-middle">Manage Company</span>
                </a>
                <ul id="company" class="sidebar-dropdown list-unstyled collapse" data-parent="#sidebar">
                    <li class="sidebar-item"><a class="sidebar-link" href="{{ route('company.update-page') }}">Update Info </a></li>
                </ul>
            </li>
            @endrole
            @role('owner|agent|admin')
            <li class="sidebar-item">
                <a href="#sms" data-toggle="collapse" class="sidebar-link collapsed">
                    <i class="align-middle mr-2 fas fa-fw fa-sms"></i> <span class="align-middle">Send SMS's</span>
                </a>
                <ul id="sms" class="sidebar-dropdown list-unstyled collapse" data-parent="#sidebar">
                    <li class="sidebar-item"><a class="sidebar-link" href="{{ route('message.send-single-page') }}">Single SMS</a></li>
                    <li class="sidebar-item"><a class="sidebar-link" href="{{ route('message.outgoing-sms-page') }}">SMS Report/ History </a></li>
                </ul>
            </li>
            @endrole
            @role('admin')
            <li class="sidebar-item">
                <a href="#admin" data-toggle="collapse" class="sidebar-link collapsed">
                    <i class="align-middle mr-2 fas fa-fw fa-user-shield"></i> <span class="align-middle">Administration</span>
                </a>
                <ul id="admin" class="sidebar-dropdown list-unstyled collapse" data-parent="#sidebar">
                    <li class="sidebar-item"><a class="sidebar-link" href="#"><i class="align-middle mr-2 fas fa-fw fa-user-friends"></i> Companies</a></li>
                    <li class="sidebar-item"><a class="sidebar-link" href="#"><i class="align-middle mr-2 fas fa-fw fa-building"></i> Properties</a></li>
                    <li class="sidebar-item"><a class="sidebar-link" href="#"><i class="align-middle mr-2 fas fa-fw fa-code-branch"></i> Units</a></li>
                    <li class="sidebar-item"><a class="sidebar-link" href="#"><i class="align-middle mr-2 fas fa-fw fa-users"></i> Tenants</a></li>
                    <li class="sidebar-item"><a class="sidebar-link" href="#"><i class="align-middle mr-2 fas fa-fw fa-user"></i> Owners</a></li>
                    <li class="sidebar-item"><a class="sidebar-link" href="#"><i class="align-middle mr-2 fas fa-fw fa-credit-card"></i> Transactions</a></li>
                    <li class="sidebar-item"><a class="sidebar-link" href="#"><i class="align-middle mr-2 fas fa-fw fa-wallet"></i> Wallet Transactions</a></li>
                    <li class="sidebar-item"><a class="sidebar-link" href="#"><i class="align-middle mr-2 fas fa-fw fa-hand-holding-usd"></i> Rental Payments</a></li>
                    <li class="sidebar-item"><a class="sidebar-link" href="#"><i class="align-middle mr-2 fas fa-fw fa-bed"></i> Units Types</a></li>
                    <li class="sidebar-item"><a class="sidebar-link" href="#"><i class="align-middle mr-2 fas fa-fw fa-globe-africa"></i> Counties</a></li>
                </ul>
            </li>
            @endrole
        </ul>
    </div>
</nav>
