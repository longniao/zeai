<?php
!function_exists('ifalone') && exit('Forbidden!');
$mini_url = (empty($mini_url))?'javascript:window.history.back(-1);':'openlinks("'.$mini_url.'")';
?>
<header class="top_mini">
    <div onclick='<?php echo $mini_url; ?>' class='back'><i></i></div>
    <div class='title'><div class='h1'><?php echo $mini_title;?></div></div>
</header>