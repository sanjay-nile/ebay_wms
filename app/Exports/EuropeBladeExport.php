<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class EuropeBladeExport implements FromView
{
	private $data;
	private $pallet;

    public function __construct($data, $pallet){
        $this->data = $data;
        $this->pallet = $pallet;
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View{
        return view('exports.europeXml', [
	        'data' => $this->data,
	        'pallet' => $this->pallet
	    ]);
    }
}
