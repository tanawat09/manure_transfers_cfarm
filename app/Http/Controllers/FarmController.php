<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use Illuminate\Http\Request;

class FarmController extends Controller
{
    public function index()
    {
        $farms = Farm::withCount('transfers')->orderBy('name')->get();
        return view('farms.index', compact('farms'));
    }

    public function create()
    {
        return view('farms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:farms,name',
        ], [
            'name.required' => 'กรุณากรอกชื่อฟาร์ม',
            'name.unique' => 'ชื่อฟาร์มนี้มีอยู่ในระบบแล้ว',
        ]);

        Farm::create($request->only('name'));

        return redirect()->route('farms.index')->with('success', 'เพิ่มข้อมูลฟาร์มเรียบร้อยแล้ว');
    }

    public function edit(Farm $farm)
    {
        return view('farms.edit', compact('farm'));
    }

    public function update(Request $request, Farm $farm)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:farms,name,' . $farm->id,
        ], [
            'name.required' => 'กรุณากรอกชื่อฟาร์ม',
            'name.unique' => 'ชื่อฟาร์มนี้มีอยู่ในระบบแล้ว',
        ]);

        $farm->update($request->only('name'));

        return redirect()->route('farms.index')->with('success', 'แก้ไขข้อมูลฟาร์มเรียบร้อยแล้ว');
    }

    public function destroy(Farm $farm)
    {
        if ($farm->transfers()->count() > 0) {
            return back()->with('error', 'ไม่สามารถลบฟาร์มนี้ได้เนื่องจากมีข้อมูลการขนย้ายมูลไก่ถูกบันทึกไว้');
        }

        $farm->delete();
        return redirect()->route('farms.index')->with('success', 'ลบข้อมูลฟาร์มเรียบร้อยแล้ว');
    }
}
