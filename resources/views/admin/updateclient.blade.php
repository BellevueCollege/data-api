@extends('layouts.admin')

@section('title', 'Edit Client')

@section('content')

    <h1>Edit Client</h1>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-warning">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ url('admin/client/' . $client->id . '/update' ) }}" method="post">
        @csrf
        @method('PUT')
        <div class="mb-3">
        <label for="clientname" class="form-label">Client name</label>
        <input type="text" class="form-control" name="clientname" id="clientname" 
                value="{{ old('clientname', $client->clientname) }}" required>
    </div>
    
    <div class="mb-3">
        <label for="clienturl" class="form-label">Client URL</label>
        <input type="url" class="form-control" name="clienturl" id="clienturl"
                value="{{ old('clienturl', $client->clienturl) }}" required>
    </div>
    
    <div class="mb-3">
        <fieldset>
            <legend>Permissions</legend>
            @if (isset($permissions) && count($permissions) > 0)
                @foreach($permissions as $permissionName => $permissionDescription)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" 
                                name="permissions[]" 
                                value="{{ $permissionName }}" 
                                id="permission-{{ $permissionName }}"
                                @if(in_array($permissionName, $client->permissions ?? [] )) checked @endif>
                        <label class="form-check-label" for="permission-{{ $permissionName }}">
                            <strong>{{ $permissionName }}</strong>: {{ $permissionDescription }}
                        </label>
                    </div>
                @endforeach
            @endif
        </fieldset>
    </div>
    
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Update Client</button>
        <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>


@endsection