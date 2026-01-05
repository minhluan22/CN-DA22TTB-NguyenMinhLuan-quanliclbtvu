<?php

namespace App\Http\Controllers\Admin\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;

trait HandlesFiltering
{
    /**
     * Áp dụng filter chung cho query
     * 
     * @param Builder|QueryBuilder $query
     * @param Request $request
     * @param array $filterConfig Cấu hình filter: ['field_name' => ['type' => 'exact|like|date|relation', 'column' => 'column_name', ...]]
     * @return Builder|QueryBuilder
     */
    protected function applyFilters($query, Request $request, array $filterConfig = [])
    {
        // Filter mặc định: club_id, status, approval_status
        $defaultFilters = [
            'club_id' => ['type' => 'exact', 'column' => 'club_id'],
            'status' => ['type' => 'exact', 'column' => 'status'],
            'approval_status' => ['type' => 'exact', 'column' => 'approval_status'],
            'user_id' => ['type' => 'exact', 'column' => 'user_id'],
            'severity' => ['type' => 'exact', 'column' => 'severity'],
            'priority' => ['type' => 'exact', 'column' => 'priority'],
        ];

        $allFilters = array_merge($defaultFilters, $filterConfig);

        foreach ($allFilters as $field => $config) {
            if (!$request->filled($field)) {
                continue;
            }

            // Skip filter nếu type là 'skip' (dùng để override defaultFilters nhưng không áp dụng filter)
            $type = $config['type'] ?? 'exact';
            if ($type === 'skip') {
                continue;
            }

            $value = $request->input($field);
            $column = $config['column'] ?? $field;

            switch ($type) {
                case 'exact':
                    $query->where($column, $value);
                    break;
                
                case 'like':
                    $query->where($column, 'like', "%{$value}%");
                    break;
                
                case 'date':
                    $dateField = $config['date_field'] ?? $column;
                    if ($field === 'start_date' || $field === 'date_from') {
                        $query->whereDate($dateField, '>=', $value);
                    } elseif ($field === 'end_date' || $field === 'date_to') {
                        $query->whereDate($dateField, '<=', $value);
                    } else {
                        $query->whereDate($dateField, $value);
                    }
                    break;
                
                case 'relation':
                    $relation = $config['relation'] ?? null;
                    $relationColumn = $config['relation_column'] ?? 'name';
                    if ($relation) {
                        $query->whereHas($relation, function($q) use ($relationColumn, $value) {
                            $q->where($relationColumn, 'like', "%{$value}%");
                        });
                    }
                    break;
                
                case 'custom':
                    $callback = $config['callback'] ?? null;
                    if (is_callable($callback)) {
                        $callback($query, $value);
                    }
                    break;
            }
        }

        return $query;
    }

    /**
     * Áp dụng tìm kiếm chung cho query
     * 
     * @param Builder|QueryBuilder $query
     * @param Request $request
     * @param array $searchFields Các trường để tìm kiếm: ['column1', 'column2', 'relation:column']
     * @return Builder|QueryBuilder
     */
    protected function applySearch($query, Request $request, array $searchFields = [])
    {
        if (!$request->filled('search')) {
            return $query;
        }

        $search = $request->input('search');
        
        // Nếu không có cấu hình, sử dụng mặc định
        if (empty($searchFields)) {
            $searchFields = ['title', 'name', 'code', 'description'];
        }

        $query->where(function($q) use ($search, $searchFields) {
            foreach ($searchFields as $field) {
                // Kiểm tra nếu là relation search (format: "relation:column")
                if (strpos($field, ':') !== false) {
                    [$relation, $column] = explode(':', $field, 2);
                    $q->orWhereHas($relation, function($relationQuery) use ($column, $search) {
                        $relationQuery->where($column, 'like', "%{$search}%");
                    });
                } else {
                    $q->orWhere($field, 'like', "%{$search}%");
                }
            }
        });

        return $query;
    }

    /**
     * Áp dụng date range filter
     * 
     * @param Builder|QueryBuilder $query
     * @param Request $request
     * @param string $dateColumn Tên cột ngày tháng
     * @return Builder|QueryBuilder
     */
    protected function applyDateRange($query, Request $request, string $dateColumn = 'created_at')
    {
        if ($request->filled('start_date') || $request->filled('date_from')) {
            $startDate = $request->input('start_date') ?? $request->input('date_from');
            $query->whereDate($dateColumn, '>=', $startDate);
        }

        if ($request->filled('end_date') || $request->filled('date_to')) {
            $endDate = $request->input('end_date') ?? $request->input('date_to');
            $query->whereDate($dateColumn, '<=', $endDate);
        }

        return $query;
    }

    /**
     * Áp dụng pagination với query string
     * 
     * @param Builder|QueryBuilder $query
     * @param int $perPage Số item mỗi trang
     * @param string $orderBy Cột để sắp xếp
     * @param string $orderDirection Hướng sắp xếp (asc/desc)
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function paginateWithQueryString($query, int $perPage = 10, string $orderBy = 'created_at', string $orderDirection = 'desc')
    {
        return $query->orderBy($orderBy, $orderDirection)
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Áp dụng tất cả filters, search và pagination
     * 
     * @param Builder|QueryBuilder $query
     * @param Request $request
     * @param array $options Các tùy chọn: ['filters' => [], 'searchFields' => [], 'perPage' => 10, 'orderBy' => 'created_at', 'orderDirection' => 'desc', 'dateColumn' => 'created_at']
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function applyCommonFilters($query, Request $request, array $options = [])
    {
        // Áp dụng filters
        $filters = $options['filters'] ?? [];
        $query = $this->applyFilters($query, $request, $filters);

        // Áp dụng date range
        $dateColumn = $options['dateColumn'] ?? 'created_at';
        $query = $this->applyDateRange($query, $request, $dateColumn);

        // Áp dụng search
        $searchFields = $options['searchFields'] ?? [];
        $query = $this->applySearch($query, $request, $searchFields);

        // Pagination
        $perPage = $options['perPage'] ?? 10;
        $orderBy = $options['orderBy'] ?? 'created_at';
        $orderDirection = $options['orderDirection'] ?? 'desc';

        return $this->paginateWithQueryString($query, $perPage, $orderBy, $orderDirection);
    }

    /**
     * Xử lý filter đặc biệt cho approval_status (có thể map sang status nếu là 'disabled')
     * 
     * @param Builder|QueryBuilder $query
     * @param Request $request
     * @param string $statusColumn Tên cột status
     * @param string $approvalColumn Tên cột approval_status
     * @return Builder|QueryBuilder
     */
    protected function applyApprovalStatusFilter($query, Request $request, string $statusColumn = 'status', string $approvalColumn = 'approval_status')
    {
        if ($request->filled('approval_status')) {
            $approvalStatus = $request->input('approval_status');
            
            if ($approvalStatus === 'disabled') {
                // Nếu chọn "Bị vô hiệu hóa", filter theo status = 'disabled'
                $query->where($statusColumn, 'disabled');
            } else {
                // Các trạng thái duyệt khác
                $query->where($approvalColumn, $approvalStatus);
            }
        }

        return $query;
    }
}

