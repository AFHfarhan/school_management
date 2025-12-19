<?php

namespace App\Http\Controllers\Teacher\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Component;

class ComponentController extends Controller
{
    public function index()
    {
        // Separate mandatory components from others
        $mandatoryComponents = Component::where('category', 'mandatory')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $components = Component::where('category', '!=', 'mandatory')
            ->orWhereNull('category')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('teacher.superadmin.managecomponent', compact('components', 'mandatoryComponents'));
    }

    public function getMandatoryComponent($name)
    {
        $component = Component::where('name', $name)
            ->where('category', 'mandatory')
            ->first();

        if ($component) {
            return response()->json([
                'success' => true,
                'component' => $component
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Component not found'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'upload_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'data_raw' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $value = trim((string) $value);
                    if ($value === '') {
                        return; // empty is allowed
                    }

                    // JSON: must decode to array/object
                    if (in_array($value[0], ['{', '['])) {
                        $decoded = json_decode($value, true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            $fail("The $attribute contains invalid JSON: " . json_last_error_msg());
                            return;
                        }
                        if (!is_array($decoded)) {
                            $fail("The $attribute JSON must be an object or array.");
                        }
                        return;
                    }

                    // key=value pairs (comma separated)
                    if (strpos($value, '=') !== false) {
                        $pairs = array_filter(array_map('trim', explode(',', $value)));
                        foreach ($pairs as $pair) {
                            if ($pair === '') {
                                continue;
                            }
                            if (strpos($pair, '=') === false) {
                                $fail("Invalid pair '$pair' — expected format key=value (comma separated).");
                                return;
                            }
                            [$k, $v] = array_map('trim', explode('=', $pair, 2));
                            if ($k === '') {
                                $fail("Empty key in pair '$pair'. Keys must be non-empty.");
                                return;
                            }
                            if (!preg_match('/^[A-Za-z0-9_.-]+$/', $k)) {
                                $fail("Invalid key '$k' in pair '$pair'. Allowed characters: letters, numbers, underscore, dot, hyphen.");
                                return;
                            }
                        }
                        return;
                    }

                    // comma-separated list of values
                    if (strpos($value, ',') !== false) {
                        $items = array_map('trim', explode(',', $value));
                        foreach ($items as $it) {
                            if ($it === '') {
                                $fail("Empty item found in comma-separated list for $attribute.");
                                return;
                            }
                        }
                        return;
                    }

                    // single value is acceptable
                },
            ],
        ]);

        $dataRaw = $request->input('data_raw') ?? '';
        $data = $this->parseDataString((string) $dataRaw);

        // Handle file upload for mandatory components
        if ($request->hasFile('upload_file')) {
            $file = $request->file('upload_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->move(public_path('global_assets/uploads'), $fileName);
            
            // Store file path in data
            $data['uploads'] = 'global_assets/uploads/' . $fileName;
        }

        $createdBy = Auth::guard('teacher')->id() ?? Auth::id() ?? null;
        $category = $request->input('category');
        
        // For mandatory components, update or create
        if ($category === 'mandatory') {
            $component = Component::updateOrCreate(
                [
                    'name' => $request->input('name'),
                    'category' => 'mandatory'
                ],
                [
                    'data' => $data,
                    'created_by' => $createdBy,
                ]
            );
            
            return redirect()->route('v1.component.manage')->with('success_mandatory', 'Mandatory component saved successfully');
        }

        Component::create([
            'name' => $request->input('name'),
            'category' => $category,
            'data' => $data,
            'created_by' => $createdBy,
        ]);

        return redirect()->route('v1.component.manage')->with('success', 'Component added');
    }

