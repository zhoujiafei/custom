<?php
return array(
   	array(
        'zh_name'       => '首页',
        'en_name'       => 'home',
        'link'          => '#',
        'child'  		=>  array(
						   			array(
							             'zh_name'       => '首页',
								         'en_name'       => 'home',
								         'link'          => 'index.php', 
						   			),
       							 ),  
    ),
    array(
        'zh_name'       => '客户信息管理',
        'en_name'       => 'custom_manger',
        'link'          => '#',    
        'child'  		=>  array(
    								array(
								        'zh_name'       => '客户信息',
								        'en_name'       => 'custom_info',
								        'link'          => 'custom.php',  
    								), 
						        ),            
    ),
);


?>