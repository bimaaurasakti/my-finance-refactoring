<?php

namespace App\Http\Controllers;

use App\Models\Source;
use App\Models\Transaction;
use App\Models\Type;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;

class SourceController extends Controller
{
    public function sources(Request $request)
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
        if ($request->input('source_id') != null) {
            $where[] = ["source_id", $request->input('source_id')];
        }
        if ($request->input('source_name') != null) {
            $where[] = ["source_name", $request->input('source_name')];
        }
        if ($request->input('source_user_id') != null) {
            $where[] = ["source_user_id", $request->input('source_user_id')];
        }
        if ($request->input('beginning_balance') != null) {
            $where[] = ["beginning_balance", $request->input('beginning_balance')];
        }
        if ($request->input('source_ending_balance') != null) {
            $where[] = ["source_ending_balance", $request->input('source_ending_balance')];
        }
        if ($request->input('source_is_cancelled') != null) {
            $where[] = ["source_is_cancelled", $request->input('source_is_cancelled')];
        }
        if ($request->input('check_ending_balance') == 1) {
            $where[] = ["source_ending_balance", ">", 0];
        }

        if ($request->input('filters')) {
            if ($request->input('filters')['source_name']) {
                $where[] = ["sources.source_name", "ilike", "%" . $request->input('filters')['source_name'][0] . "%"];
            }
            if ($request->input('filters')['source_beginning_balance']) {
                $where[] = ["sources.source_beginning_balance", "ilike", "%" . $request->input('filters')['source_beginning_balance'][0] . "%"];
            }
            if ($request->input('filters')['source_ending_balance']) {
                $where[] = ["sources.source_ending_balance", 'ilike', '%' . $request->input('filters')['source_ending_balance'][0] . '%'];
            }
            if ($request->input('filters')['source_user_id']) {
                $where[] = ["sources.source_user_id", 'ilike', '%' . $request->input('filters')['source_user_id'][0] . '%'];
            }
        }

        $offset = ($page - 1) * $limit;

