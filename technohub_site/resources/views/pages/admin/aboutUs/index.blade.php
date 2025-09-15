@extends('layouts.admin.theme')
@section('content')
<!-- Breadcrump -->
<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
    <div class="my-auto mb-2">
        <h2 class="mb-1">About Us</h2>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="card-title">About Us Page</h4>
                    </div>
                   @if(blank($aboutUs))
                        <div class="col-md-6 text-end">
                            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#add_modal">Add record</button>
                        </div>
                   @endif
                </div>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table datatable ">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image 1</th>
                                <th>Image 2</th>
                                <th>Title</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!blank($aboutUs))
                                <tr>
                                    <td>1</td>
                                    <td>
                                        <img src="/storage/{{$aboutUs->img_1}}" width="100px" alt="">
                                    </td>
                                    <td>
                                        <img src="/storage/{{$aboutUs->img_2}}" width="100px" alt="">
                                    </td>
                                    <td>
                                        {{$aboutUs->title}}
                                    </td>
                                    <td>
                                        {{date('Y-m-d', strtotime($aboutUs->created_at))}}
                                    </td>
                                    <td>
                                        <div class="action-icon d-inline-flex">
                                            <a href="#" class="me-2" data-bs-toggle="modal" data-bs-target="#edit_modal" onclick="openUpdateModal({{$aboutUs->id}})">
                                                <i class="ti ti-edit"></i>
                                            </a>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#delete_modal" onclick="openDeleteModal({{$aboutUs->id}})">
                                                <i class="ti ti-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Breadcrump -->
@include('pages.admin.aboutUs.modals')

<script>
    function openUpdateModal(recordId) {
        fetch(`/dashboard/aboutUs/getById/${recordId}`, {
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
</script>
@endsection

