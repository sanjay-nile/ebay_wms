<!-- Modal -->
<div class="modal fade" id="bulkUpload" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="post" enctype="multipart/form-data" id="importWaybillData" class="was-validated" action="{{ $url }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLongTitle">Orders Bulk Upload</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">                
                   <div class="custom-file form-group">
                        <input type="file" class="custom-file-input" id="validatedCustomFile" name="csvFileImport" required>
                        <label class="custom-file-label" for="validatedCustomFile">Choose file...</label>
                    </div>
                    <div class="form-group" id="msg">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm action" data-dismiss="modal">Close</button>
                    <button type="submit" name="upload_file" id="upload_file" class="btn btn-outline-primary btn-sm btn-action">Upload</button>
                </div>
            </div>
        </form>
    </div>
</div>