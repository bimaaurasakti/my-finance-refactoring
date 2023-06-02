<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\History;
use App\Models\Type;
use Illuminate\Http\Request;
use Exception;

class HistoryController extends Controller
{
    public function histories(Request $request)
    {
        $page = 1;
        $limit = 0;
        $where = [];
        $orwhere = [];

        if ($request->input('page') != null) {
            $page = $request->input('page');
        }
        if ($request->input('results') != null) {
            $limit = $request->input('results');
        }
        if ($request->input('history_id') != null) {
            $where[] = ["history_id", $request->input('history_id')];
        }
        if ($request->input('history_transaction_id') != null) {
            $where[] = ["history_transaction_id", $request->input('history_transaction_id')];
        }
        if ($request->input('history_ending_balance') != null) {
            $where[] = ["history_ending_balance", $request->input('history_ending_balance')];
        }
        if ($request->input('history_source_balance') != null) {
            $where[] = ["history_source_balance", $request->input('history_source_balance')];
        }
        if ($request->input('source_name') != null) {
            $where[] = ["source_name", $request->input('source_name')];
        }
        if ($request->input('source_id') != null) {
            $orwhere[] = ["source_id", $request->input('source_id')];
            $orwhere[] = ["history_source_id", $request->input('source_id')];
        }
        if ($request->input('action') != null) {
            $where[] = ["action", $request->input('action')];
        }
        if ($request->input('history_user_id') != null) {
            $where[] = ["transaction_user_id", $request->input('history_user_id')];
        }


        if ($request->input('filters')) {
            if ($request->input('filters')['history_id']) {
                $where[] = ["sources.history_id", "ilike", "%" . $request->input('filters')['history_id'][0] . "%"];
            }
            if ($request->input('filters')['history_transaction_id']) {
                $where[] = ["sources.history_transaction_id", "ilike", "%" . $request->input('filters')['history_transaction_id'][0] . "%"];
            }
            if ($request->input('filters')['history_ending_balance']) {
                $where[] = ["sources.history_ending_balance", 'ilike', '%' . $request->input('filters')['history_ending_balance'][0] . '%'];
            }
            if ($request->input('filters')['history_source_balance']) {
                $where[] = ["sources.history_source_balance", "ilike", "%" . $request->input('filters')['history_source_balance'][0] . "%"];
            }
            if ($request->input('filters')['action']) {
                $where[] = ["sources.action", 'ilike', '%' . $request->input('filters')['action'][0] . '%'];
            }
        }

        $offset = ($page - 1) * $limit;

        try {
            $query = History::select(
                'action',
                'type_name',
                'transaction_id',
                'transaction_user_id',
                'source_from_history.source_id as source_history_id',
                'source_from_transaction.source_id as source_transaction_id',
                'history_source_id',
                'source_from_history.source_name as source_history_name',
                'source_from_transaction.source_name as source_transaction_name',
                'transaction_date',
                'transaction_description',
                'history_transaction_total',
                'history_source_balance',
                'history_ending_balance',
                'source_from_history.source_is_cancelled as source_history_cancelled',
                'source_from_transaction.source_is_cancelled as source_transaction_cancelled',
                'transaction_is_cancelled'
            )
                ->leftjoin('transactions', 'transaction_id', 'history_transaction_id')
                ->leftjoin('sources as source_from_history', 'source_from_history.source_id', 'history_source_id')
                ->leftjoin('sources as source_from_transaction', 'source_from_transaction.source_id', 'transaction_source_id')
                ->leftjoin('types', 'type_id', 'history_type_id')
                ->where($where)
                ->orwhere($orwhere);

            if ($request->input('sort_by') != null) {
                for ($i = 0; $i < sizeof($request->input('sort_by')); $i++) {
                    $sort_by = trans('sorting.histories.' . $request->input('sort_by')[$i]);
                    $order = $request->input('order')[$i];
                    $query->orderBy($sort_by, $order);
                }
            }

            $order = "asc";
            if ($request->input('order') == "descend") {
                $order = "desc";
            }

            if ($request->input('field') == "history_transaction_id") {
                $query->orderBy("history_transaction_id", $order);
            } else if ($request->input('field') == "transaction_total") {
                $query->orderBy("transaction_total", $order);
            } else if ($request->input('field') == "history_ending_balance") {
                $query->orderBy("history_ending_balance", $order);
            } else if ($request->input('field') == "history_source_balance") {
                $query->orderBy("history_source_balance", $order);
            } else if ($request->input('field') == "menu") {
                $query->orderBy("menu", $order);
            } else if ($request->input('field') == "action") {
                $query->orderBy("action", $order);
            } else {
                $query->orderBy('history_id', 'desc');
            }

            if ($limit > 0) {
                $query->offset($offset)->limit($limit);
                $sources = $query->paginate($limit);
            } else {
                $sources = $query->get();
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'old-request' => $request->all()], 400);
        }

