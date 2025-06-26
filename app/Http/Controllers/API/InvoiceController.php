<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index()
    {
        
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_no' => 'required',
            'customer_no' => 'required',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required',
            'items.*.item_description' => 'required',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.sub_total' => 'required|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        
        try {
            $grandTotal = collect($request->items)->sum(function ($item) {
                return floatval($item['sub_total']);
            });
            
            $invoice = null;
            
            if (!empty($request->invoice_no)) {
                $invoice = \App\Models\Invoice::where('invoice_no', $request->invoice_no)->first();
                if ($invoice) {
                    $invoice->grand_total = $grandTotal;
                    $invoice->save();
                }
            }
            
            if (!$invoice) {
                $last = \App\Models\Invoice::orderBy('invoice_no', 'desc')
                ->where('invoice_no', 'like', 'INV%')
                ->first();
                
                if ($last && preg_match('/INV(\d+)/', $last->invoice_no, $matches)) {
                    $nextNumber = intval($matches[1]) + 1;
                } else {
                    $nextNumber = 1;
                }
                
                $invoiceNo = 'INV' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                
                $invoice = \App\Models\Invoice::create([
                    'invoice_no' => $invoiceNo,
                    'employee_no' => $request->employee_no,
                    'customer_no' => $request->customer_no,
                    'grand_total' => $grandTotal,
                    'discount_percentage' => $request->discount_percentage ?? 0,
                    'discount_amount' => $request->discount_amount ?? 0,
                    'status' => 1,
                ]);
            }
      
            $lastItem = collect($request->items)->last(); 
            $invoice->items()->create([
                'item_id' => $lastItem['item_id'],
                'item_description' => $lastItem['item_description'],
                'quantity' => $lastItem['quantity'],
                'amount' => $lastItem['amount'],
                'discount_percentage' => $lastItem['discount_percentage'] ?? 0,
                'discount_amount' => $lastItem['discount_amount'] ?? 0,
                'sub_total' => $lastItem['sub_total'],
            ]);
            
            DB::commit();
            
            // return response()->json([
            //     'message' => $invoice->wasRecentlyCreated ? 'Invoice created successfully' : 'Invoice grand total updated and item added.',
            //     'invoice_no' => $invoice->invoice_no,
            //     'invoice_id' => $invoice->id,
            // ], $invoice->wasRecentlyCreated ? 201 : 200);
            
            $allItems = $invoice->items()->get()->makeHidden(['created_at', 'updated_at', 'deleted_at']);
            
            return response()->json([
                'message' => $invoice->wasRecentlyCreated ? 'Invoice created successfully' : 'Invoice updated.',
                'invoice' => [
                    'id' => $invoice->id,
                    'invoice_no' => $invoice->invoice_no,
                    'employee_no' => $invoice->employee_no,
                    'customer_no' => $invoice->customer_no,
                    'token_no' => $invoice->invoice_no,
                    'items' => $allItems
                    ]
                ], $invoice->wasRecentlyCreated ? 201 : 200);
                
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Failed to process invoice',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
        
        
        
        
        public function show(string $id)
        {
            
        }
        
        public function update(Request $request, string $id)
        {
            
        }
        
        public function destroy(string $id)
        {
            
        }
        
        public function loadItemDropdown(Request $request)
        {
            $search = $request->get('q');
            
            $employees = \App\Models\Service::where('code', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->limit(10)
            ->orderBy('code', 'asc')
            ->get();
            
            $results = [];
            
            foreach ($employees as $emp) {
                $results[] = [
                    'label' => $emp->code . ' - ' . $emp->description,
                    'value' => $emp->id,
                    'price' => $emp->price
                ];
            }
            
            return response()->json($results);
        }
        
        public function loadInvoiceDropdown(Request $request)
        {
            $search = $request->get('q');
            
            $invoices = \App\Models\Invoice::where('id', 'like', "%$search%")
            ->limit(10)
            ->orderBy('id', 'asc')
            ->get();
            
            $results = [];
            
            foreach ($invoices as $inv) {
                $results[] = [
                    'label' => $inv->invoice_no,
                    'value' => $inv->id
                ];
            }
            
            return response()->json($results);
        }

       public function getInvoiceItems($id)
        {
            $invoice = \App\Models\Invoice::with(['items', 'employee', 'customer'])->findOrFail($id);

            $items = $invoice->items->makeHidden(['created_at', 'updated_at', 'deleted_at']);

            return response()->json([
                'invoice_id' => $invoice->id,
                'invoice_no' => $invoice->invoice_no,
                'employee_no' => $invoice->employee_no,
                'employee_name' => $invoice->employee->name ?? '',  // Ensure relation is loaded
                'customer_no' => $invoice->customer_no,
                'customer_name' => $invoice->customer->name ?? '',
                'token_no' => $invoice->invoice_no,
                'items' => $items,
            ]);
        }      
        
        
    }
    