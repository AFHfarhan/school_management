<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Student; 

class ProfileController extends Controller
{
    public function index()
    {
        return view('student.index', [
            'students' => Student::all(),
        ]);
    }

    public function create()
    {
        return view('student.create');
    }

    public function store(Request $request){
        // Validate the request
        // dd($request->all());

        $validatedData = $this->validateRequest($request);

        // Debugging statement
        // dd($validatedData);

        // Process subjects as an array
        $data = $validatedData['data'];
        // if (isset($data['other']['subjects'])) {
        //     $data['academic']['subjects'] = explode(',', $data['academic']['subjects']);
        // }

        //merge separated date fields into one
        $data['form_date'] = $validatedData['formgruptanggal'] . '/' . $validatedData['formgrupbulan'] . '/' . $validatedData['formgruptahun'];
        $data['form_reg'] = $validatedData['formgrupReg1'] . '/' . $validatedData['formgrupReg2'] . '/' . $validatedData['formgrupReg3'];
        $data['personal']['birthdate'] = $validatedData['formgrupTTLTanggal'] . '/' . $validatedData['formgrupTTLBulan'] . '/' . $validatedData['formgrupTTLTahun'];

        // dd($data);

        // Save the student with JSON data
        Student::create([
            'name' => $validatedData['name'],
            'data' => $data,
        ]);
    
        return redirect()->route('v1.students.index')->with('success', 'Student added successfully.');
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Student $student)
    {
        return view('student.edit', compact('student'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        // Validate the request
        $validatedData = $this->validateRequest($request);

        // Process subjects as an array
        $data = $validatedData['data'];
        // if (isset($data['academic']['subjects'])) {
        //     $data['academic']['subjects'] = explode(',', $data['academic']['subjects']);
        // }

        //merge separated date fields into one
        $data['form_date'] = $validatedData['formgruptanggal'] . '/' . $validatedData['formgrupbulan'] . '/' . $validatedData['formgruptahun'];
        $data['form_reg'] = $validatedData['formgrupReg1'] . '/' . $validatedData['formgrupReg2'] . '/' . $validatedData['formgrupReg3'];
        $data['personal']['birthdate'] = $validatedData['formgrupTTLTanggal'] . '/' . $validatedData['formgrupTTLBulan'] . '/' . $validatedData['formgrupTTLTahun'];

        dd($request,$data);


        // Update the student record
        $student->update([
            'name' => $validatedData['name'],
            'data' => $data,
        ]);

        return redirect()->route('v1.students.index')->with('success', 'Student updated successfully.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Student $student): RedirectResponse
    {
        $student->delete(); // Soft delete
        return redirect()->route('v1.students.index')->with('success', 'Student deleted successfully.');
    }

    /**
     * Validate the request.
     */
    private function validateRequest(Request $request): array{

        // dd($request->all());

        return $request->validate([
            'name' => 'required|string|max:255',
            'formgruptanggal' => 'required|string',
            'formgrupbulan' => 'required|string',
            'formgruptahun' => 'required|string',
            'formgrupReg1' => 'required|string',
            'formgrupReg2' => 'required|string',
            'formgrupReg3' => 'required|string',
            'formgrupTTLTanggal' => 'required|string',
            'formgrupTTLBulan' => 'required|string',
            'formgrupTTLTahun' => 'required|string',
            'data.grade' => 'required|string',
            'data.program' => 'required|string',
            'data.personal.birthplace' => 'required|string',
            'data.personal.gender' => 'required|string',
            'data.personal.religion' => 'required|string',
            'data.personal.address' => 'required|string',
            'data.personal.phone' => 'required|string',
            'data.personal.email' => 'required|email',
            'data.personal.kks_no' => 'nullable|string',
            'data.personal.kps' => 'nullable|string|in:ya,tidak',
            'data.personal.kps_no' => 'nullable|string',
            'data.personal.kip' => 'nullable|string|in:ya,tidak',
            'data.personal.kip_no' => 'nullable|string',
            'data.personal.kip_name' => 'nullable|string',
            'data.personal.school_prev' => 'nullable|string',
            'data.personal.homephone' => 'nullable|string',
            'data.parent.dad.name' => 'nullable|string',
            'data.parent.dad.birthdate' => 'nullable|string',
            'data.parent.dad.education' => 'nullable|string',
            'data.parent.dad.job' => 'nullable|string',
            'data.parent.dad.salary' => 'nullable|string',
            'data.parent.mom.name' => 'nullable|string',
            'data.parent.mom.birthdate' => 'nullable|string',
            'data.parent.mom.education' => 'nullable|string',
            'data.parent.mom.job' => 'nullable|string',
            'data.parent.mom.salary' => 'nullable|string',
            'data.parent.sub.name' => 'nullable|string',
            'data.parent.sub.birthdate' => 'nullable|string',
            'data.parent.sub.education' => 'nullable|string',
            'data.parent.sub.job' => 'nullable|string',
            'data.parent.sub.salary' => 'nullable|string',
            'data.achievement.type' => 'nullable|string',
            'data.achievement.grade' => 'nullable|string',
            'data.achievement.name' => 'nullable|string',
            'data.achievement.year' => 'nullable|string',
            'data.achievement.credit' => 'nullable|string',
            'data.other.requirements' => 'nullable|array',
            'data.other.reference' => 'nullable|array',
        ]);
    }
}
