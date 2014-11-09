<nav>
	<ul class="pager">
	<?php 
	
	if($page > 1) {
		$prev = $page - 1;
		$arr_args = array('p' => $prev);
		$arr_args = array_merge($_GET, $arr_args);
		
		
		$args = http_build_query($arr_args);
		echo '<li><a href="' . $self_url . '?' . $args . '">&laquo; Previous</a></li>';
	}
	
	if($page < $page_count) {
		$nex = $page + 1;
		$arr_args = array('p' => $nex);
		$arr_args = array_merge($_GET, $arr_args);
		
		$args = http_build_query($arr_args);
		echo '<li><a href="' . $self_url . '?' . $args . '">Next &raquo;</a></li>';
		
	}
	?>
	</ul>
</nav>