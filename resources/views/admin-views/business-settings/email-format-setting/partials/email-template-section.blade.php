{{-- <select name="email_template" class="custom-select mb-3" onchange="set_mail_filter('{{url()->full()}}',this.value, 'template')">
    <option value="1" {{ $template == '1'?'selected':'' }}>Template - 1</option>
    <option value="2" {{ $template == '2'?'selected':'' }}>Template - 2</option>
    <option value="3" {{ $template == '3'?'selected':'' }}>Template - 3</option>
    <option value="4" {{ $template == '4'?'selected':'' }}>Template - 4</option>
    <option value="5" {{ $template == '5'?'selected':'' }}>Template - 5</option>
    <option value="6" {{ $template == '6'?'selected':'' }}>Template - 6</option>
    <option value="7" {{ $template == '7'?'selected':'' }}>Template - 7</option>
    <option value="8" {{ $template == '8'?'selected':'' }}>Template - 8</option>
    <option value="9" {{ $template == '9'?'selected':'' }}>Template - 9</option>
</select> --}}

<input type="hidden" value="{{ $template }}" name="email_template">
