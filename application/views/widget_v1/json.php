<?php 
$output = ""; 
if (is_object($dto)) {
	$output = $dto->toJSONString();
}
echo $output;

/* End of file json.php */
/* Location: ./application/views/widget_v1/json.php */