        return $sources;
    }

    public function histories_income_expense(Request $request)
    {

        $user_id = $request->input('history_user_id');

        $income = History::select(DB::raw("SUM(history_transaction_total) as total"))
            ->leftjoin('types', 'type_id', 'history_type_id')
            ->leftjoin('transactions', 'transaction_id', 'history_transaction_id')
            ->leftjoin('sources', 'source_id', 'history_source_id')
            ->where('type_name', 'Pemasukan')

            ->where(function ($income) use ($user_id) {
                $income->where("transaction_user_id", $user_id)->orWhere("source_user_id", $user_id);
            })
            ->where(function ($income) {
                $income->where('transaction_is_cancelled', 0)->orWhere("source_is_cancelled", 0);
            })
            ->first();

        $expense = History::select(DB::raw("SUM(history_transaction_total) as total"))
            ->leftjoin('types', 'type_id', 'history_type_id')
            ->leftjoin('transactions', 'transaction_id', 'history_transaction_id')
            ->leftjoin('sources', 'source_id', 'history_source_id')
            ->where('transaction_is_cancelled', 0)
            ->where('type_name', 'Pengeluaran')
            ->where(function ($expense) use ($user_id) {
                $expense->where("transaction_user_id", $user_id)->orWhere("source_user_id", $user_id);
            })
            ->where(function ($expense) {
                $expense->where('transaction_is_cancelled', 0)->orWhere("source_is_cancelled", 0);
            })
            ->first();

        $data = [
            'income' => $income->total,
            'expense' => $expense->total
        ];

        return $data;
    }

    public function store_from_transaction(Request $request, $previous_data)
    {
        $action = 'Create';
        $type = Type::select('type_name')->where("type_id", $request->input('transaction_type_id'))->get();

        $ending_balance = $previous_data['last_history']->history_ending_balance;
        if ($type[0]->type_name == 'Pemasukan') {
            $ending_balance =  $ending_balance + $request->input('transaction_total');
        } else if ($type[0]->type_name == 'Pengeluaran') {
            $ending_balance =   $ending_balance - $request->input('transaction_total');
        }

        $object = [
            'history_transaction_id' => $previous_data['previous_transaction']->transaction_id,
            'history_transaction_total' => $previous_data['previous_transaction']->transaction_total,
            'history_ending_balance' => $ending_balance,
            'history_source_balance' => $previous_data['source_ending_balance'],
            'history_type_id' => $previous_data['previous_transaction']->transaction_type_id,
            'action' => $action,
        ];

        try {
            $history = History::create($object);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'old-request' => $request->all()], 400);
        }

        return response()->json(['data' => $history], 201);
    }

    public function update_from_transaction(Request $request, $previous_data)
    {
        $action = 'Update';
        $type = Type::select('type_name')->where("type_id", $request->input('transaction_type_id'))->first();

        $ending_balance = $previous_data['last_history']->history_ending_balance;
        if ($request->input('transaction_type_id') !=  $previous_data['previous_transaction']->transaction_type_id) {
            if ($type->type_name == 'Pemasukan') {
                $ending_balance =  $ending_balance + $previous_data['previous_transaction']->transaction_total + $request->input('transaction_total');
            } else if ($type->type_name == 'Pengeluaran') {
                $ending_balance =  $ending_balance - $previous_data['previous_transaction']->transaction_total - $request->input('transaction_total');
            }
        } else {
            if ($type->type_name == 'Pemasukan') {
                $ending_balance = $ending_balance + $request->input('transaction_total') - $previous_data['previous_transaction']->transaction_total; // 109 + 5 -4
            } else if ($type->type_name == 'Pengeluaran') {
                $ending_balance = $ending_balance + $previous_data['previous_transaction']->transaction_total - $request->input('transaction_total'); // 109 + 5 -4
            }
        }

        $object = [
            'history_transaction_id' => $previous_data['previous_transaction']->transaction_id,
            'history_transaction_total' => $request->input('transaction_total'),
            'history_ending_balance' => $ending_balance,
            'history_source_balance' => $previous_data['source_ending_balance'],
            'history_type_id' => $request->input('transaction_type_id'),
            'action' => $action,
        ];

        try {
            $history = History::create($object);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'old-request' => $request->all()], 400);
        }

        return response()->json(['data' => $history], 201);
    }

    public function delete_from_transaction(Request $request, $previous_data)
    {
        $action = 'Delete';
        $ending_balance = $previous_data['last_history']->history_ending_balance;
        $ending_balance =   $ending_balance + $previous_data['previous_transaction']->transaction_total;

        $object = [
            'history_transaction_id' => $previous_data['previous_transaction']->transaction_id,
            'history_transaction_total' => $previous_data['previous_transaction']->transaction_total,
            'history_ending_balance' => $ending_balance,
            'history_source_balance' => $previous_data['source_ending_balance'],
            'history_type_id' => $previous_data['previous_transaction']->transaction_type_id,
            'action' => $action,
        ];

        try {
            $history = History::create($object);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'old-request' => $request->all()], 400);
        }

        return response()->json(['data' => $history], 201);
    }

    public function store_from_source(Request $request, $source_data)
    {
        $action = 'Create';
        $last_history =  History::select('history_ending_balance')
            ->leftjoin('sources', 'source_id', 'history_source_id')
            ->where("source_user_id", $request->input('source_user_id'))->orderBy('history_id', 'desc')->first();

        $type = Type::select('type_id')->where("type_name", 'Pemasukan')->first();

        $ending_balance = $last_history ? $last_history->history_ending_balance : 0;
        $ending_balance = $ending_balance + $source_data->source_ending_balance;

        $object = [
            'history_transaction_total' => $source_data->source_ending_balance,
            'history_ending_balance' => $ending_balance,
            'history_source_balance' =>  $source_data->source_ending_balance,
            'history_type_id' => $type->type_id,
            'action' => $action,
            'history_source_id' => $source_data->source_id
        ];

        try {
            $history = History::create($object);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'old-request' => $request->all()], 400);
        }

        return response()->json([$history], 201);
    }

    public function delete_from_source(Request $request, $source_data)
    {
        $action = 'Delete';
        $last_history =  History::select('history_ending_balance')
            ->leftjoin('sources', 'source_id', 'history_source_id')
            ->where("source_user_id", $source_data->source_user_id)
            ->orderBy('history_id', 'desc')->first();

        $type = Type::select('type_id')->where("type_name", 'Pengeluaran')->first();

        $ending_balance = $last_history ? $last_history->history_ending_balance : 0;
        $source_balance = 0;
        $ending_balance = $ending_balance -  $source_data->source_ending_balance;

        $object = [
            'history_transaction_total' => $source_data->source_ending_balance,
            'history_ending_balance' => $ending_balance,
            'history_source_balance' => $source_balance,
            'history_type_id' => $type->type_id,
            'action' => $action,
            'history_source_id' => $source_data->source_id
        ];

        try {
            $history = History::create($object);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'old-request' => $request->all()], 400);
        }

        return response()->json([$history], 201);
    }
}
