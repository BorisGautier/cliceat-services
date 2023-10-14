<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{translate('MTN MOMO PAYMENT')}}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{asset('assets/modules/css/bootstrap.min.css')}}">
    <script src="{{asset('assets/modules/js/jquery.slim.min.js')}}"></script>
    <script src="{{asset('assets/modules/js/popper.min.js')}}"></script>
    <script src="{{asset('assets/modules/js/bootstrap.bundle.min.js')}}"></script>
</head>
<body>
<div class="container py-3">
    <div class="row">
        <div class="col-md-12">

            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card card-outline-secondary">
                        <div class="card-header">
                            <h3 class="mb-0 text-center"><img class="class-1"
                                                              src="{{asset('assets/modules/image/momo.png')}}"/>
                            </h3>
                        </div>
                        <div class="card-body">
                            <form action="{{route('momo.callback')}}">
                                <input type="hidden" name="orderID" value="{{$data->attribute_id}}">
                                <input type="hidden" name="paymentID" value="{{$data->id}}">
                                <div class="form-group">
                                    <label for="mobile_number">{{translate('Amount')}}</label>
                                    <input class="form-control border-0 border-none" value="{{$data->payment_amount}}"
                                           name="order_amount" type="text" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="mobile_number">{{translate('Phone Number')}}</label>
                                    <input class="form-control" id="mobile_number" name="mobile_number" required=""
                                           type="text">
                                </div>
                                <button class="btn btn-success btn-block"
                                        type="submit">{{translate('Proceed to payment')}}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

</body>

</html>
