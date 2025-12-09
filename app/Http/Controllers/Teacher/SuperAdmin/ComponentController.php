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
        $components = Component::orderBy('created_at', 'desc')->get();
        return view('teacher.superadmin.managecomponent', compact('components'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
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

        $dataRaw = $request->input('data_raw', '');
        $data = $this->parseDataString($dataRaw);

        $createdBy = Auth::guard('teacher')->id() ?? Auth::id() ?? null;

        Component::create([
            'name' => $request->input('name'),
            'category' => $request->input('category'),
            'data' => $data,
            'created_by' => $createdBy,
        ]);

        return redirect()->route('v1.component.manage')->with('success', 'Component added');
    }

    public function edit(Component $component)
    {
        return view('teacher.superadmin.editsuperadmin', compact('component'));
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

        $data = $this->parseDataString($request->input('data_raw', ''));
        $updatedBy = Auth::guard('teacher')->id() ?? Auth::id() ?? null;

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
