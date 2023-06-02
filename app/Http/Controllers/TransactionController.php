<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\History;
use App\Models\Source;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Models\Transaction;
use App\Models\Type;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    public function index()
    {
        $data = Transaction::with('types')->get();
        return view('pages.transaksi', ['type_menu' => 'transaksi', 'data' => $data]);
    }

    public function transactions(Request $request)
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
        if ($request->input('transaction_id') != null) {
            $where[] = ["transaction_id", $request->input('transaction_id')];
        }
        if ($request->input('transaction_user_id') != null) {
            $where[] = ["transaction_user_id", $request->input('transaction_user_id')];
        }
        if ($request->input('transaction_source_id') != null) {
            $where[] = ["transaction_source_id", $request->input('transaction_source_id')];
        }
        if ($request->input('transaction_type_id') != null) {
            $where[] = ["transaction_type_id", $request->input('transaction_type_id')];
        }
        if ($request->input('transaction_date') != null) {
            $where[] = ["transaction_date", $request->input('transaction_date')];
        }
        if ($request->input('transaction_total') != null) {
            $where[] = ["transaction_total", $request->input('transaction_total')];
        }
        if ($request->input('transaction_description') != null) {
            $where[] = ["transaction_description", $request->input('transaction_description')];
        }
        if ($request->input('transaction_is_cancelled') != null) {
            $where[] = ["transaction_is_cancelled", $request->input('transaction_is_cancelled')];
        }

        if ($request->input('filters')) {
            if ($request->input('filters')['transaction_id']) {
                $where[] = ["transactions.transaction_id", "ilike", "%" . $request->input('filters')['transaction_id'][0] . "%"];
            }
            if ($request->input('filters')['transaction_user_id']) {
                $where[] = ["transactions.transaction_user_id", "ilike", "%" . $request->input('filters')['transaction_user_id'][0] . "%"];
            }
            if ($request->input('filters')['transaction_source_id']) {
                $where[] = ["transactions.transaction_source_id", 'ilike', '%' . $request->input('filters')['transaction_source_id'][0] . '%'];
            }
            if ($request->input('filters')['transaction_type_id']) {
                $where[] = ["transactions.transaction_type_id", "ilike", "%" . $request->input('filters')['transaction_type_id'][0] . "%"];
            }
            if ($request->input('filters')['transaction_date']) {
                $where[] = ["transactions.transaction_date", "ilike", "%" . $request->input('filters')['transaction_date'][0] . "%"];
            }
            if ($request->input('filters')['transaction_total']) {
                $where[] = ["transactions.transaction_total", 'ilike', '%' . $request->input('filters')['transaction_total'][0] . '%'];
            }
            if ($request->input('filters')['transaction_description']) {
                $where[] = ["transactions.transaction_description", 'ilike', '%' . $request->input('filters')['transaction_description'][0] . '%'];
            }
        }

        $offset = ($page - 1) * $limit;

        try {
            $query = Transaction::leftjoin('types', 'type_id', 'transaction_type_id')->leftjoin('sources', 'source_id', 'transaction_source_id')->where($where);

            if ($request->input('sort_by') != null) {
                for ($i = 0; $i < sizeof($request->input('sort_by')); $i++) {
                    $sort_by = trans('sorting.transactions.' . $request->input('sort_by')[$i]);
                    $order = $request->input('order')[$i];
                    $query->orderBy($sort_by, $order);
                }
            }

            $order = "asc";
            if ($request->input('order') == "descend") {
                $order = "desc";
            }

            if ($request->input('field') == "transaction_user_id") {
                $query->orderBy("transaction_user_id", $order);
            } else if ($request->input('field') == "transaction_source_id") {
                $query->orderBy("transaction_source_id", $order);
            } else if ($request->input('field') == "transaction_type_id") {
                $query->orderBy("transaction_type_id", $order);
            } else if ($request->input('field') == "transaction_date") {
                $query->orderBy("transaction_date", $order);
            } else if ($request->input('field') == "transaction_total") {
                $query->orderBy("transaction_total", $order);
            } else if ($request->input('field') == "transaction_description") {
                $query->orderBy("transaction_description", $order);
            } else {
                $query->orderBy('transaction_id', 'desc');
            }

            if ($limit > 0) {
                $query->offset($offset)->limit($limit);
                $transactions = $query->paginate($limit);
            } else {
                $transactions = $query->get();
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'old-request' => $request->all()], 400);
        }

        return $transactions;
    }

    public function store(TransactionRequest $request)
    {
        $type = Type::where('type_id', $request->input('transaction_type_id'))->first();
        if ($type->type_name == 'Pengeluaran') {
            $check = Source::select('source_ending_balance')->where('source_id', $request->input('transaction_source_id'))->first();
            if ($request->input('transaction_total') > $check->source_ending_balance) {
                return response()->json(['error' => ['message' => 'Saldo tidak cukup untuk transaksi ini']], Response::HTTP_BAD_REQUEST);
            }
        }

        try {
            $transaction = Transaction::create(
                $request->only([
                    'transaction_user_id', 
                    'transaction_source_id', 
                    'transaction_type_id', 
                    'transaction_date', 
                    'transaction_total', 
                    'transaction_description'
                ])
            );
            $last_history = History::select('history_ending_balance')->orderBy('history_id', 'desc')->first();
            $update_source = app('App\Http\Controllers\SourceController')->source_create_ending_balance($request);
            $create_history = app('App\Http\Controllers\HistoryController')->store_from_transaction($request, [
                'source_ending_balance' =>  $update_source->source_ending_balance,
                'last_history' => $last_history,
                'previous_transaction' => $transaction
            ]);

            return response()->json([
                'data' => [
                    'transaction' => $transaction,
                    'update_source' => $update_source,
                    'create_history' => $create_history
                ]
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'old-request' => $request->all()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(TransactionRequest $request, $id)
    {
        try {
            $previous_transaction =  Transaction::select('transaction_id', 'transaction_total', 'transaction_type_id', 'transaction_source_id', 'type_name', 'source_ending_balance')
                ->leftJoin('types', 'transaction_type_id', 'type_id')
                ->leftJoin('sources', 'transaction_source_id', 'source_id')
                ->where("transaction_id", $id)->orderBy('transaction_id', 'desc')->first();
            $last_history =  History::select('history_ending_balance')->where('history_transaction_id', $id)->orderBy('history_id', 'desc')->first();
            $update_source = app('App\Http\Controllers\SourceController')->source_update_ending_balance($request, $previous_transaction);
            $create_history = app('App\Http\Controllers\HistoryController')->update_from_transaction($request, [
                'source_ending_balance' =>  $update_source->source_ending_balance,
                'last_history' => $last_history,
                'previous_transaction' => $previous_transaction
            ]);
            $update_transaction = Transaction::where("transaction_id", $id)->update($request->only([
                'transaction_user_id', 
                'transaction_source_id', 
                'transaction_type_id', 
                'transaction_date', 
                'transaction_total', 
                'transaction_description'
            ]));

            return response()->json([
                'data' => [
                    'update_transaction' => $update_transaction,
                    'update_source' => $update_source,
                    'create_history' => $create_history
                ]
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'old-request' => $request->all()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function destroy($id)
    {
        try {
            $transaction = Transaction::where("transaction_id", $id)->orderBy('transaction_id', 'desc')->first();
            $transaction->transaction_is_cancelled =  true;
            $transaction->save();

            $update_source = app('App\Http\Controllers\SourceController')->source_delete_transaction($transaction);
            $last_history =  History::select('history_ending_balance')->where('history_transaction_id', $id)->orderBy('history_id', 'desc')->first();
            $create_history = app('App\Http\Controllers\HistoryController')->delete_from_transaction(new Request(), [
                'source_ending_balance' =>  $update_source->source_ending_balance,
                'previous_transaction' => $transaction,
                'last_history' => $last_history
            ]);

            return response()->json([
                'data' => [
                    'transaction_deleted_rows' => $transaction,
                    'update_source' => $update_source,
                    'create_history' => $create_history
                ]
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
