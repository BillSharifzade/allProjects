<div class="modal fade" id="add_modal" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <h4 class="modal-title me-2">Create Record</h4>
                </div>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form action="{{route('aboutQwasar.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body pb-0 ">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Title <span class="text-danger"> *</span></label>
                                <input type="text" class="form-control" name="title">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="summernote"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Video</label>
                                <input type="file" class="form-control" name="video">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light border me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_modal" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <h4 class="modal-title me-2">Edit Record</h4>
                </div>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form action="{{route('aboutQwasar.update')}}" id="updateForm" method="POST" enctype="multipart/form-data">
                @method('PUT')
                @csrf
                <input type="hidden" name="id" value="" id="updateID">
                <div class="modal-body pb-0 ">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Title <span class="text-danger"> *</span></label>
                                <input type="text" class="form-control" name="title" id="title">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" id="description" class="summernote"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Video</label>
                                <input type="file" class="form-control" name="video">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light border me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="delete_modal" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <h4 class="modal-title me-2">Delete Record</h4>
                </div>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <form action="{{route('aboutQwasar.delete')}}" id="updateForm" method="POST" enctype="multipart/form-data">
                @method('DELETE')
                @csrf
                <input type="hidden" name="id" value="" id="deleteID">
                <div class="modal-body pb-2 ">
                    <h5>Are you sure you want to delete this record?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light border me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Delete </button>
                </div>
            </form>
        </div>
    </div>
</div>

