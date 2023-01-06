<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\BillResource;
use App\Models\Bill;
use App\Models\bill_usercategory;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validate the Bills
        $request->validate([
            'bill' => 'required|string|unique:bills,bill_name',
            'description' => 'required|string',
            'amount' => 'required|numeric|between:0,9999999999.99',
            'pay_by' => 'required',
            'start_at' => ['date','date_format:Y-m-d','before:end_at'],
            'end_at'   => 'date|date_format:Y-m-d|after:start_at',
        ]);

        //$payable = $request->pay_by;
        //return response()->json($payable);
        
        //create the bill
        $bill = Bill::create([
            'bill_name' => $request->bill,
            'description' => $request->description,
            'amount' => $request->amount,
            'start_date' => $request->start_at,
            'end_date' => $request->end_at,
            'active' => 1,
            'approved' => 0,
        ]);

        // if(!$bill){
        //     DB::rollBack();
        // }
        foreach($request->pay_by as $pay){
            bill_usercategory::create([
                'bill_id' => $bill->id,
                'userCategory_id' => intval($pay),
            ]);
        }
        $newBill = new BillResource($bill);
        return $this->success($newBill);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bill $fee)
    {
        //check if fee has been approved first
        if($fee->approved === 1) return $this->error(message:"cant update an approved fill");
        $request->validate([
            'bill' => "string|unique:bills,bill_name,$fee->id",
            'description' => 'string',
            'amount' => 'numeric|between:0,9999999999.99',
            'pay_by.*' => 'numeric',
            'start_at' => ['date', 'date_format:Y-m-d', 'before:end_at'],
            'end_at'   => 'date|date_format:Y-m-d|after:start_at',
        ]);

        DB::beginTransaction();
        $update_fee = $fee->update([
            'bill_name' => isset($request->bill) ? $request->bill : $fee->bill_name ,
            'description' => isset($request->description) ? $request->description : $fee->description ,
            'amount' => isset($request->amount) ? $request->amount : $fee->amount,
            'start_date' => isset($request->start_at) ? $request->start_at : $fee->start_date,
            'end_date' => isset($request->end_at) ? $request->end_at : $fee->end_date,
        ]);
        if(!$update_fee) {
            DB::rollBack();
            return $this->error(message:"update failed");
        }

        //update payby
        if(isset($request->pay_by)){
            bill_usercategory::where('bill_id', $fee->id)->delete();
            foreach ($request->pay_by as $pay) {
                bill_usercategory::create([
                    'bill_id' => $fee->id,
                    'userCategory_id' => intval($pay),
                ]);
            }
        }
        DB::commit();
        return $this->success(message:"Fee updated");
        
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bill $fee)
    {
        //make sure it hasnt been approved
        if($fee->approved === 1) return $this->error(message:"you cannot delete an approved bill");
         // //delete fee from fee catrgories
        $deleteit = bill_usercategory::where($fee->id,'bill_id')->delete();
        $fee->delete();
        return $this->success(message:"fee deleted successfully");
    }
}
