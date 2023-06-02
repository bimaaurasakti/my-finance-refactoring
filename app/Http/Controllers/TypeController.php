<?php

namespace App\Http\Controllers;

use App\Http\Requests\TypeRequest;
use App\Models\Type;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;

class TypeController extends Controller
{
    public function types(Request $request)
    {
        $page = 1;
        $limit = 0;
        $where = [];

        if ($request->input('page') != null) {
            $page = $request->input('page');
        }
        if ($request->input('results') != null) {
            $limit = $request->input('results');
        }
        if ($request->input('type_id') != null) {
            $where[] = ["type_id", $request->input('type_id')];
        }
        if ($request->input('type_name') != null) {
            $where[] = ["type_name", $request->input('type_name')];
        }
        if ($request->input('type_description') != null) {
            $where[] = ["type_description", $request->input('type_description')];
        }

        if ($request->input('filters')) {
            if ($request->input('filters')['type_id']) {
                $where[] = ["sources.type_id", "ilike", "%" . $request->input('filters')['type_id'][0] . "%"];
            }
            if ($request->input('filters')['type_name']) {
                $where[] = ["sources.type_name", "ilike", "%" . $request->input('filters')['type_name'][0] . "%"];
            }
            if ($request->input('filters')['type_description']) {
                $where[] = ["sources.type_description", 'ilike', '%' . $request->input('filters')['type_description'][0] . '%'];
            }
        }

        $offset = ($page - 1) * $limit;

        try {
            $query = Type::where($where);

            if ($request->input('sort_by') != null) {
                for ($i = 0; $i < sizeof($request->input('sort_by')); $i++) {
                    $sort_by = trans('sorting.types.' . $request->input('sort_by')[$i]);
                    $order = $request->input('order')[$i];
                    $query->orderBy($sort_by, $order);
                }
            }

            $order = "asc";
            if ($request->input('order') == "descend") {
                $order = "desc";
            }

            if ($request->input('field') == "type_name") {
                $query->orderBy("type_name", $order);
            } else if ($request->input('field') == "type_description") {
                $query->orderBy("type_description", $order);
            } else {
                $query->orderBy('type_id', 'desc');
            }

            if ($limit > 0) {
                $query->offset($offset)->limit($limit);
                $types = $query->paginate($limit);
            } else {
                $types = $query->get();
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'old-request' => $request->all()], 400);
        }

        return $types;
    }

    public function store(TypeRequest $request)
    {
        try {
            $type = Type::create($request->only('type_name', 'type_description'));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'old-request' => $request->all()], 400);
        }

        return response()->json(['data' => $type], 201);
    }


    public function update(TypeRequest $request, $id)
    {
        try {
            $type_updated_rows = Type::where("type_id", $id)
                ->update($request->only('type_name', 'type_description'));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'old-request' => $request->all()], 400);
        }

        return response()->json(['type_updated_rows' => $type_updated_rows], 200);
    }

    public function destroy(Request $request, $id)
    {
        try {
            $type_deleted_rows = Type::where("type_id", $id)->delete();
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'old-request' => $request->all()], 400);
        }

        return response()->json([
            'type_deleted_rows' => $type_deleted_rows
        ], 200);
    }
}
