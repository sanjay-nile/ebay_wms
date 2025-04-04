@if($errors->all())
    <div class="box no-border" id="successMessage">
        <div class="box-tools">
            <div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <ul>
                    @foreach($errors->all() as $message)
                        <li>{{ $message }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@elseif(session()->has('message'))
    <div class="box no-border" id="successMessage">
        <div class="box-tools">
            <p class="alert alert-success alert-dismissible">
                {{ session()->get('message') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </p>
        </div>
    </div>
@elseif(session()->has('error'))
    <div class="box no-border" id="successMessage">
        <div class="box-tools">
            <p class="alert alert-danger alert-dismissible">
                {{ session()->get('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </p>
        </div>
    </div>
@endif

@if (session()->has('success_message'))
    <div class="alert alert-success alert-dismissible" id="successMessage">
        {{ session()->get('success_message') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
@endif

@if (session()->has('success'))
    <div class="alert alert-success alert-dismissible" id="successMessage">
        {{ session()->get('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
@endif

@if (session()->has('error_message'))
    <div class="alert alert-danger alert-dismissible" id="successMessage">
        {{ session()->get('error_message') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
@endif