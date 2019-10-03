<div class="container dashboard">
    {{-- search form --}}
    <form id="plugin-search-form">
        <div class="row justify-content-center mt-4">
            <div class="col-sm-auto">
                <button class="btn btn-info" type="submit" title="Show all available plugins"
                    onclick="form.keyword.value='';">{{$helper->t('all,available,plugins')}}</button>
            </div>
            <div class="col-sm-auto">

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" class="form-control keyword" placeholder="Keyword" aria-label="Keyword"
                        value="{{$_REQUEST['keyword'] ?? ''}}" aria-describedby="basic-addon2" name="keyword"
                        id="keyword" />
                    <div class="input-group-append">
                        <button class="btn btn-secondary" type="submit" title="{{$helper->t('search')}}"
                            id="inputGroupinquiryAddon05">{{$helper->t('search')}}</button>
                    </div>
                </div>

            </div>


        </div>
    </form>
    <div class="row" id="search-result">

    </div>
</div>


<script>
    function installPlugin(link)    {
        if ( !confirm('Start download & install this plugin , It will take 1-5 minutes, please stay at this page!') ){
            return false;
        }
        link.removeClass('install').addClass("disabled").find('span').html('Retrieve latest release information');
        $('#search-result a.install').hide();
        link.find('i').removeClass().addClass("fas fa-spinner fa-spin mr-1");
        //link.fadeIn('slow').addClass("disabled").find('span').html('Retrieve latest release information');

        setTimeout(downloadExtractInstall(link),500);

    }


    function downloadExtractInstall(link)  {

        var download_url = '';

        var ajax_data = $.ajax({
            type: "GET",
            url: link.attr('href'),
            success: function(response) {
               // console.log(response);
            },
            cache: false,
            async: false
        });

        //console.log(ajax_data.status);
        if ( ajax_data.status != '200'){
            download_url = link.attr('href').replace('api.github.com/repos','github.com').replace('releases/latest','archive/master') + '.tar.gz';
        } else {
            download_url = ajax_data.responseJSON.html_url.replace('releases/tag','archive') + '.tar.gz';
        }




        link.find('span').html('Downloading the plugin package file');
        ajax_data = $.ajax({
            type: "GET",
            url: "./files?download_file=" + download_url,
            success: function(response) {
                //console.log(response);
            },
            cache: false,
            async: false
        });

        if ( hasError(link, ajax_data, 'downloading') ){
            return false;
        }

        link.find('span').html('Extracting the plugin package file');
        ajax_data = $.ajax({
            type: "GET",
            url: "./files?extract_file=" + ajax_data.responseText,
            success: function(response) {
                //console.log(response);
            },
            cache: false,
            async: false
        });

        if ( hasError(link, ajax_data, 'extracting') ){
            return false;
        }

        link.find('span').html('Installing the plugin package file');
        ajax_data = $.ajax({
            type: "GET",
            url: "./files?install_package=" + ajax_data.responseText,
            success: function(response) {
                //console.log(response);
            },
            cache: false,
            async: false
        });

        if ( hasError(link, ajax_data, 'installing') ){
            return false;
        }

        console.log(ajax_data);

        var response_json = ajax_data.responseJSON;
        if ( ! response_json.success ){
            response_json.status = 203;
            hasError(link, response_json, 'installing (' + response_json.error_message + ')');
            return false;
        }


        setTimeout(function(){
            $('#search-result a.install').fadeIn('slow');
            link.addClass('text-danger','disabled').html('<i class="fas fa-download mr-1"></i><span>Successfully installed</span>');
        },1000);
        //alert('Installing , It will take 1-5 minutes, please keep this page open!');
    }

    function hasError(link, ajax_data, message){
        if ( ajax_data.status != '200'){
            alert('Something wrong with response code ' + ajax_data.status + ' while '+ message +', install cancelled.');
            $('#search-result a.install').fadeIn('slow');
            link.hide();
            console.log(ajax_data);
            return true;
        }
        return false;
    }

    $(function(){


        $('#plugin-search-form').submit(function(e){
            e.preventDefault();
            var api_url = "https://packagist.org/search.json?type=amila-laravel-cms-plugin&per_page=40&q=" + $('#keyword').val();
            var cmsPlugins = $.getJSON(api_url, function(data) {
                // console.log( "success" );
                // console.log(data['tag_name']);
                })
                .done(function(data) {
                    // console.log( "second success");
                    console.log(data['results']);
                    var all_results = '';
                    data['results'].forEach((item) => {
                        all_results += '<div class="col-md-12 bg-light text-secondary p-2 title"><a href="' + item.repository + '" class="text-info" target="_blank"><i class="fas fa-cogs mr-2"></i>' + item.name + '</a>  <i class="fas fa-download mr-1 ml-3"></i>' + item.downloads + ' <a href="https://api.github.com/repos/' + item.name + '/releases/latest" class="ml-3 btn btn-outline-info btn-sm install" target="_blank"><i class="fas fa-download mr-1"></i><span>Install<span></a> </div>'
                        + '<div class="col-md-12 mb-2 p-2 abstract">' + item.description + '</div>';
                    });
                    $('#search-result').html('' + all_results);

                    $('#search-result a.install').click(function(e){
                        e.preventDefault();
                        //alert($(this).attr('href'));
                        installPlugin($(this));
                        return false;
                    });
                })
                .fail(function() {
                    console.log( "get cmsPlugins error" );
                })
                .always(function() {
                    console.log( "get cmsPlugins complete" );
                });
        });
    });

</script>
