<?php

require(dirname(__FILE__) . '/includes/bootstrap.php');

$periods = array('today', 'this-week', 'this-month', 'all');
$types = array('image', 'text', 'video');
$counts = array('image' => 12, 'text' => 10, 'video' => 8);

foreach($types as $type)
{
    $result = array();    
    foreach($periods as $period)
    {
        $tResult = BuckysPost::getTopPosts(BuckysPost::INDEPENDENT_POST_PAGE_ID, $period, $type, 1, $counts[$type] - count($result));
        
        $result = array_merge($result, $tResult);
        if(count($result) >= $counts[$type])
            break;
    }
    //Delete Old Data From DB
    $db->query("DELETE FROM " . TABLE_STATS_POST . " WHERE postType='" . $type . "'");
    //Insert New Data To DB
    foreach($result as $idx=>$row)
    {
        $db->insertFromArray(TABLE_STATS_POST, array('postID' => $row['postID'], 'postType' => $type, 'sortOrder' => $idx + 1, 'createdDate' => date('Y-m-d H:i:s')));
    }
    
}
//Send Email for Testing
//buckys_sendmail('itsjinlie@gmail.com', 'Eric So', 'cronjob test', 'cronjob test');