    public function edit(Component $component)
    {
        // Prepare data display string, excluding 'uploads' key for mandatory components
        $dataDisplay = '';
        if ($component && $component->data) {
            $dataToShow = $component->data;
            
            // For mandatory components, exclude the uploads key from display
            if ($component->category === 'mandatory' && is_array($dataToShow) && isset($dataToShow['uploads'])) {
                $dataToShow = array_filter($dataToShow, function($key) {
                    return $key !== 'uploads';
                }, ARRAY_FILTER_USE_KEY);
            }
            
            if (is_array($dataToShow)) {
                // Always encode arrays as JSON to preserve structure
                if (!empty($dataToShow)) {
                    $dataDisplay = json_encode($dataToShow, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                }
            } else {
                $dataDisplay = (string) $dataToShow;
            }
        }
        
        return view('teacher.superadmin.editsuperadmin', compact('component', 'dataDisplay'));
    }

    /**
     * Helper to convert data array back to string format for display in textarea
     */
    protected function dataToString($data): string
    {
        if (empty($data)) {
            return '';
        }

        if (is_array($data)) {
            // Check if it's a sequential array (list)
            if (array_keys($data) === range(0, count($data) - 1)) {
                return implode(',', $data);
            }
            // Associative array - return as JSON
            return json_encode($data);
        }

        return (string) $data;
    }

    public function update(Request $request, Component $component)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'upload_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'data_raw' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    $value = trim((string) $value);
                    if ($value === '') {
                        return; // empty allowed
                    }

                    if (in_array($value[0], ['{', '['])) {
                        $decoded = json_decode($value, true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            $fail("The $attribute contains invalid JSON: " . json_last_error_msg());
                            return;
                        }
                        if (!is_array($decoded)) {
                            $fail("The $attribute JSON must be an object or array.");
                        }
                        return;
                    }

                    if (strpos($value, '=') !== false) {
                        $pairs = array_filter(array_map('trim', explode(',', $value)));
                        foreach ($pairs as $pair) {
                            if ($pair === '') {
                                continue;
                            }
                            if (strpos($pair, '=') === false) {
                                $fail("Invalid pair '$pair' — expected format key=value (comma separated).");
                                return;
                            }
                            [$k, $v] = array_map('trim', explode('=', $pair, 2));
                            if ($k === '') {
                                $fail("Empty key in pair '$pair'. Keys must be non-empty.");
                                return;
                            }
                            if (!preg_match('/^[A-Za-z0-9_.-]+$/', $k)) {
                                $fail("Invalid key '$k' in pair '$pair'. Allowed characters: letters, numbers, underscore, dot, hyphen.");
                                return;
                            }
                        }
                        return;
                    }

                    if (strpos($value, ',') !== false) {
                        $items = array_map('trim', explode(',', $value));
                        foreach ($items as $it) {
                            if ($it === '') {
                                $fail("Empty item found in comma-separated list for $attribute.");
                                return;
                            }
                        }
                        return;
                    }

                    // single value is acceptable
                },
            ],
        ]);

        $data = $this->parseDataString((string) ($request->input('data_raw') ?? ''));
        $updatedBy = Auth::guard('teacher')->id() ?? Auth::id() ?? null;

        // Handle file upload for mandatory components
        if ($request->hasFile('upload_file')) {
            $file = $request->file('upload_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('global_assets/uploads'), $fileName);
            $data['uploads'] = 'global_assets/uploads/' . $fileName;
        } else {
            // Preserve existing file path if no new file uploaded
            if (is_array($component->data) && isset($component->data['uploads'])) {
                $data['uploads'] = $component->data['uploads'];
            }
        }

        $component->update([
            'name' => $request->input('name'),
            'category' => $request->input('category'),
            'data' => $data,
            'updated_by' => $updatedBy,
        ]);

        return redirect()->route('v1.component.manage')->with('success', 'Component updated');
    }

    public function destroy(Component $component)
    {
        $component->delete();
        return redirect()->route('v1.component.manage')->with('success', 'Component deleted');
    }

    /**
     * Parse a data string like "a=b,c=d" into associative array ['a'=>'b','c'=>'d']
     */
    protected function parseDataString(string $raw): array
    {
        $raw = trim($raw);
        if ($raw === '') {
            return [];
        }

        // If looks like JSON (object or array), try to decode
        if (($raw[0] === '{') || ($raw[0] === '[')) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            // fall through to fallback parsing
        }

        // If contains '=' assume key=value pairs
        if (strpos($raw, '=') !== false) {
            $result = [];
            $pairs = array_filter(array_map('trim', explode(',', $raw)));
            foreach ($pairs as $pair) {
                if (strpos($pair, '=') !== false) {
                    [$k, $v] = array_map('trim', explode('=', $pair, 2));
                    if ($k !== '') {
                        // accumulate duplicate keys into arrays
                        if (array_key_exists($k, $result)) {
                            if (is_array($result[$k])) {
                                $result[$k][] = $v;
                            } else {
                                $result[$k] = [$result[$k], $v];
                            }
                        } else {
                            $result[$k] = $v;
                        }
                    }
                }
            }
            return $result;
        }

        // If contains commas but no '=' treat as list of strings
        if (strpos($raw, ',') !== false) {
            $items = array_values(array_filter(array_map('trim', explode(',', $raw))));
            return $items;
        }

        // Single value -> return as single-item array
        return [$raw];
    }
}
