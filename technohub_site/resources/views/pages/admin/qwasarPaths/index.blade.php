@extends('layouts.admin.theme')
@section('content')
<!-- Breadcrump -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">Qwasar Paths</h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title">Qwasar Paths Page</h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#add_modal">Add record</button>
                    </div>
                </div>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table datatable ">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!blank($paths))
                                @foreach($paths as $item)
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <img src="/storage/{{$item->img}}" width="100px" alt="">
                                        </td>
                                        <td>
                                            {{$item->title}}
                                        </td>
                                        <td>
                                            <div class="col-md-4">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           role="switch"
                                                           id="flexSwitchCheckChecked"
                                                            <?= $item->status == 1 ? 'checked' : ''?>
                                                            onclick="changeStatus({{$item->id}},<?= $item->status == 1 ? 0 : 1?>)"
                                                    >
                                                    <label class="form-check-label" for="flexSwitchCheckChecked">Active</label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{date('Y-m-d', strtotime($item->created_at))}}
                                        </td>
                                        <td>
                                            <div class="action-icon d-inline-flex">
                                                <a href="#" class="me-2" data-bs-toggle="modal" data-bs-target="#edit_modal" onclick="openUpdateModal({{$item->id}})">
                                                    <i class="ti ti-edit"></i>
                                                </a>
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#delete_modal" onclick="openDeleteModal({{$item->id}})">
                                                    <i class="ti ti-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Breadcrump -->
@include('pages.admin.qwasarPaths.modals')

<script>
    function openUpdateModal(recordId) {
        fetch(`/dashboard/qwasarPaths/getById/${recordId}`, {
            method: 'GET',
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const record = data.data;
                const form = document.getElementById('updateForm');

                for (const field in record) {
                    if (record.hasOwnProperty(field) && form[field]) {
                        if (form[field].type !== 'file') {
                            form[field].value = record[field] || '';
                        }
                    }
                }

                // Если поле description есть в данных, вставляем его в Summernote
                if (record.hasOwnProperty('description')) {
                    $('#description').summernote('code', record.description || '');
                }
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while fetching the record.');
        });
    }

    function openDeleteModal(recordId){
        $("#deleteID").val(recordId)
    }

    function changeStatus(id, status){
        fetch(`/dashboard/qwasarPaths/changeStatus/${id}/${status}`, {
            method: 'GET',
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while fetching the record.');
            });
    }
</script>
@endsection