        try {
            $query = Source::where($where);

            if ($request->input('sort_by') != null) {
                for ($i = 0; $i < sizeof($request->input('sort_by')); $i++) {
                    $sort_by = trans('sorting.sources.' . $request->input('sort_by')[$i]);
                    $order = $request->input('order')[$i];
                    $query->orderBy($sort_by, $order);
                }
            }

            $order = "asc";
            if ($request->input('order') == "descend") {
                $order = "desc";
            }

            if ($request->input('field') == "source_name") {
                $query->orderBy("source_name", $order);
            } else if ($request->input('field') == "beginning_balance") {
                $query->orderBy("beginning_balance", $order);
            } else if ($request->input('field') == "source_ending_balance") {
                $query->orderBy("source_ending_balance", $order);
            } else if ($request->input('field') == "source_user_id") {
                $query->orderBy("source_user_id", $order);
            } else {
                $query->orderBy('source_id', 'desc');
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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'source_name' => 'required|max:50',
            'beginning_balance' => 'required|numeric',
            'source_user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages(), 'old-request' => $request->all()], 400);
        }

        $check_source = Source::where('source_name', $request->input('source_name'))->where('source_user_id', $request->input('source_user_id'))->get();
        if (count($check_source) > 0) {
            return response()->json(['error' => [
                'message' => 'Sumber Dana Sudah Tersedia'
            ]], 400);
        }

        $object = [
            "source_name" => $request->input('source_name'),
            "beginning_balance" => $request->input('beginning_balance'),
            "source_ending_balance" => $request->input('beginning_balance'),
            "source_user_id" => $request->input('source_user_id'),
        ];

        try {
            $source = Source::create($object);
            $create_history = app('App\Http\Controllers\HistoryController')->store_from_source($request, $source);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'old-request' => $request->all()], 400);
        }

        return response()->json(['data' => [
            'created_source' => $source,
            'history' => $create_history
        ]], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'source_name' => 'required|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages(), 'old-request' => $request->all()], 400);
        }

        try {
            $source_updated_rows = Source::where("source_id", $id)
                ->update($request->only('source_name'));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'old-request' => $request->all()], 400);
        }

        return response()->json(['source_updated_rows' => $source_updated_rows], 200);
    }

    public function source_create_ending_balance(Request $request)
    {
        $source_ending_balance = Source::select('source_ending_balance')->where("source_id", $request->input('transaction_source_id'))->get();
        $type = Type::select('type_name')->where("type_id", $request->input('transaction_type_id'))->get();

        $ending_balance = $source_ending_balance[0]->source_ending_balance;
        if ($type[0]->type_name == 'Pemasukan') {
            $ending_balance =  $ending_balance + $request->input('transaction_total');
        } else if ($type[0]->type_name == 'Pengeluaran') {
            $ending_balance =  $ending_balance - $request->input('transaction_total');
        }

        $update_source_balance =  Source::where("source_id", $request->input('transaction_source_id'))->first();
        $update_source_balance->source_ending_balance = $ending_balance;
        $update_source_balance->save();

        return $update_source_balance;
    }

    public function source_update_ending_balance(Request $request, $previous_transaction)
    {
        $type = Type::select('type_name', 'type_id')->where("type_id", $request->input('transaction_type_id'))->first();

        $difference_total = $request->input('transaction_total');
        if ($previous_transaction->transaction_type_id == $type->type_id) {
            if ($type->type_name == 'Pemasukan') {
                $difference_total =  $previous_transaction->transaction_total - $difference_total;
            } else if ($type->type_name == 'Pengeluaran') {
                $difference_total = $difference_total - ($previous_transaction->transaction_total * -1);
            }
        } else {
            if ($previous_transaction->type_name == 'Pengeluaran' && $type->type_name == 'Pemasukan') {
                $difference_total = ($difference_total * -1) - $previous_transaction->transaction_total;
            } else if ($previous_transaction->type_name == 'Pemasukan' && $type->type_name == 'Pengeluaran') {
                $difference_total = $previous_transaction->transaction_total - ($difference_total * -1);
            }
        }

        if ($request->input('transaction_source_id') != $previous_transaction->transaction_source_id) {
            $difference_total = $request->input('transaction_total');

            $update_source_balance = Source::where("source_id", $request->input('transaction_source_id'))->orderBy('source_id', 'desc')->first();
            $last_source_balance_prev = Source::where("source_id", $previous_transaction->transaction_source_id)->orderBy('source_id', 'desc')->first();

            $ending_balance_new_source = $update_source_balance->source_ending_balance - $difference_total;
            $update_source_balance->source_ending_balance = $ending_balance_new_source;
            $update_source_balance->save();

            $ending_balance_prev_source = $last_source_balance_prev->source_ending_balance + $difference_total;
            $last_source_balance_prev->source_ending_balance = $ending_balance_prev_source;
            $last_source_balance_prev->save();
        } else {
            $last_source_balance = Source::select('source_ending_balance')->where("source_id", $request->input('transaction_source_id'))->orderBy('source_id', 'desc')->first();

            $ending_balance = $last_source_balance->source_ending_balance - $difference_total;
            $update_source_balance = Source::where("source_id", $previous_transaction->transaction_source_id)->orderBy('source_id', 'desc')->first();
            $update_source_balance->source_ending_balance = $ending_balance;
            $update_source_balance->save();
        }

        return $update_source_balance;
    }

    public function source_delete_transaction($request)
    {
        $source_ending_balance = Source::select('source_ending_balance')->where("source_id", $request->transaction_source_id)->orderBy('source_id', 'desc')->first();
        $ending_balance = $source_ending_balance->source_ending_balance + $request->transaction_total; //65 + 10

        $update_source_balance =  Source::where("source_id", $request->transaction_source_id)->first();
        $update_source_balance->source_ending_balance = $ending_balance;
        $update_source_balance->save();

        return $update_source_balance;
    }

    public function destroy(Request $request, $id)
    {
        $transaction = Transaction::where('transaction_source_id', $id)->where('transaction_is_cancelled', 0)->first();
        if ($transaction) {
            return response()->json(['error' => [
                'message' => 'Sumber dana tidak dapat dihapus'
            ]], 400);
        }
        try {
            $source = Source::where("source_id", $id)->first();
            $source->source_is_cancelled =  true;
            $source->source_name =  $source->source_name . '_cancel';
            $source->save();

            $create_history = app('App\Http\Controllers\HistoryController')->delete_from_source($request, $source);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'old-request' => $request->all()], 400);
        }

        return response()->json([
            'source_deleted_rows' => $source,
            'history' => $create_history
        ], 200);
    }
}
