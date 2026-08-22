<?php
$reg = base64_encode(file_get_contents(public_path('fonts/KhmerUI.ttf')));
$bold = base64_encode(file_get_contents(public_path('fonts/KhmerUIb.ttf')));
?>
<style>
@font-face {
    font-family: 'KhmerUI';
    src: url('data:font/truetype;charset=utf-8;base64,{{ $reg }}') format('truetype');
    font-weight: normal;
    font-style: normal;
}
@font-face {
    font-family: 'KhmerUI';
    src: url('data:font/truetype;charset=utf-8;base64,{{ $bold }}') format('truetype');
    font-weight: bold;
    font-style: normal;
}
</style>
