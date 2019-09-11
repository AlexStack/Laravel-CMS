@php
if ( !isset($input_attributes) ) {
$input_attributes = [];
}
if ( !isset($input_attributes['class'])){
$input_attributes['class'] = 'form-control input-'.$name;
}

@endphp

<div class="{{ $groupClass ?? 'form-group' }}">
    <label for="{{$name}}" class="label-{{$name}}">{!! $label ?? $helper->t($name) !!}</label>

    @if ( !isset($type) || $type == 'text')

    {!! Form::text($name, null, $input_attributes); !!}

    @elseif ( $type == 'textarea')

    {!! Form::textarea($name, null, $input_attributes); !!}

    @elseif ( $type == 'email')

    {!! Form::email($name, null, $input_attributes); !!}

    @elseif ( $type == 'number')

    {!! Form::number($name, null, $input_attributes); !!}

    @elseif ( $type == 'file')

    {!! Form::file($name, $input_attributes); !!}
    @if ( isset($file_data) && property_exists($file_data, $name) && isset($file_data->$name) )
    <div class="form-group upload-img">
        <a href="{{$helper->imageUrl($file_data->$name, 'original','original') }}" target="_blank">
            <img class="img-fluid img-thumbnail p-1"
                src="{{$helper->imageUrl($file_data->$name, $helper->s('file.small_image_width'), $helper->s('file.small_image_height')) }}"
                style="max-height:{{$helper->s('file.small_image_height')}}px;" /></a>

        {{$helper->t('delete') }}: {!! Form::checkbox($name . '_delete', 1, $checked ?? false); !!}

        {{-- @if ( $name == 'main_image')
        Insert Image:
        <a href="#"
            onclick="insertImageToEditor('.input-main_content', '{{$helper->imageUrl($file_data->$name, 'w','h') }}');return
        false;">Original
        Image</a>
        -
        <a href="#"
            onclick="insertImageToEditor('.input-main_content', '{{$helper->imageUrl($file_data->$name, $helper->s('file.big_image_width'), 'h') }}');return false;">Big
            Image</a>
        -
        <a href="#"
            onclick="insertImageToEditor('.input-main_content', '{{$helper->imageUrl($file_data->$name, $helper->s('file.small_image_width'), $helper->s('file.small_image_height')) }}');return false;">Small
            Image</a>

        @endif --}}

    </div>
    @endif

    @elseif ( $type == 'checkbox')

    {!! Form::checkbox($name, 1, $checked ?? false); !!}

    @elseif ( $type == 'select')

    {!! Form::select($name, $options, null, $input_attributes); !!}

    @elseif ( $type == 'date')

    {!! Form::date($name, $value ?? null, $input_attributes); !!}

    @endif
</div>
