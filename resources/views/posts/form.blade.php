 {!! Form::open(['route' => 'posts.store', 'files' => true]) !!}
    <div class="form-group">
        {!! Form::textarea('content', old('content'), ['class' => 'form-control', 'rows' => '3']) !!}
    </div>
    <div class="form-group">
        {!! Form::file('image', ['class'=>'form-control-file input-group-prepend']) !!}
    </div>
        {!! Form::submit('Post', ['class' => 'btn btn-primary btn-block']) !!}
{!! Form::close() !!}

    