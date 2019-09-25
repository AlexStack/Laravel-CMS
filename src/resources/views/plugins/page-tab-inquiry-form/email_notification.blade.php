<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        .container {
            width: 100%;
        }

        hr {
            color: #cccccc;
        }
    </style>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-11">

                <div class="card p-2 mb-3">


                    <div class="border-t-2 w-full mb-3 pt-3 text-dark">
                        {!! $body !!}
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>

</html>
