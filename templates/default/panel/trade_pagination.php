<?php

/**
* $BUCKYS_GLOBALS['tradePagination'] should be set by  function buckys_trade_pagination
*/

$paginationData = $BUCKYS_GLOBALS['tradePagination'];



$pagination = new Pagination($paginationData['totalRecords'], TRADE_ROWS_PER_PAGE, $paginationData['currentPage']);


$pagination->renderPaginate($paginationData['baseUrl'], $paginationData['currentRecords']);



/* =========== Old Pagination ==================//

$paginationData = $BUCKYS_GLOBALS['tradePagination'];
$parsedUrl = parse_url($paginationData['baseUrl']);
if ($parsedUrl['query'] == '') {
    $paginationData['baseUrl'] .= '?p=';
}
else {
    $paginationData['baseUrl'] .= '&p=';
}

function getPaginationUrl($baseUrl, $pageNum) {
    return $baseUrl . $pageNum;
}

$pageList[] = $paginationData['currentPage'];

for ($idx = 1; $idx < 5; $idx ++) {
    if ($paginationData['currentPage'] + $idx <= $paginationData['totalPages'])
        $pageList[] = $paginationData['currentPage'] + $idx;
    if ($paginationData['currentPage'] - $idx > 0)
        $pageList[] = $paginationData['currentPage'] - $idx;
    
    if (count($pageList) >= 5)
        break;
}

asort($pageList);


?>

<section id="trade_pagination">
    <div class="total-record-p"><?php echo sprintf('Showing %d - %d of %s Results', $paginationData['startIndex'], $paginationData['endIndex'], number_format($paginationData['totalRecords']))?></div>
    <div class="trade-page-box">
        
        <?php if ($paginationData['currentPage'] > 1) :?>
            <span><a href="<?php echo getPaginationUrl($paginationData['baseUrl'], 1);?>"><font size="+1">&laquo;</font> First</a></span>
            <span><a href="<?php echo getPaginationUrl($paginationData['baseUrl'], $paginationData['currentPage'] - 1);?>"><font size="+1">&laquo;</font> Back</a></span>
        <?php endif;?>
        
        <?php 
            
            $lastPageNum = 1;
            foreach($pageList as $pageNum) {
                if ($pageNum == $paginationData['currentPage'])
                    echo sprintf('<span class="cur-page">%s</span>', $pageNum);
                else 
                    echo sprintf('<span><a href="%s"> %s</a></span>', getPaginationUrl($paginationData['baseUrl'], $pageNum), $pageNum);
                $lastPageNum = $pageNum;
            }
            
            if ($lastPageNum < $paginationData['totalPages']) {
                echo '<span>...</span>';
            }
            
        ?>
            
        <?php if ($paginationData['currentPage'] < $paginationData['totalPages']) :?>
            <span><a href="<?php echo getPaginationUrl($paginationData['baseUrl'], $paginationData['currentPage'] + 1);?>">Next <font size="+1">&raquo;</font></a></span>
            <span><a href="<?php echo getPaginationUrl($paginationData['baseUrl'], $paginationData['totalPages']);?>">Last <font size="+1">&raquo;</font></a></span>
        <?php endif;?>
    </div>
</section>
*/