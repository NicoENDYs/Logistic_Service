<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DeliveriesExport;
use App\Models\Delivery;

class reportsController extends Controller
{
    public function exportExcel(Request $request)
    {
        $status=$request->input('status');
        return Excel::download(new DeliveriesExport($status), 'Deliveries.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $status= $request->input('status');
        $deliveries= (new DeliveriesExport($status))->collection();
        $pdf =Pdf::loadView('reports.deliveries', ['deliveries'=>$deliveries]);

        return $pdf->download('deliveries.pdf');

    }

     public function kpis()
    {
        $total = Delivery::count();
        $deliveried = Delivery::where('status','entregado')->count();
        $pending = Delivery::where('status', 'pendiente')->count();
        $failed = Delivery::where('status', 'fallido')->count();

        $porcentaje= $total > 0 ? round(($deliveried / $total)*100, 2): 0;

        return view('reports.kpis', compact('total','deliveried','pending','failed','porcentaje'));

    }
}
