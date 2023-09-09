<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{translate('add wallet point')}}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<form action="javascript:" method="POST" id="point-form-{{$customer['id']}}">
    @csrf
    <div class="modal-body">
        <h5>
            <label class="badge badge-soft-info">
                {{$customer['f_name']}} {{$customer['l_name']}}
            </label>
            <label class="show-point-{{$customer['id']}}">
                ( {{translate('Available Point')}} : {{$customer['point']}} )
            </label>
        </h5>
        <div class="form-group">
            <label for="recipient-name" class="col-form-label">{{translate('Add Point :')}}</label>
            <input type="number" min="1" value="1" max="1000000"
                   class="form-control"
                   name="point">
        </div>

        <div class="d-flex justify-content-end gap-3">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                {{translate('Close')}}
            </button>
            <button type="button"
                    onclick="add_point('point-form-{{$customer['id']}}','{{route('admin.customer.add-point',[$customer['id']])}}','{{$customer['id']}}')"
                    class="btn btn-primary">{{translate('Add')}}
            </button>
        </div>
    </div>
</form>
