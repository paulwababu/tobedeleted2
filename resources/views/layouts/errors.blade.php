@if (count($errors))
    <div class="alert alert-danger alert-dismissible" role="alert">
        <div class="alert-icon">
            <i class="far fa-fw fa-bell"></i>
        </div>
        <div class="alert-message">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
