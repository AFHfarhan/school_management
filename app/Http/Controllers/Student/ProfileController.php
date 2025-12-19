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
    protected $logger;

    public function index()
    {
        // Only get students that have form_date in their data field
        $students = Student::all()->filter(function ($student) {
            $data = is_array($student->data) ? $student->data : json_decode($student->data, true);
            return isset($data['form_date']);
        });

        return view('student.index', [
            'students' => $students,
        ]);
    }

    public function create()
    {
        return view('student.create');
    }

    /**
     * Display the specified student's full details (read-only).
     */
    public function show(Student $student): View
    {
        $this->authorize('view', $student);

        $data = $student->data ?? [];
        return view('student.show', compact('student', 'data'));
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
        $data['form_reg'] = $validatedData['formgrupReg1'] . '/' . $validatedData['formgrupReg2'] . '/' . $validatedData['formgrupReg3'];
        
        // dd($data);

        // Save the student with JSON data
        Student::create([
            'name' => $validatedData['name'],
            'data' => $data,
        ]);

    // $this->logger->info('student.created', ['ip' => $request->ip(), 
    // 'user_id' => Auth::guard('teacher')->id(),
    //  'message' => 'New student created: ' . $validatedData['name'], 
    //  'context' => $data]);
    
        return redirect()->route('v1.students.index')->with('success', 'Student added successfully.');
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Student $student)
    {
        $this->authorize('update', $student);

        $data = $student->data ?? [];
        return view('student.edit', compact('student','data'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        // Validate the request
        $this->authorize('update', $student);

        $validatedData = $this->validateRequest($request);

        // Process subjects as an array
        $data = $validatedData['data'];
        // if (isset($data['academic']['subjects'])) {
        //     $data['academic']['subjects'] = explode(',', $data['academic']['subjects']);
        // }

        //merge separated date fields into one
        $data['form_reg'] = $validatedData['formgrupReg1'] . '/' . $validatedData['formgrupReg2'] . '/' . $validatedData['formgrupReg3'];

        // dd($request,$data);



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
        $this->authorize('delete', $student);

        $student->delete(); // Soft delete
        return redirect()->route('v1.students.index')->with('success', 'Student deleted successfully.');
    }

    /**
     * Validate the request.
     */
    private function validateRequest(Request $request): array{

        // dd($request->all());

        return $request->validate([
            'formgrupReg1' => 'required|string',
            'formgrupReg2' => 'required|string',
            'formgrupReg3' => 'required|string',
            'name' => 'required|string|max:255',
            'data.form_date' => 'required|string',
            'data.grade' => 'required|string',
            'data.program' => 'required|string',
            'data.personal.birthplace' => 'required|string',
            'data.personal.birthdate' => 'required|string',
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
            'data.parent.is_sub_active' => 'nullable|string',
            'data.achievement' => 'nullable|array',
            'data.achievement.*.type' => 'nullable|string',
            'data.achievement.*.grade' => 'nullable|string',
            'data.achievement.*.name' => 'nullable|string',
            'data.achievement.*.year' => 'nullable|string',
            'data.achievement.*.credit' => 'nullable|string',
            'data.other.requirements' => 'nullable|array',
            'data.other.reference' => 'nullable|array',
            'data.other.reference_guru' => 'nullable|string',
            'data.other.reference_teman' => 'nullable|string',
            'data.other.reference_lainnya' => 'nullable|string',
        ]);
    }
}
