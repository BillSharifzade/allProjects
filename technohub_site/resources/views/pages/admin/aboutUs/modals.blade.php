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
            <form action="{{route('aboutUs.store')}}" method="POST" enctype="multipart/form-data">
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
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Image 1</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="custom-file-container" data-upload-id="myFirstImage">
                                            <label>Upload
                                                <a href="javascript:void(0)" class="custom-file-container__image-clear" title="Clear Image">x</a>
                                            </label>
                                            <label class="custom-file-container__custom-file">
                                                <input type="file" name="img_1"
                                                       class="custom-file-container__custom-file__custom-file-input">
                                                <input type="hidden" name="MAX_FILE_SIZE" value="10485760">
                                                <span class="custom-file-container__custom-file__custom-file-control"></span>
                                            </label>
                                            <div class="custom-file-container__image-preview"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Image 2</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="custom-file-container" data-upload-id="mySecondImage">
                                            <label>Upload
                                                <a href="javascript:void(0)" class="custom-file-container__image-clear" title="Clear Image">x</a>
                                            </label>
                                            <label class="custom-file-container__custom-file">
                                                <input type="file" name="img_2"
                                                       class="custom-file-container__custom-file__custom-file-input">
                                                <input type="hidden" name="MAX_FILE_SIZE" value="10485760">
                                                <span class="custom-file-container__custom-file__custom-file-control"></span>
                                            </label>
                                            <div class="custom-file-container__image-preview"></div>
                                        </div>
                                    </div>
                                </div>
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
            <form action="{{route('aboutUs.update')}}" id="updateForm" method="POST" enctype="multipart/form-data">
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
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Image 1</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="custom-file-container" data-upload-id="myFirstImageEdit">
                                            <label>Upload
                                                <a href="javascript:void(0)" class="custom-file-container__image-clear" title="Clear Image">x</a>
                                            </label>
                                            <label class="custom-file-container__custom-file">
                                                <input type="file" name="img_1"
                                                       class="custom-file-container__custom-file__custom-file-input">
                                                <input type="hidden" name="MAX_FILE_SIZE" value="10485760">
                                                <span class="custom-file-container__custom-file__custom-file-control"></span>
                                            </label>
                                            <div class="custom-file-container__image-preview"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Image 2</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="custom-file-container" data-upload-id="mySecondImageEdit">
                                            <label>Upload
                                                <a href="javascript:void(0)" class="custom-file-container__image-clear" title="Clear Image">x</a>
                                            </label>
                                            <label class="custom-file-container__custom-file">
                                                <input type="file" name="img_2"
                                                       class="custom-file-container__custom-file__custom-file-input">
                                                <input type="hidden" name="MAX_FILE_SIZE" value="10485760">
                                                <span class="custom-file-container__custom-file__custom-file-control"></span>
                                            </label>
                                            <div class="custom-file-container__image-preview"></div>
                                        </div>
                                    </div>
                                </div>
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
            <form action="{{route('aboutUs.delete')}}" id="updateForm" method="POST" enctype="multipart/form-data">
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

