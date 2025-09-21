<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'data.personal.birthdate' => 'required|string',
            'data.personal.birthplace' => 'required|string',
            'data.personal.gender' => 'required|string',
            'data.personal.religion' => 'required|string',
            'data.personal.kks_no' => 'nullable|string',
            'data.personal.kps' => 'nullable|boolean',
            'data.personal.kps_no' => 'nullable|string',
            'data.personal.kip' => 'nullable|boolean',
            'data.personal.kip_no' => 'nullable|string',
            'data.personal.kip_name' => 'nullable|string',
            'data.personal.school_prev' => 'nullable|string',
            'data.personal.address' => 'required|string',
            'data.personal.homephone' => 'nullable|string',
            'data.personal.phone' => 'required|string',
            'data.personal.email' => 'required|email',
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
            'data.other.requirement_checklist.form' => 'nullable|boolean',
            'data.other.requirement_checklist.kk' => 'nullable|boolean',
            'data.other.requirement_checklist.ktp' => 'nullable|boolean',
            'data.other.requirement_checklist.foto' => 'nullable|boolean',
            'data.other.requirement_checklist.reference' => 'nullable|array',
            'data.other.requirement_checklist.reference.*' => 'nullable|string',
        ];
    }
}
