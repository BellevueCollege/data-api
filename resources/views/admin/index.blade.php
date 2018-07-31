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
                <th></th>
            </tr>
        </thead>
        <tbody>
        @foreach ($data as $client)
            <tr>
                <td><a href="{{$client->clienturl}}">{{ $client->clientname }}</a></td>
                <td>{{ $client->clientid }}</td>
                <td>
                    <a class="btn btn-secondary btn-sm" href="{{route('admin.client.delete', [ 'id' => $client->id ])}}" role="button">Delete</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <p>
        <a class="btn btn-primary" href="{{route('admin.client.add')}}">Add new client</a>
    </p>
@endsection