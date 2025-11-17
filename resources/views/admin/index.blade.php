@extends('layouts.admin')

@section('title', 'Home')

@section('content')

    <h2>Clients</h2>
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
    <table class="table">
        <thead>
            <tr>
                <th id="th_clientname">Client name</th>
                <th>Client id</th>
                <th>Permissions</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($data as $client)
            <tr>
                <td><a href="{{$client->clienturl}}">{{ $client->clientname }}</a></td>
                <td>{{ $client->clientid }}</td>
                <td>
                    @if (isset($client->permissions) && count($client->permissions) > 0)
                        @foreach($client->permissions as $permission)
                            <span class="badge badge-dark">{{ $permission->name }}</span>&#32;
                        @endforeach
                    @else
                        <span class="text-muted">No permissions</span>
                    @endif
                </td>
                <td>
                    <a class="btn btn-secondary btn-sm" href="{{ url('admin/client/' . $client->id . '/delete' ) }}" role="button">Delete</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <p>
        <a class="btn btn-primary" href="{{ url('admin/client/add') }}">Add new client</a>
    </p>
@endsection