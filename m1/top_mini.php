<?php !function_exists('ifalone') && exit('Forbidden!');
$mini_class = (empty($mini_class))?'top_mini':$mini_class;
?>
<header class="<?php echo $mini_class;?>"<?php echo $mini_ext;?>>
    <div class='L'><?php echo $mini_backT;?></div>
	<h1 id="mini_title"><?php echo $mini_title;?></h1>
    <div class='R'><?php echo $mini_R;?></div>
</header>