<a name="inquiry_form"></a>

<form method="POST" action="{{route('LaravelCmsPluginInquiry.submitForm',null,false)}}" accept-charset="UTF-8"
    id="laravel-cms-inquiry-form">

    <input type="hidden" name="response_type" value="json" />

    {!! $dynamic_inputs !!}

</form>


{!! $gg_recaptcha !!}

{{--
    A javascript function submitInquiryForm() in the bottom.js will be invoked when the page is ready

    It's better to put the javascript here when develop your own plugin as the frontend bottom.js file are not controllable
--}}
