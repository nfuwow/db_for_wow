<?php
/**
 *  QueryList使用示例
 *  
 * 入门教程:http://doc.querylist.cc/site/index/doc/4
 * 
 * QueryList::Query(采集的目标页面,采集规则[,区域选择器][，输出编码][，输入编码][，是否移除头部])
* //采集规则
* $rules = array(
*   '规则名' => array('jQuery选择器','要采集的属性'[,"标签过滤列表"][,"回调函数"]),
*   '规则名2' => array('jQuery选择器','要采集的属性'[,"标签过滤列表"][,"回调函数"]),
*    ..........
*    [,"callback"=>"全局回调函数"]
* );
 */



require 'vendor/autoload.php';

use QL\QueryList;

//header('Content-Type: text/html; charset=UTF-8');

ini_set( 'default_charset', 'UTF-8' );

$sql = '';

$page = '';
$intPage = 1;
if(!empty($_GET['page'])) {
    $page = '/page/'.$_GET['page'];
    $intPage = intval($_GET['page']);
}

$intTotal = 180;
//$intTotal = 10;

if($intPage >= $intTotal) {
    exit('alldone');
}


//采集某页面所有的图片
$data = QueryList::Query('http://cn.60wdb.com/items'.$page,

array(
    'title' => array('.iconname a','title','',function($content){
        return $content;
    }),

    'id' => array('.iconname a','id','',function($content){
        return $content;
    }),
))->data;

$arrNew = array(
    'e'=>array(),
    'c'=>array(),
);

foreach($data as $key=>$temp){
    $sql .= "UPDATE `wow`.`item_template` SET `chinese` = \"".$temp['title']."\" WHERE `item_template`.`entry` = \"".$temp['id']."\"; \r\n"; 
}

//echo $sql;
//
//

//file_put_contents('items.sql',$sql);

// 要写入的文件名字
$filename = 'items.sql';

error_log($sql, 3, $filename);

//sleep(1);

?>
<script type="text/javascript">setTimeout(function() { window.location.href='http://localhost:8888/wowhead/tool/QueryList/items.php?page=<?php echo ($intPage+1);?>'; }, 1000);</script>
正在跳转到 <?php echo ($intPage+1);?> 页

