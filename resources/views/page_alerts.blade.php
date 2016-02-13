<div id='alerts' class='row'>
    @if(count($errors) > 0)
    <div class='alert-box warning'>
        <span class='alert-icon'><i class='alert-icon fa fa-warning'></i></span>
        <span class='alert-text'>Ada beberapa kesalahan dalam pengisian data.</span>
        <p>
            <ul>
            @foreach ($errors->all() as $error)
            <li>- {{ $error }}</li>
            @endforeach
            </ul>
        </p>
    </div>
    @endif
    @if(Session::has('message'))
    <div class='alert-box success'>
        <span class='alert-text'>{{ Session::get('message') }}</span>
    </div>
    @endif
</div>