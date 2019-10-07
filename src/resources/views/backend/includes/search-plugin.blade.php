<div class="container search-plugin-container">
    <a name="search_plugin"></a>
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
        echo 'var githubRepositories = ' . json_encode($github_repositories) . ";";
    @endphp

    var downloadUrl     = '';
    var latestVersion   = 'master';
    var githubFullName  = '';
    var confirmCount    = 0;

    function installPlugin(link)    {
        if ( confirmCount < 1 && !confirm('Start download & install this plugin , It may take a few minutes, please stay at this page!') ){
            return false;
        }
        confirmCount++;
        link.removeClass('install').addClass("disabled").find('span').html('Retrieve latest release information');
        $('#search-result a.install').hide();
        link.find('i').removeClass().addClass("fas fa-spinner fa-spin mr-1");
        //link.fadeIn('slow').addClass("disabled").find('span').html('Retrieve latest release information');

        setTimeout(function() {
            retrieveLatestRelease(link)
        }, 1500);

    }


    function retrieveLatestRelease(link)  {

        downloadUrl   = '';
        latestVersion = 'master';
        githubFullName = link.attr('href').replace('https://api.github.com/repos/','').replace('/releases/latest','');

        // Retrieve latest release information
        var ajaxData = $.ajax({
            type: "GET",
            url: link.attr('href'),
            success: function(response) {
                console.log( "retrieveLatestRelease success" );
                // console.log(response);
                downloadUrl = response.html_url.replace('releases/tag','archive') + '.tar.gz';
                latestVersion = response.tag_name;
                downloadThePackage(link);
            },
            error: function(response) {
                console.log( "retrieveLatestRelease error" );
                // console.log(response);
                downloadUrl = link.attr('href').replace('api.github.com/repos','github.com').replace('releases/latest','archive/master')
                + '.tar.gz';
                downloadThePackage(link);

            },
            always: function(response) {
                console.log( "retrieveLatestRelease always" );
                console.log(githubRepositories);
            },
            cache: false,
            async: true
        });
    }

    function downloadThePackage(link){
        // master version should always be able to update
        if(latestVersion != 'master' && githubFullName in githubRepositories && githubRepositories[githubFullName] == latestVersion ){
            if ( hasError(link, 203, 'installing(Version ' + latestVersion + ' already installed)') ){
                return false;
            }
        }

        link.find('span').html('Downloading the plugin package\(' + latestVersion + '\)');
        ajaxData = $.ajax({
            type: "GET",
            url: "./files?download_file=" + downloadUrl,
            success: function(response) {
                console.log('downloadThePackage success');
                // console.log(response);
                extractThePackage(link, response);
            },
            error: function(response) {
                console.log( "downloadThePackage error" );
                // console.log(response);
                if ( hasError(link, response.status, 'downloading') ){
                    return false;
                }
            },
            cache: false,
            async: true
        });

    }

    function extractThePackage(link, fileUrl){
        link.find('span').html('Extracting the plugin package file');
        ajaxData = $.ajax({
            type: "GET",
            url: "./files?extract_file=" + fileUrl,
            success: function(response) {
                console.log('extractThePackage success');
                // console.log(response);
                installThePackage(link, fileUrl);
            },
            error: function(response) {
                console.log( "extractThePackage error" );
                // console.log(response);
                if ( hasError(link, response.status, 'extracting') ){
                    return false;
                }
            },
            cache: false,
            async: true
        });
    }

    function installThePackage(link, fileUrl){
        link.find('span').html('Installing the plugin package file');
        ajaxData = $.ajax({
            type: "GET",
            url: "./files?install_package=" + fileUrl,
            success: function(response) {
                console.log( "installThePackage success" );
                // console.log(response);
                if ( ! response.success ){
                    hasError(link, 203, 'installing (' + response.error_message + ')');
                    return false;
                }
                setTimeout(function(){
                    $('#search-result a.install').fadeIn('slow');
                    link.addClass('btn-outline-danger','disabled').html('<i class="fas fa-download mr-1"></i><span>Successfully installed</span>');

                    link.after('<a href="./settings/'+ response.plugin_id +'/edit" class="btn btn-sm btn-outline-success ml-3 btn-after-install"><i class="fas fa-cog mr-1"></i><span>Settings</span></a>');

                    if ( response.plugin_type == 'standalone'){
                        link.after('<a href="./plugins/'+ response.param_name +'" class="btn btn-sm btn-outline-primary ml-3 btn-standalone"><i class="fas fa-cogs mr-1"></i><span>Launch</span></a>');
                    }

                },1500);
            },
            error: function(response) {
                console.log( "installThePackage error" );
                // console.log(response);
                if ( hasError(link, response.status, 'installing') ){
                    return false;
                }
            },
            cache: false,
            async: true
        });
    }

    function hasError(link, responseCode, message){
        if ( responseCode != '200'){
            alert('Something wrong with response code ' + responseCode + ' while '+ message +', install cancelled.');
            $('#search-result a.install').fadeIn('slow');
            link.hide();
            return true;
        }
        return false;
    }

    function searchPlugin(){

        var apiUrl = "https://packagist.org/search.json?type=amila-laravel-cms-plugin&per_page=40&q=" + $('#keyword').val();
        // var apiUrl = "https://packagist.org/search.json?type=laravel&per_page=40&q=" + $('#keyword').val(); // for testing
        var cmsPlugins = $.getJSON(apiUrl, function(data) {
            // console.log( "success" );
            // console.log(data['tag_name']);
            })
            .done(function(data) {
                // console.log( "second success");
                // console.log(data['results']);
                var allResults = '';
                data['results'].forEach((item) => {
                    var btnText  = 'Install';
                    var btnClass = 'btn-outline-info install';
                    if( item.name in githubRepositories) {
                        btnText  = 'Update';
                        btnClass = 'btn-outline-success install';
                    }
                    allResults += '<div class="col-md-12 bg-light text-secondary p-3 title"><a href="' + item.repository + '" class="text-info" target="_blank"><i class="fas fa-cogs mr-2"></i>' + item.name + '</a>  <span class="download-count d-none"><i class="fas fa-download mr-1 ml-3"></i>' + item.downloads + '</span> <a href="https://api.github.com/repos/' + item.name + '/releases/latest" class="ml-3 btn btn-sm '+ btnClass + '" target="_blank"><i class="fas fa-download mr-1"></i><span>' + btnText + '<span></a> </div>'
                    + '<div class="col-md-12 mb-2 p-3 abstract">' + item.description + '</div>';
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
                console.log( "searchPlugin error" );
            })
            .always(function() {
                console.log( "searchPlugin complete" );
            });

    }


    $(function(){
        var oneTimeClass = 'mouseover-done';

        $('#plugin-search-form').submit(function(e){

            e.preventDefault();

            searchPlugin();
        });


        if ( location.href.indexOf('search_plugin=yes') != -1 ){
            $('#plugin').addClass(oneTimeClass);
            $('#plugin ul:first').prepend('<li class="list-group-item pb-5 search-plugin-li">'+ $('.search-plugin-container').html()+'</li>');
            $('.search-plugin-container').html('');
            searchPlugin();
        } else {

            $('#plugin ul.plugin').mouseover(function(){
                //var oneTimeClass = 'mouseover-done';
                if (!$(this).hasClass(oneTimeClass)){
                    $(this).addClass(oneTimeClass);
                    searchPlugin();
                }
            });
        }
    });

</script>
