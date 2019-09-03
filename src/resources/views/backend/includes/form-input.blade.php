@php
   if ( !isset($input_attributes) )    {
        $input_attributes = [];
   }
   if ( !isset($input_attributes['class'])){
    $input_attributes['class'] = 'form-control input-'.$name;
   }

@endphp

<div class="{{ $groupClass ?? 'form-group' }}">
{!! Form::label($name, ($label ?? ucwords(str_replace('_',' ',$name))), ['class' => 'label-'.$name]); !!}

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
                <img class="img-fluid img-thumbnail p-1" src="{{$helper->imageUrl($file_data->$name, 'w','100') }}" alt="" /></a>

            Delete: {!! Form::checkbox($name . '_delete', 1, $checked ?? false); !!}

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
