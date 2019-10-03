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
    @php
        $github_repositories = [];
        foreach( $helper->s('top.plugin') as $plugin){
            if ( isset($plugin['github_full_name']) && strpos($plugin['github_full_name'], '/') ){
                $github_repositories[strtolower(trim($plugin['github_full_name']))] = $plugin['version'] ?? 'master';
            }
        }
        echo 'var githubRepositories =' . json_encode($github_repositories) . ";\n\n";
    @endphp

    function installPlugin(link)    {
        if ( !confirm('Start download & install this plugin , It may take a few minutes, please stay at this page!') ){
            return false;
        }
        link.removeClass('install').addClass("disabled").find('span').html('Retrieve latest release information');
        $('#search-result a.install').hide();
        link.find('i').removeClass().addClass("fas fa-spinner fa-spin mr-1");
        //link.fadeIn('slow').addClass("disabled").find('span').html('Retrieve latest release information');

        // setTimeout(downloadExtractInstall(link),1500);
        setTimeout(function() {
            downloadExtractInstall(link)
        }, 2000);

    }


    function downloadExtractInstall(link)  {

        var downloadUrl   = '';
        var latestVersion = 'master';

        // Retrieve latest release information
        var ajaxData = $.ajax({
            type: "GET",
            url: link.attr('href'),
            success: function(response) {
               // console.log(response);
            },
            cache: false,
            async: false
        });

        //console.log(ajaxData.status);
        githubFullName = link.attr('href').replace('https://api.github.com/repos/','').replace('/releases/latest','');
        if ( ajaxData.status != '200'){
            downloadUrl = link.attr('href').replace('api.github.com/repos','github.com').replace('releases/latest','archive/master') + '.tar.gz';
        } else {
            downloadUrl = ajaxData.responseJSON.html_url.replace('releases/tag','archive') + '.tar.gz';
            latestVersion = ajaxData.responseJSON.tag_name;
        }
        console.log(' installedVersion:' +githubRepositories[githubFullName] + ' latestVersion:' + latestVersion);

        if(githubFullName in githubRepositories && githubRepositories[githubFullName] == latestVersion ){
            ajaxData.status = 203;
            if ( hasError(link, ajaxData, 'installing(version ' + latestVersion + ' already installed)') ){
                return false;
            }
        }


        link.find('span').html('Downloading the plugin package\(' + latestVersion + '\)');
        ajaxData = $.ajax({
            type: "GET",
            url: "./files?download_file=" + downloadUrl,
            success: function(response) {
                //console.log(response);
            },
            cache: false,
            async: false
        });

        if ( hasError(link, ajaxData, 'downloading') ){
            return false;
        }

        link.find('span').html('Extracting the plugin package file');
        ajaxData = $.ajax({
            type: "GET",
            url: "./files?extract_file=" + ajaxData.responseText,
            success: function(response) {
                //console.log(response);
            },
            cache: false,
            async: false
        });

        if ( hasError(link, ajaxData, 'extracting') ){
            return false;
        }

        link.find('span').html('Installing the plugin package file');
        ajaxData = $.ajax({
            type: "GET",
            url: "./files?install_package=" + ajaxData.responseText,
            success: function(response) {
                //console.log(response);
            },
            cache: false,
            async: false
        });

        if ( hasError(link, ajaxData, 'installing') ){
            return false;
        }

        console.log(ajaxData);

        var response_json = ajaxData.responseJSON;
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

    function hasError(link, ajaxData, message){
        if ( ajaxData.status != '200'){
            alert('Something wrong with response code ' + ajaxData.status + ' while '+ message +', install cancelled.');
            $('#search-result a.install').fadeIn('slow');
            link.hide();
            console.log(ajaxData);
            return true;
        }
        return false;
    }

    $(function(){


        $('#plugin-search-form').submit(function(e){
            e.preventDefault();
            //var apiUrl = "https://packagist.org/search.json?type=amila-laravel-cms-plugin&per_page=40&q=" + $('#keyword').val();
            var apiUrl = "https://packagist.org/search.json?type=laravel&per_page=40&q=" + $('#keyword').val();
            var cmsPlugins = $.getJSON(apiUrl, function(data) {
                // console.log( "success" );
                // console.log(data['tag_name']);
                })
                .done(function(data) {
                    // console.log( "second success");
                    console.log(data['results']);
                    var allResults = '';
                    data['results'].forEach((item) => {
                        var btnText  = 'Install';
                        var btnClass = 'btn-outline-info install';
                        if( item.name in githubRepositories) {
                            btnText  = 'Update';
                            btnClass = 'btn-outline-success install';
                        }
                        allResults += '<div class="col-md-12 bg-light text-secondary p-2 title"><a href="' + item.repository + '" class="text-info" target="_blank"><i class="fas fa-cogs mr-2"></i>' + item.name + '</a>  <span class="download-count d-none"><i class="fas fa-download mr-1 ml-3"></i>' + item.downloads + '</span> <a href="https://api.github.com/repos/' + item.name + '/releases/latest" class="ml-3 btn btn-sm '+ btnClass + '" target="_blank"><i class="fas fa-download mr-1"></i><span>' + btnText + '<span></a> </div>'
                        + '<div class="col-md-12 mb-2 p-2 abstract">' + item.description + '</div>';
                    });
                    $('#search-result').html('' + allResults);

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
