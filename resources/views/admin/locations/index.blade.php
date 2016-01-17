@extends('layouts.admin')

@section('title')
    Location List
@endsection

@section('content')
<div class="col-md-12">
    <ul class="breadcrumb">
        <li><a href="/admin">Admin Control</a></li>
        <li class="active">Locations</li>
    </ul>
    <h3>All Locations</h3><hr />
    <table class="table table-bordered table-hover table-striped">
        <thead>
            <tr>
                <th>Location</th>
                <th>Description</th>
                <th class="text-center">Nodes</th>
                <th class="text-center">Servers</th>
                <th class="text-center"></th>
                <th class="text-center"></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($locations as $location)
                <tr>
                    <td><code>{{ $location->short }}</code></td>
                    <td>{{ $location->long }}</td>
                    <td class="text-center">{{ $location->a_nodeCount }}</td>
                    <td class="text-center">{{ $location->a_serverCount }}</td>
                    <td class="text-center"><a href="#edit"><i class="fa fa-wrench" data-toggle="modal" data-target="#editModal" data-action="edit" data-id="{{ $location->id }}" data-short="{{ $location->short }}" data-long="{{ $location->long }}"></i></a></td>
                    <td class="text-center"><a href="#delete" class="text-danger" data-action="delete" data-id="{{ $location->id }}"><i class="fa fa-trash-o"></i></a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="row">
        <div class="col-md-12 text-center">{!! $locations->render() !!}</div>
    </div>
    <div class="well">
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-sm btn-success" id="addNewLocation">Add New Location</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Editing Location</h4>
            </div>
            <form action="{{ route('admin.locations') }}" method="POST" id="editLocationForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="location-short" class="control-label">Location Code:</label>
                        <input type="text" class="form-control" id="location-short">
                    </div>
                    <div class="form-group">
                        <label for="location-long" class="control-label">Description:</label>
                        <input type="text" class="form-control" id="location-long">
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="location-id">
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-sm btn-primary">Edit Location</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
$(document).ready(function () {
    $('#sidebar_links').find("a[href='/admin/locations']").addClass('active');
    $('#editModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var short = button.data('short');
        var long = button.data('long');
        var id = button.data('id');
        var modal = $(this);

        modal.find('#location-id').val(id);
        modal.find('#location-short').val(short);
        modal.find('#location-long').val(long);
    });
    $('#editLocationForm').submit(function (event) {
        event.preventDefault();
        $.ajax({
            method: 'PATCH',
            url: '{{ route('admin.locations') }}/' + $('#location-id').val(),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: {
                short: $('#location-short').val(),
                long: $('#location-long').val()
            }
        }).done(function (data) {
            swal({
                type: 'success',
                title: '',
                text: 'Successfully updated location information.',
                closeOnConfirm: false,
                showLoaderOnConfirm: true
            }, function () {
                window.location = '{{ route('admin.locations') }}';
            });
        }).fail(function (jqXHR) {
            console.error(jqXHR);
            swal({
                type: 'error',
                title: 'Whoops!',
                text: (typeof jqXHR.responseJSON.error !== 'undefined') ? jqXHR.responseJSON.error : 'An error occured while processing this request.'
            });
        });
    });
    $('[data-action="delete"]').click(function (event) {
        event.preventDefault();
        var self = $(this);
        swal({
            type: 'warning',
            title: '',
            text: 'Do you really want to delete this location?',
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, function () {
            $.ajax({
                method: 'DELETE',
                url: '{{ route('admin.locations') }}/' + self.data('id'),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).done(function () {
                swal({
                    type: 'success',
                    title: '',
                    text: 'Location was successfully deleted.'
                });
                self.parent().parent().slideUp();
            }).fail(function (jqXHR) {
                console.error(jqXHR);
                swal({
                    type: 'error',
                    title: 'Whoops!',
                    text: (typeof jqXHR.responseJSON.error !== 'undefined') ? jqXHR.responseJSON.error : 'An error occured while processing this request.'
                });
            });
        });
    });
});
</script>
@endsection
