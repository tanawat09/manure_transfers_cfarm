<?php

namespace App\Http\Controllers;

use App\Models\ManurePile;
use Illuminate\Http\Request;

class ManurePileController extends Controller
{
    public function index()
    {
        $piles = ManurePile::withCount('transfers')->ordered()->get();
        return view('piles.index', compact('piles'));
    }

    public function create()
    {
        return view('piles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:manure_piles,name',
        ], [
            'name.required' => 'กรุณากรอกชื่อกองมูลไก่',
            'name.unique' => 'ชื่อกองมูลไก่นี้มีอยู่ในระบบแล้ว',
        ]);

        ManurePile::create($request->only('name'));

        return redirect()->route('piles.index')->with('success', 'เพิ่มข้อมูลกองมูลไก่เรียบร้อยแล้ว');
    }

    public function edit(ManurePile $pile)
    {
        return view('piles.edit', compact('pile'));
    }

    public function update(Request $request, ManurePile $pile)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:manure_piles,name,' . $pile->id,
        ], [
            'name.required' => 'กรุณากรอกชื่อกองมูลไก่',
            'name.unique' => 'ชื่อกองมูลไก่นี้มีอยู่ในระบบแล้ว',
        ]);

        $pile->update($request->only('name'));

        return redirect()->route('piles.index')->with('success', 'แก้ไขข้อมูลกองมูลไก่เรียบร้อยแล้ว');
    }

    public function destroy(ManurePile $pile)
    {
        if ($pile->transfers()->count() > 0) {
            return back()->with('error', 'ไม่สามารถลบกองมูลไก่นี้ได้เนื่องจากมีข้อมูลการรับเข้ามูลไก่ถูกบันทึกไว้');
        }

        $pile->delete();
        return redirect()->route('piles.index')->with('success', 'ลบข้อมูลกองมูลไก่เรียบร้อยแล้ว');
    }
}
