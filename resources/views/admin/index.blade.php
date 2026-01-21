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
    <div class="table-responsive">
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
                            @foreach($client->permissions as $permission )
                                <span class="badge text-bg-secondary">{{ $permission }}</span>&#32;
                            @endforeach
                        @else
                            <span class="text-muted">No permissions</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group" role="group" aria-label="Edit client {{ $client->clientname }}">
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.client.update', $client->id) }}">Edit</a>
                            <a class="btn btn-danger btn-sm delete-client" data-url="{{ route('admin.client.delete', $client->id) }}" href="#">Delete</a>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <p>
        <a class="btn btn-primary" href="{{ url('admin/client/add') }}">Add new client</a>
    </p>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="deleteConfirmationModalLabel">Confirm Deletion</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this client? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this script after your Bootstrap JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
            const deleteForm = document.getElementById('deleteForm');
            
            document.querySelectorAll('.delete-client').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('data-url');
                    deleteForm.action = url;
                    deleteModal.show();
                });
            });
        });
    </script>
@endsection