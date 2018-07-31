@extends('layouts.admin')

@section('title', 'Add Client')

@section('content')

    <h2>Add Client</h2>
    @if ($success)
        <div class="alert alert-success">
            New client {{$clientname}} added successfully. The client will use the following ID and key to authenticate against the API. Note 
            the key now as it is not retrievable.
            <ul class="mb-0 mt-1">
                <li>Client ID: {{ $clientid }}</li>
                <li>Client key: {{ $clientkey }}</li>
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

    <form action="{{url('admin/client/add')}}" method="post">
        @csrf
        <div class="form-group">
            <label for="clientname">Client name</label>
            <input type="" class="form-control" name="clientname" id="clientname">
        </div>
        <div class="form-group">
            <label for="clienturl">Client URL</label>
            <input type="url" class="form-control" name="clienturl" id="clienturl">
        </div>
        <button type="submit" class="btn btn-primary">Add client</button>
    </form>

@endsection