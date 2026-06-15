<!-- Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="javascript:void(0)" id="editRole" method="POST">
            @method('PUT')
            <input type="hidden" name="id" id="editRoleId">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Add Role</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input name="name" type="text" class="form-control" id="roleName" placeholder="Role name">
                    <span class="text-danger"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    <button data-btn="roleSubmitBtn" type="submit" class="btn btn-primary">Update Role</button>
                </div>
            </div>
        </form>
    </div>
</div>
