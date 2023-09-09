{{--@dd($tables)--}}
@if($tables != null)
@foreach($tables as $table)
    <div class="dropright">
        <div class="card table_hover-btn py-4 {{ $table['order'] != null ? 'bg-c1' : 'bg-gray'}} stopPropagation"
{{--             data-toggle="modal" data-target="#tableInfoModal"--}}
        >
            <div class="card-body mx-3 position-relative text-center">
{{--                next release--}}
{{--                <i class="tio-alarm-alert position-absolute right-0 top-0 fz-18 text-primary"></i>--}}
                <h3 class="card-title mb-2">{{ translate('table') }}</h3>
                <h5 class="card-title mb-1">{{ $table['number'] }}</h5>
                <h5 class="card-title mb-1">{{ translate('capacity') }}: {{ $table['capacity'] }}</h5>
            </div>
        </div>
        <div class="table_hover-menu px-3">
            <h3 class="mb-3">{{ translate('Table - D2 ') }}</h3>
            @if(($table['order'] != null))
                @foreach($table['order'] as $order)
                    <div class="fz-14 mb-1">{{ translate('order id') }}: <strong>{{ $order['id'] }}</strong></div>
                @endforeach
            @else
                <div class="fz-14 mb-1">{{ translate('current status') }} - <strong>{{ translate('empty') }}</strong></div>
                <div class="fz-14 mb-1">{{ translate('any reservation') }} - <strong>{{ translate('N/A') }}</strong></div>
            @endif
{{-- next release--}}
{{--            <div class="d-flex flex-wrap gap-2 mt-3">--}}
{{--                <a href="#" data-dismiss="modal" class="btn btn-outline-primary text-nowrap stopPropagation" data-toggle="modal" data-target="#reservationModal"><i class="tio-alarm-alert"></i> {{ translate('Create_Reservation') }}</a>--}}
{{--                <a href="#" class="btn btn-primary text-nowrap">{{ translate('Place_Order') }}</a>--}}
{{--            </div>--}}
        </div>
    </div>
@endforeach
@else
    <div class="col-md-12 text-center">
        <h4 class="">{{translate('This branch has no table')}}</h4>
    </div>
@endif

{{--next release--}}
<!-- tAble Info Modal -->
<div class="modal fade" id="tableInfoModal" tabindex="-1" role="dialog" aria-labelledby="tableInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="text-center position-relative px-4 py-5">
            <button type="button" class="close text-primary position-absolute right-2 top-2 fz-24" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>

            <h3 class="mb-3">{{ translate('Table# - D2 ') }}</h3>
            <div class="fz-14 mb-1">{{ translate('current status') }} - <strong>{{ translate('Available') }}</strong></div>
            <div class="fz-14 mb-1">{{ translate('any reservation') }} - <strong>{{ translate('5 Reservation') }}</strong></div>

            <div class="d-flex flex-wrap justify-content-center text-nowrap gap-2 mt-4">
                <div class="bg-gray rounded d-flex flex-column gap-2 p-3">
                    <h6 class="mb-0">Today</h6>
                    <p class="mb-0">12:00 - 23:00</p>
                </div>
                <div class="bg-gray rounded d-flex flex-column gap-2 p-3">
                    <h6 class="mb-0">Tomorrow</h6>
                    <p class="mb-0">12:00 - 23:00</p>
                    <p class="mb-0">12:00 - 23:00</p>
                </div>
                <div class="bg-gray rounded d-flex flex-column gap-2 p-3">
                    <h6 class="mb-0">Today</h6>
                    <p class="mb-0">12:00 - 23:00</p>
                </div>
            </div>

            <div class="d-flex mt-5 mx-lg-5">
                <a href="#" class="btn btn-outline-primary w-100 text-nowrap" data-dismiss="modal" data-toggle="modal" data-target="#reservationModal"><i class="tio-alarm-alert"></i> {{ translate('Create_Reservation') }}</a>
            </div>
        </div>
    </div>
  </div>
</div>

<!-- Reservatrion Modal -->
<div class="modal fade" id="reservationModal" tabindex="-1" role="dialog" aria-labelledby="reservationModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="position-relative px-4 py-5">
            <button type="button" class="close text-primary position-absolute right-2 top-2 fz-24" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <form action="#">
                <div class="text-center">
                    <h3 class="mb-3">{{ translate('Table# - D2 ') }}</h3>
                    <div class="fz-14 mb-1">{{ translate('current status') }} - <strong>{{ translate('Available') }}</strong></div>
                </div>

                <div class="mb-4 mt-5">
                    <label for="table_no">{{ translate('Table_No') }}</label>
                    <select name="table_no" id="table_no" class="custom-select">
                        <option value="#" selected disabled>{{ translate('Select_Tables') }}</option>
                        <option value="#">{{ translate('D1') }}</option>
                        <option value="#">{{ translate('D2') }}</option>
                        <option value="#">{{ translate('D3') }}</option>
                        <option value="#">{{ translate('D4') }}</option>
                        <option value="#">{{ translate('D5') }}</option>
                        <option value="#">{{ translate('D6') }}</option>
                        <option value="#">{{ translate('D7') }}</option>
                    </select>
                </div>

                <div class="mb-4 mt-5">
                    <label for="reservation_time">{{ translate('Reservation_Time') }}</label>
                    <input type="date" id="reservation_time" class="form-control">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="start_time">{{ translate('Start_Time') }}</label>
                            <input type="time" id="start_time" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label for="end_time">{{ translate('end_Time') }}</label>
                            <input type="time" id="end_time" class="form-control">
                        </div>
                    </div>
                </div>

                <p class="text-primary text-center mt-3"> *  Sorry, There is already another reservation in this time </p>

                <div class="d-flex justify-content-center mt-4">
                    <button type="submit" class="btn btn-primary px-lg-5 text-nowrap">{{ translate('Book_Reservation') }}</button>
                </div>
            </form>
        </div>
    </div>
  </div>
</div>
