@if (Session::get('success'))
    <div class="callout callout-success">
        <p>{{ Session::get('success') }}</p>
    </div>
@elseif (Session::get('warning'))
    <div class="callout callout-warning">
        <p>{{ Session::get('warning') }}</p>
    </div>
@elseif (Session::get('error'))
    <div class="callout callout-danger">
        <p>{{ Session::get('error') }}</p>
    </div>
@endif
