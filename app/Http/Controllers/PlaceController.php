<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;


class PlaceController extends Controller
{
    public function create()
    {
        return view('admin.increase-admin');
    }

    public function store(Request $request)
    {
        $request->validate([
            'place_name' => 'required',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        Place::create([
            'place_name' => $request->place_name,
            'latitude' => $request->lat,
            'longitude' => $request->lng,
        ]);

        return redirect()->route('admin.cafe.index')->with('success', 'เพิ่มสถานที่เรียบร้อยแล้ว');
    }
}
