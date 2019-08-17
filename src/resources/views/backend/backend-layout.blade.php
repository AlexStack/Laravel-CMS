<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Amila Laravel CMS Backend</title>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.9.0/css/all.min.css">

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>


        <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-bs4.css" rel="stylesheet">
        <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-bs4.js"></script>

        <style>
            body {font-family: "Microsoft YaHei","微软雅黑", "PingFang SC","Arial"}
        </style>
    </head>
    <body>
        @include('laravel-cms::backend.backend-header')
        @yield('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md text-center mt-5 mb-5 footer">
        <span class="small">Laravel CMS {{date('Y')}}</span>
        </div>
    </div>
</div>


        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.9.0/Sortable.min.js" integrity="sha256-dTRAC66orJgjxgfw22bvkFVLM8/S+vchcXIzHyYzNFg=" crossorigin="anonymous"></script>

        <script src="https://cdn.jsdelivr.net/npm/jquery-sortablejs@latest/jquery-sortable.js"></script>


        <script>
            $('#sortableList').sortable({
            handle: '.handle', // handle's class
            animation: 150,
            // Element dragging ended
            onEnd: function (/**Event*/evt) {
                var itemEl = evt.item;  // dragged HTMLElement
                evt.to;    // target list
                evt.from;  // previous list
                evt.oldIndex;  // element's old index within old parent
                evt.newIndex;  // element's new index within new parent
                evt.oldDraggableIndex; // element's old index within old parent, only counting draggable elements
                evt.newDraggableIndex; // element's new index within new parent, only counting draggable elements
                evt.clone // the clone element
                evt.pullMode;  // when item is in another sortable: `"clone"` if cloning, `true` if moving
                console.log(evt);
            },
        });
        </script>

    </body>
</html>
