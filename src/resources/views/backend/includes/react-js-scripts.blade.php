<script src="https://cdn.jsdelivr.net/npm/lodash@4.17.15/lodash.min.js" crossorigin="anonymous"></script>

@php
if ( isset($_SERVER['HTTP_USER_AGENT']) ){
$ua = htmlentities($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8');
if ( preg_match('~MSIE|Internet Explorer~i', $ua) || (strpos($ua, 'Trident') !== false )) {
//echo '<h3>polyfill loaded</h3>' . $ua;
echo '<script src="https://cdn.jsdelivr.net/npm/babel-polyfill@6.26.0/dist/polyfill.min.js" crossorigin="anonymous">
</script>';
}
}
@endphp


<script src="https://cdn.jsdelivr.net/npm/react@16.8.6/umd/react.production.min.js" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/react-router@5.0.1/umd/react-router.min.js" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/react-dom@16.8.6/umd/react-dom.production.min.js" crossorigin="anonymous">
</script>

{{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.min.js" crossorigin="anonymous"></script> --}}


<script src="https://cdn.jsdelivr.net/npm/redux@4.0.4/dist/redux.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/react-redux@7.1.0/dist/react-redux.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/redux-saga@1.0.5/dist/redux-saga.umd.min.js" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/redux-persist@5.10.0/dist/redux-persist.min.js" crossorigin="anonymous">
</script>

<script src="https://cdn.jsdelivr.net/npm/axios@0.19.0/dist/axios.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/history@4.9.0/umd/history.min.js" crossorigin="anonymous"></script>


<script src="https://cdn.jsdelivr.net/npm/immutable@4.0.0-rc.12/dist/immutable.min.js" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/formik@1.5.8/dist/formik.umd.production.js" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/mark.js@8.11.1/dist/jquery.mark.min.js"></script>
