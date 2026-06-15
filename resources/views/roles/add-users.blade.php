<!-- Modal -->
<div class="modal fade" id="userSearchModal" tabindex="-1" aria-labelledby="userSearchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="javascript:void(0)" id="addUsers" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Add Users</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <select name="users[]" class="form-control select2" id="select-users" multiple>
                        @foreach ($users as $user )
                            <option value="{{$user->id}}">{{$user->name}} - {{$user->user_number}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    <button data-btn="roleSubmitBtn" type="submit" class="btn btn-primary">Add Users</button>
                </div>
            </div>
        </form>
    </div>
</div>
