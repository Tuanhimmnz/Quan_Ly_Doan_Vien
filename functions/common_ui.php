<?php
// [FITDNU-ADD] Common UI helpers (pagination, truncation styles)

function paginate_array($items, $page, $per_page) {
    $page = max(1, (int)$page);
    $per_page = max(1, (int)$per_page);
    $total = count($items);
    $pages = max(1, (int)ceil($total / $per_page));
    if ($page > $pages) $page = $pages;
    $offset = ($page - 1) * $per_page;
    $slice = array_slice($items, $offset, $per_page);
    return [
        'data' => $slice,
        'meta' => [
            'page' => $page,
            'per_page' => $per_page,
            'total' => $total,
            'pages' => $pages
        ]
    ];
}

function render_pagination($baseUrl, $page, $pages, $query = []) {
    if ($pages <= 1) return '';
    $html = '<nav><ul class="pagination pagination-sm justify-content-end">';
    $prev = max(1, $page - 1);
    $next = min($pages, $page + 1);
    $q = $query;
    $q['page'] = $prev;
    $html .= '<li class="page-item'.($page==1?' disabled':'').'"><a class="page-link" href="'.htmlspecialchars($baseUrl.'?'.http_build_query($q)).'">«</a></li>';
    for ($i = 1; $i <= $pages; $i++) {
        $q['page'] = $i;
        $html .= '<li class="page-item'.($i==$page?' active':'').'"><a class="page-link" href="'.htmlspecialchars($baseUrl.'?'.http_build_query($q)).'">'.$i.'</a></li>';
    }
    $q['page'] = $next;
    $html .= '<li class="page-item'.($page==$pages?' disabled':'').'"><a class="page-link" href="'.htmlspecialchars($baseUrl.'?'.http_build_query($q)).'">»</a></li>';
    $html .= '</ul></nav>';
    return $html;
}

?>

