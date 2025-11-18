@extends('layouts.admin')

@section('title', 'Add Client')

@section('content')

    <h1>Add Client</h1>
    @if ( !empty($success) && $success )
        <div class="alert alert-success">
            New client {{$clientname}} added successfully. The client will use the following ID and key to authenticate against the API. Note 
            the key now as it is not retrievable.
            <ul class="mb-0 mt-1">
                <li><label for="client-id">Client ID:</label> <input id="client-id" class="form-control" type="text" value="{{ $clientid }}" readonly></li>
                <li><label for="client-key">Client key:</label> <input id="client-key" class="form-control" type="text" value="{{ $clientkey }}" readonly></li>
            </ul>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-warning text-left">
            @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif

    @if ( empty($success) ) 

        <form action="{{url('admin/client/add')}}" method="post">
            @csrf
            <div class="mb-3">
                <label for="clientname" class="form-label">Client name</label>
                <input type="" class="form-control" name="clientname" id="clientname">
            </div>
            <div class="mb-3">
                <label for="clienturl" class="form-label">Client URL</label>
                <input type="url" class="form-control" name="clienturl" id="clienturl">
            </div>
            <div class="mb-3">
                <fieldset>
                    <legend>Permissions</legend>
                    <!-- Add permission checkboxes here -->
                    @if (isset($permissions) && count($permissions) > 0)
                        @foreach($permissions as $permission)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="permission-{{ $permission->name }}">
                                <label class="form-check-label" for="permission-{{ $permission->name }}">
                                    <strong>{{ $permission->name }}</strong>: {{ $permission->description }}
                                </label>
                            </div>
                        @endforeach
                    @endif
                </fieldset>
            </div>
            <button type="submit" class="btn btn-primary">Add client</button>
        </form>
    @endif

@endsection