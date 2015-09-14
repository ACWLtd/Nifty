@if ( $errors->any() )
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong>Please check the errors below</strong>
        <ul>
            @foreach( $errors->all() as $message )
                <li>{!! $message !!}</li>
            @endforeach
        </ul>
    </div>
@endif